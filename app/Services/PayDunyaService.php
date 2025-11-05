<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class PayDunyaService
{
    protected function getConfig(): array
    {
        $cfg = config('services.paydunya');
        if (!$cfg || empty($cfg['master_key']) || empty($cfg['public_key']) || empty($cfg['private_key']) || empty($cfg['token'])) {
            throw new \RuntimeException('Configuration PayDunya incomplète.');
        }
        return $cfg;
    }

    protected function baseUrl(string $mode): string
    {
        $mode = strtolower($mode ?: 'test');
        return $mode === 'live'
            ? 'https://app.paydunya.com/api/v1'
            : 'https://app.paydunya.com/sandbox-api/v1';
    }

    protected function authHeaders(array $cfg): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'PAYDUNYA-MASTER-KEY' => $cfg['master_key'],
            'PAYDUNYA-PRIVATE-KEY' => $cfg['private_key'],
            'PAYDUNYA-PUBLIC-KEY' => $cfg['public_key'],
            'PAYDUNYA-TOKEN' => $cfg['token'],
        ];
    }

    public function createCheckout(Order $order): array
    {
        $cfg = $this->getConfig();
        $base = $this->baseUrl($cfg['mode'] ?? 'test');

        $items = [];
        foreach ($order->items as $it) {
            $items[] = [
                'name' => (string)($it->label ?? 'Article'),
                'quantity' => 1,
                'unit_price' => (int)$it->amount,
                'total_price' => (int)$it->amount,
            ];
        }

        $payload = [
            'invoice' => [
                'items' => $items,
                'total_amount' => (int)$order->total_amount,
                'description' => 'Paiement commande #'.$order->id,
                'custom_data' => [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ],
                'callback_url' => route('webhooks.paydunya'),
                'cancel_url' => route('payments.cancel'),
                'return_url' => route('payments.paydunya.verify'),
            ],
            'store' => [
                'name' => $cfg['store_name'] ?? config('app.name', 'SMART-HEALTH'),
                'tagline' => $cfg['store_tagline'] ?? 'Système de gestion médicale',
                'phone' => $cfg['store_phone'] ?? '',
                'postal_address' => $cfg['store_address'] ?? '',
                'website_url' => $cfg['store_website'] ?? config('app.url'),
                'logo_url' => $cfg['store_logo'] ?? (rtrim(config('app.url'),'/').'/logo.png'),
            ],
        ];

        $res = Http::withHeaders($this->authHeaders($cfg))
            ->post($base.'/checkout-invoice/create', $payload);

        if (!$res->ok()) {
            return ['success' => false, 'error' => 'HTTP '.$res->status().' - '.$res->body()];
        }

        $json = $res->json();
        // Tentatives robustes de lecture des champs de réponse
        $body = $json['response_body'] ?? $json['data'] ?? $json;
        $token = $body['token'] ?? $body['invoice_token'] ?? $json['token'] ?? null;
        $url = $body['checkout_url'] ?? $body['invoice_url'] ?? $body['redirect_url'] ?? $json['redirect_url'] ?? null;

        if ($token && $url) {
            return ['success' => true, 'payment_url' => $url, 'token' => $token];
        }

        return ['success' => false, 'error' => 'Réponse PayDunya inattendue', 'raw' => $json];
    }

    public function verifyWebhook(array $data): bool
    {
        $cfg = $this->getConfig();
        // Méthode simple: vérifier hash basé sur master_key si fourni par PayDunya
        $receivedHash = $data['data']['hash'] ?? $data['hash'] ?? null;
        if (!$receivedHash) { return false; }
        $expected = hash('sha512', (string)$cfg['master_key']);
        return hash_equals($expected, (string)$receivedHash);
    }

    public function verifyPayment(string $token): array
    {
        $cfg = $this->getConfig();
        $base = $this->baseUrl($cfg['mode'] ?? 'test');

        $res = Http::withHeaders($this->authHeaders($cfg))
            ->get($base.'/checkout-invoice/confirm', [ 'token' => $token ]);

        if (!$res->ok()) {
            return ['success' => false, 'error' => 'HTTP '.$res->status().' - '.$res->body()];
        }

        $json = $res->json();
        $body = $json['response_body'] ?? $json['data'] ?? $json;
        $status = strtolower((string)($body['status'] ?? ''));

        if (in_array($status, ['completed','completed_success','completed-success','paid','success'])) {
            return [
                'success' => true,
                'status' => $status,
                'order_id' => $body['custom_data']['order_id'] ?? null,
                'customer_name' => $body['customer']['name'] ?? null,
                'customer_email' => $body['customer']['email'] ?? null,
                'customer_phone' => $body['customer']['phone'] ?? null,
                'receipt_url' => $body['receipt_url'] ?? null,
            ];
        }

        return [
            'success' => false,
            'status' => $status,
            'error' => $body['response_text'] ?? $json['response_text'] ?? 'Paiement non confirmé',
        ];
    }

    /**
     * Configure le SDK PHP PayDunya si disponible (composer require paydunya/paydunya)
     */
    protected function setupSdk(): void
    {
        if (!class_exists('Paydunya\\Setup') && !class_exists('\\Paydunya\\Setup')) {
            throw new \RuntimeException('SDK PayDunya introuvable. Installez-le: composer require paydunya/paydunya');
        }
        $cfg = $this->getConfig();
        // Espace de noms du SDK selon version
        if (class_exists('\\Paydunya\\Setup')) {
            \Paydunya\Setup::setMasterKey($cfg['master_key']);
            \Paydunya\Setup::setPublicKey($cfg['public_key']);
            \Paydunya\Setup::setPrivateKey($cfg['private_key']);
            \Paydunya\Setup::setToken($cfg['token']);
            \Paydunya\Setup::setMode($cfg['mode'] ?? 'test');
        } else {
            \Paydunya_Setup::setMasterKey($cfg['master_key']);
            \Paydunya_Setup::setPublicKey($cfg['public_key']);
            \Paydunya_Setup::setPrivateKey($cfg['private_key']);
            \Paydunya_Setup::setToken($cfg['token']);
            \Paydunya_Setup::setMode($cfg['mode'] ?? 'test');
        }
    }

    /**
     * PER: Créditer un compte PayDunya (email ou numéro mobile) d'un montant donné.
     * Retourne [success=>bool, transaction_id?, description?, response_text?, error?]
     */
    public function perCreditAccount(string $recipient, int $amount): array
    {
        $this->setupSdk();
        try {
            // Selon version du SDK, la classe peut être Paydunya_DirectPay ou \Paydunya\DirectPay
            if (class_exists('\\Paydunya\\DirectPay')) {
                $dp = new \Paydunya\DirectPay();
                if ($dp->creditAccount($recipient, $amount)) {
                    return [
                        'success' => true,
                        'transaction_id' => $dp->transaction_id ?? null,
                        'description' => $dp->description ?? null,
                        'response_text' => $dp->response_text ?? null,
                    ];
                }
                return [
                    'success' => false,
                    'error' => $dp->response_text ?? 'Echec PayDunya DirectPay',
                ];
            }

            if (class_exists('\\Paydunya_DirectPay')) {
                $dp = new \Paydunya_DirectPay();
                if ($dp->creditAccount($recipient, $amount)) {
                    return [
                        'success' => true,
                        'transaction_id' => $dp->transaction_id ?? null,
                        'description' => $dp->description ?? null,
                        'response_text' => $dp->response_text ?? null,
                    ];
                }
                return [
                    'success' => false,
                    'error' => $dp->response_text ?? 'Echec PayDunya DirectPay',
                ];
            }

            return [
                'success' => false,
                'error' => 'Classe DirectPay introuvable dans le SDK PayDunya.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
