<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Services\PayDunyaService;

class AdminPaymentsController extends Controller
{
    public function index()
    {
        $stats = [
            'total_amount' => (int) Order::where('status', 'paid')->sum('total_amount'),
            'today_payments' => (int) Order::whereDate('created_at', now()->toDateString())->count(),
            'tarifs_configured' => 2,
            'active_secretaires' => (int) User::where('role','secretaire')->where('active', true)->count(),
            'today_amount' => (int) Order::where('status','paid')->whereDate('paid_at', now()->toDateString())->sum('total_amount'),
            'month_amount' => (int) Order::where('status','paid')->whereYear('paid_at', now()->year)->whereMonth('paid_at', now()->month)->sum('total_amount'),
        ];
        $tarifs = [
            'consultation' => (int) Setting::getValue('price.consultation', 5000),
            'analyse' => (int) Setting::getValue('price.analyse', 10000),
        ];
        $secretaires = User::where('role','secretaire')->orderBy('name')->get(['id','name','active']);
        $recent_activities = [];
        return view('admin.payments.index', compact('stats','tarifs','secretaires','recent_activities'));
    }

    public function settings()
    {
        $settings = [
            'consultation_price' => (int) Setting::getValue('price.consultation', 5000),
            'analyse_price' => (int) Setting::getValue('price.analyse', 10000),
            'urgence_price' => (int) Setting::getValue('price.urgence', 8000),
            'specialty_price' => (int) Setting::getValue('price.specialty', 12000),
            'default_currency' => (string) Setting::getValue('currency', 'XOF'),
            'tax_rate' => (float) Setting::getValue('tax_rate', 18),
            'payment_methods' => (array) (Setting::getValue('payment_methods', ['cash']) ?? ['cash']),
            'invoice_prefix' => (string) Setting::getValue('invoice_prefix', 'FAC'),
            'invoice_counter' => (int) Setting::getValue('invoice_counter', 1),
            'require_validation' => (bool) Setting::getValue('require_validation', false),
        ];
        $last_backup = null;
        return view('admin.payments.settings', compact('settings','last_backup'));
    }

    public function reports()
    {
        $financial_kpis = [];
        $transactions = [];
        $top_doctors_revenue = [];
        $performance_metrics = [];
        $doctors_performance = [];
        $services_total = 0;
        return view('admin.payments.reports', compact(
            'financial_kpis','transactions','top_doctors_revenue','performance_metrics','doctors_performance','services_total'
        ));
    }

    public function perCredit(Request $request, PayDunyaService $pay)
    {
        $data = $request->validate([
            'recipient' => ['required','string','max:255'], // email ou numéro mobile PayDunya
            'amount' => ['required','integer','min:100'],
        ]);
        try {
            $res = $pay->perCreditAccount($data['recipient'], (int)$data['amount']);
            if (!$res['success']) {
                return back()->with('error', $res['error'] ?? 'Échec de la redistribution PER.');
            }
            return back()->with('success', 'Redistribution PER effectuée. Transaction: '.($res['transaction_id'] ?? 'N/A'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Erreur PER: '.$e->getMessage());
        }
    }

    public function markOrderPaid(int $orderId)
    {
        $order = Order::findOrFail($orderId);
        if ($order->status !== 'paid') {
            $order->status = 'paid';
            $order->paid_at = now();
            $meta = $order->metadata ?? [];
            $meta['marked_paid_by'] = auth()->id();
            $order->metadata = $meta;
            $order->save();
        }
        return back()->with('success', 'Paiement encaissé (cash) et marqué comme payé.');
    }
}
