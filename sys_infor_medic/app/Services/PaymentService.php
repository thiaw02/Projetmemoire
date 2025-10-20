<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    public function createCheckout(Order $order, string $provider): Order
    {
        $provider = strtolower($provider);
        if ($provider === 'wave') {
            return $this->createWave($order);
        }
        if (in_array($provider, ['orangemoney','orange_money','om'])) {
            return $this->createOrangeMoney($order);
        }
        if (in_array($provider, ['dexchange','dex'])) {
            return $this->createDexchange($order);
        }
        throw new \InvalidArgumentException('Fournisseur paiement inconnu');
    }

    protected function createWave(Order $order): Order
    {
        // TODO: Implémentation Wave réelle
        throw new \RuntimeException('Intégration Wave non configurée (manque API).');
    }

    protected function createOrangeMoney(Order $order): Order
    {
        // TODO: Implémentation Orange Money réelle (signature HMAC)
        throw new \RuntimeException('Intégration Orange Money non configurée (manque API).');
    }

    protected function createDexchange(Order $order): Order
    {
        $cfg = config('services.dexchange');
        if (!$cfg || empty($cfg['api_key']) || empty($cfg['merchant_id'])) {
            throw new \RuntimeException('Configuration Dexchange incomplète.');
        }
        $baseUrl = rtrim($cfg['base_url'] ?? 'https://api-m.dexchange.sn/v1', '/');

        $callbackUrl = route('webhooks.dexchange');
        $successUrl = route('payments.success', ['order' => $order->id]);
        $cancelUrl = route('payments.cancel', ['order' => $order->id]);

        $payload = [
            'merchant_id' => $cfg['merchant_id'],
            'amount' => (int) $order->total_amount,
            'currency' => $order->currency ?: 'XOF',
            'reference' => 'ord_'.$order->id.'_'.Str::random(6),
            'description' => 'Paiement commande #'.$order->id,
            'callback_url' => $callbackUrl,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ],
        ];

        // Per official docs: POST /merchant/get-link returns { success, message, data: { payment_link, transactionId, ... } }
        $response = Http::withToken($cfg['api_key'])
            ->acceptJson()
            ->post($baseUrl.'/merchant/get-link', $payload);

        if (!$response->ok()) {
            \Log::error('Dexchange create checkout failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Impossible de créer la session Dexchange');
        }

        $json = $response->json();
        $data = is_array($json) ? ($json['data'] ?? []) : [];
        $order->provider = 'dexchange';
        $order->provider_ref = $data['transactionId'] ?? ($data['id'] ?? $data['reference'] ?? null);
        $order->payment_url = $data['payment_link'] ?? ($data['checkout_url'] ?? $data['payment_url'] ?? null);
        if (!$order->payment_url) {
            throw new \RuntimeException('Réponse Dexchange invalide (pas d\'URL de paiement).');
        }
        $order->save();
        return $order;
    }
}
