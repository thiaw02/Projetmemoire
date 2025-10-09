<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    private static function generateTicketNumber(string $kind): string
    {
        $prefix = strtoupper(substr($kind,0,1));
        $date = now()->format('Ymd');
        $rand = strtoupper(substr(bin2hex(random_bytes(3)),0,6));
        return 'TKT-'.$prefix.'-'.$date.'-'.$rand;
    }

    private function authorizeReceipt(Order $order): void
    {
        $u = auth()->user();
        if (!$u) abort(401);
        if ($order->user_id === $u->id) return; // propriétaire
        if (in_array($u->role, ['admin','secretaire'])) return;
        abort(403);
    }
    public function patientIndex(Request $request)
    {
        $user = Auth::user();
        $orders = Order::with('items')->where('user_id', $user->id)->orderByDesc('created_at')->get();
        $priceConsult = (int) (Setting::getValue('price.consultation', 5000));
        $priceAnalyse = (int) (Setting::getValue('price.analyse', 10000));
        $priceActe = (int) (Setting::getValue('price.acte', 7000));
        $consultationsList = \App\Models\Consultations::where('patient_id', optional($user->patient)->id)->orderByDesc('date_consultation')->take(20)->get(['id','date_consultation']);
        $analysesList = \App\Models\Analyses::where('patient_id', optional($user->patient)->id)->orderByDesc('date_analyse')->take(20)->get(['id','type_analyse','date_analyse']);
        return view('patient.paiements', compact('orders','priceConsult','priceAnalyse','priceActe','consultationsList','analysesList'));
    }

    public function checkout(Request $request, PaymentService $svc)
    {
        $user = Auth::user();
        $data = $request->validate([
            'provider' => 'required|in:wave,orangemoney',
            'kind' => 'required|in:consultation,analyse,acte',
            'amount' => 'required|integer|min:100',
            'label' => 'nullable|string|max:255',
            'ref_id' => 'nullable|string', // format: "type:id"
        ]);

        $amount = (int)$data['amount'];
        $label = $data['label'] ?: ($data['kind'] === 'consultation' ? 'Ticket de consultation' : ucfirst($data['kind']));

        return DB::transaction(function () use ($user, $data, $amount, $label, $svc) {
            $order = Order::create([
                'user_id' => $user->id,
                'patient_id' => optional($user->patient)->id,
                'currency' => 'XOF',
                'total_amount' => $amount,
                'status' => 'pending',
                'provider' => $data['provider'],
            ]);
            // Parse ref
            $itemId = null;
            if (!empty($data['ref_id']) && str_contains($data['ref_id'], ':')) {
                [$rt, $rid] = explode(':', $data['ref_id'], 2);
                if ($rt === $data['kind']) { $itemId = (int)$rid; }
            }
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'item_type' => $data['kind'],
                'item_id' => $itemId,
                'label' => $label,
                'amount' => $amount,
                'ticket_number' => self::generateTicketNumber($data['kind']),
            ]);

            $order = $svc->createCheckout($order, $data['provider']);

            if (!$order->payment_url) {
                return back()->withErrors(['payment' => 'Impossible de créer la session de paiement.']);
            }
            return redirect()->away($order->payment_url);
        });
    }

    public function sandbox(int $order)
    {
        $order = Order::with('items')->findOrFail($order);
        if ($order->status !== 'pending') {
            return redirect()->route('patient.payments.index')->with('success', 'Commande déjà traitée.');
        }
        return view('payments.sandbox', compact('order'));
    }

    private function sendReceiptIfNotSent(Order $order): void
    {
        $meta = $order->metadata ?? [];
        if (!empty($meta['receipt_sent'])) return;
        try {
            if ($order->user && $order->status === 'paid') {
                $order->user->notify(new \App\Notifications\PaymentReceiptNotification($order));
                $meta['receipt_sent'] = true;
                $order->metadata = $meta;
                $order->save();
            }
        } catch (\Throwable $e) {}
    }

    public function success(Request $request)
    {
        $orderId = (int) $request->query('order');
        $order = Order::findOrFail($orderId);
        if ($order->status !== 'paid') {
            $order->status = 'paid';
            $order->paid_at = now();
            $order->save();
        }
        $this->sendReceiptIfNotSent($order);
        return redirect()->route('patient.payments.index')->with('success', 'Paiement confirmé. Merci.');
    }

    public function cancel(Request $request)
    {
        $orderId = (int) $request->query('order');
        $order = Order::findOrFail($orderId);
        if ($order->status === 'pending') {
            $order->status = 'canceled';
            $order->save();
        }
        return redirect()->route('patient.payments.index')->with('success', 'Paiement annulé.');
    }

    public function webhookWave(Request $request)
    {
        $ref = $request->input('reference') ?: $request->input('id');
        if (!$ref) { return response()->json(['error' => 'no ref'], 400); }
        $order = Order::where('provider_ref', $ref)->first();
        if (!$order) { return response()->json(['ok' => true]); }
        $status = $request->input('status');
        if ($status === 'success' || $status === 'paid') {
            $order->status = 'paid';
            $order->paid_at = now();
        } elseif ($status === 'failed') {
            $order->status = 'failed';
        } elseif ($status === 'canceled') {
            $order->status = 'canceled';
        }
        $order->save();
        if ($order->status === 'paid') { $this->sendReceiptIfNotSent($order); }
        return response()->json(['ok' => true]);
    }

    public function receipt(int $orderId)
    {
        $order = Order::with('items','user')->findOrFail($orderId);
        $this->authorizeReceipt($order);
        $data = [
            'order' => $order,
            'hospital' => [
                'name' => config('app.name', 'SMART-HEALTH'),
                'address' => Setting::getValue('hospital.address', 'Adresse'),
                'phone' => Setting::getValue('hospital.phone', ''),
            ],
            'generatedAt' => now(),
        ];
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payments.receipt', $data);
            $filename = 'Quittance_'.$order->id.'.pdf';
            return $pdf->download($filename);
        }
        $html = view('payments.receipt', $data)->render();
        return response($html)->header('Content-Type','text/html');
    }

    public function webhookOrangeMoney(Request $request)
    {
        $ref = $request->input('order_id') ?: $request->input('merchant_reference');
        if (!$ref) { return response()->json(['error' => 'no ref'], 400); }
        $order = Order::where('provider_ref', $ref)->first();
        if (!$order) { return response()->json(['ok' => true]); }
        $status = strtolower((string)$request->input('status'));
        if (in_array($status, ['paid','success','completed'])) {
            $order->status = 'paid';
            $order->paid_at = now();
        } elseif (in_array($status, ['failed','error'])) {
            $order->status = 'failed';
        } elseif (in_array($status, ['canceled','cancelled'])) {
            $order->status = 'canceled';
        }
        $order->save();
        if ($order->status === 'paid') { $this->sendReceiptIfNotSent($order); }
        return response()->json(['ok' => true]);
    }
}
