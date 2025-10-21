<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;
use App\Services\PayDunyaService;

class PaymentService
{
    public function createCheckout(Order $order, string $provider): Order
    {
        $provider = strtolower($provider);
        
        // Mode sandbox activé pour les tests
        if (config('app.env') !== 'production' && env('PAYMENTS_SANDBOX', true)) {
            // Mode sandbox: page locale pour simuler succès/annulation
            $order->provider = $provider;
            $order->provider_ref = 'sandbox_'.Str::random(10);
            $order->payment_url = route('payments.sandbox', ['order' => $order->id]);
            $order->save();
            return $order;
        }
        
        if (in_array($provider, ['paydunya','payd'])) {
            return $this->createPayDunya($order);
        }
        throw new \InvalidArgumentException('Fournisseur paiement inconnu. Seul PayDunya est supporté.');
    }

    protected function createPayDunya(Order $order): Order
    {
        $paydunyaService = new PayDunyaService();
        $result = $paydunyaService->createCheckout($order);
        
        if (!$result['success']) {
            throw new \RuntimeException($result['error']);
        }
        
        $order->provider = 'paydunya';
        $order->provider_ref = $result['token'];
        $order->payment_url = $result['payment_url'];
        $order->save();
        
        return $order;
    }
}
