<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class PaymentService
{
    public function createCheckout(Order $order, string $provider): Order
    {
        $provider = strtolower($provider);
        if (config('app.env') !== 'production' && env('PAYMENTS_SANDBOX', true)) {
            // Mode sandbox: page locale pour simuler succès/annulation
            $order->provider = $provider;
            $order->provider_ref = 'sandbox_'.Str::random(10);
            $order->payment_url = route('payments.sandbox', ['order' => $order->id]);
            $order->save();
            return $order;
        }
        if ($provider === 'wave') {
            return $this->createWave($order);
        }
        if (in_array($provider, ['orangemoney','orange_money','om'])) {
            return $this->createOrangeMoney($order);
        }
        throw new \InvalidArgumentException('Fournisseur paiement inconnu');
    }

    protected function createWave(Order $order): Order
    {
        // TODO: Implémenter l’appel API Wave (checkout link)
        throw new \RuntimeException('Intégration Wave non configurée (manque API). Activez le sandbox pour tester.');
    }

    protected function createOrangeMoney(Order $order): Order
    {
        // TODO: Implémenter l’appel API Orange Money Web Payment (signature HMAC)
        throw new \RuntimeException('Intégration Orange Money non configurée (manque API). Activez le sandbox pour tester.');
    }
}
