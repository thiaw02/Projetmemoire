<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\PaymentService;
use App\Services\PayDunyaService;

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
        
        // Récupérer les rendez-vous confirmés (patients peuvent payer pour ces rdv)
        $confirmedAppointments = \App\Models\Rendez_vous::with(['medecin:id,name'])
            ->where('user_id', $user->id)
            ->where('statut', 'confirmé')
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('heure')
            ->get(['id', 'medecin_id', 'date', 'heure', 'motif', 'statut']);
        
        return view('patient.paiements', compact('orders','priceConsult','priceAnalyse','priceActe','consultationsList','analysesList','confirmedAppointments'));
    }

    public function checkout(Request $request, PaymentService $svc)
    {
        $user = Auth::user();
        
        try {
            $data = $request->validate([
                'provider' => 'required|in:paydunya',
                'kind' => 'required|in:consultation,analyse,acte,rendezvous',
                'amount' => 'required|integer|min:100',
                'label' => 'nullable|string|max:255',
                'ref_id' => 'nullable|string', // format: "type:id"
            ], [
                'provider.required' => 'Veuillez sélectionner une méthode de paiement.',
                'provider.in' => 'Méthode de paiement non valide.',
                'kind.required' => 'Veuillez sélectionner un type de service.',
                'kind.in' => 'Type de service non valide.',
                'amount.required' => 'Le montant est requis.',
                'amount.integer' => 'Le montant doit être un nombre entier.',
                'amount.min' => 'Le montant minimum est de 100 XOF.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $amount = (int)$data['amount'];
        $label = $data['label'] ?: ($data['kind'] === 'consultation' ? 'Ticket de consultation' : ucfirst($data['kind']));

        try {
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
                    return back()->withErrors(['payment' => 'Impossible de créer la session de paiement. Veuillez réessayer.']);
                }
                return redirect()->away($order->payment_url);
            });
        } catch (\Exception $e) {
            \Log::error('Erreur lors du checkout de paiement', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data ?? []
            ]);
            return back()->withErrors(['payment' => 'Une erreur s\'est produite lors du traitement de votre paiement. Veuillez réessayer.'])->withInput();
        }
    }

    // Sandbox removed for production

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

    public function webhookPayDunya(Request $request)
    {
        $paydunyaService = new PayDunyaService();
        
        if (!$paydunyaService->verifyWebhook($request->all())) {
            \Log::warning('PayDunya webhook received with invalid signature.', ['request_body' => $request->all()]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->input('data', []);
        $status = strtolower($data['status'] ?? '');
        $orderId = $data['invoice']['custom_data']['order_id'] ?? null;

        if (!$orderId) {
            \Log::info('PayDunya webhook: Order ID not found in custom data', ['data' => $data]);
            return response()->json(['ok' => true]);
        }

        $order = Order::find($orderId);
        if (!$order) {
            \Log::info('PayDunya webhook: Order not found', ['order_id' => $orderId]);
            return response()->json(['ok' => true]);
        }

        if ($status === 'completed') {
            $order->status = 'paid';
            $order->paid_at = now();
        } elseif (in_array($status, ['failed', 'cancelled'])) {
            $order->status = $status === 'cancelled' ? 'canceled' : 'failed';
        }
        $order->save();

        if ($order->status === 'paid') {
            $this->sendReceiptIfNotSent($order);
        }

        return response()->json(['ok' => true]);
    }

    public function verifyPayDunya(Request $request)
    {
        $token = $request->get('token');
        if (!$token) {
            return redirect()->route('patient.payments.index')->with('error', 'Token de paiement manquant.');
        }

        $paydunyaService = new PayDunyaService();
        $result = $paydunyaService->verifyPayment($token);

        if (!$result['success']) {
            return redirect()->route('patient.payments.index')->with('error', $result['error']);
        }

        $orderId = $result['order_id'];
        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('patient.payments.index')->with('error', 'Commande introuvable.');
        }

        if ($result['status'] === 'completed') {
            $order->status = 'paid';
            $order->paid_at = now();
            $order->save();
            
            $this->sendReceiptIfNotSent($order);
            
            return redirect()->route('payments.success', ['order' => $order->id])
                ->with('success', 'Paiement effectué avec succès!');
        } elseif ($result['status'] === 'cancelled') {
            $order->status = 'canceled';
            $order->save();
            
            return redirect()->route('payments.cancel', ['order' => $order->id])
                ->with('error', 'Paiement annulé.');
        } else {
            return redirect()->route('patient.payments.index')
                ->with('warning', 'Paiement en attente de confirmation.');
        }
    }

    public function sandbox(Request $request, int $orderId)
    {
        $order = Order::findOrFail($orderId);
        return view('payments.sandbox_standalone', compact('order'));
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
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payments.receipt', $data);
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => false,
                    'isRemoteEnabled' => false,
                    'defaultFont' => 'Arial',
                    'dpi' => 150,
                ]);
                $filename = 'Quittance_'.$order->id.'.pdf';
                return $pdf->download($filename);
            } catch (\Exception $e) {
                \Log::error('Erreur génération PDF reçu: ' . $e->getMessage(), [
                    'order_id' => $orderId,
                    'trace' => $e->getTraceAsString()
                ]);
                // Fallback vers HTML en cas d'erreur PDF
            }
        }
        
        $html = view('payments.receipt', $data)->render();
        return response($html)->header('Content-Type','text/html');
    }

}
