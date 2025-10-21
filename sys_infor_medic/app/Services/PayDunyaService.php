<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Paydunya\Setup;
use Paydunya\Checkout\CheckoutInvoice;
use Paydunya\Checkout\Store;

class PayDunyaService
{
    public function __construct()
    {
        $cfg = config('services.paydunya');

        if (!$cfg || empty($cfg['master_key']) || empty($cfg['public_key']) || empty($cfg['private_key']) || empty($cfg['token'])) {
            throw new \RuntimeException('Configuration PayDunya incomplète.');
        }

        Setup::setMasterKey($cfg['master_key']);
        Setup::setPublicKey($cfg['public_key']);
        Setup::setPrivateKey($cfg['private_key']);
        Setup::setToken($cfg['token']);
        Setup::setMode($cfg['mode'] ?? 'test');

        Store::setName($cfg['store_name'] ?? config('app.name', 'SMART-HEALTH'));
        Store::setTagline($cfg['store_tagline'] ?? 'Système de gestion médicale');
        Store::setPhoneNumber($cfg['store_phone'] ?? '');
        Store::setPostalAddress($cfg['store_address'] ?? '');
        Store::setWebsiteUrl($cfg['store_website'] ?? config('app.url'));
        Store::setLogoUrl($cfg['store_logo'] ?? config('app.url') . '/logo.png');
        Store::setCallbackUrl(route('webhooks.paydunya'));
        Store::setReturnUrl(route('payments.paydunya.verify'));
        Store::setCancelUrl(route('payments.cancel'));
    }

    public function createCheckout(Order $order): array
    {
        $invoice = new CheckoutInvoice();
        $invoice->setTotalAmount((int) $order->total_amount);
        $invoice->setDescription('Paiement commande #' . $order->id);
        $invoice->addCustomData('order_id', $order->id);
        $invoice->addCustomData('user_id', $order->user_id);

        // Add items to invoice for display purposes
        foreach ($order->items as $item) {
            $invoice->addItem($item->label, 1, (int) $item->amount, (int) $item->amount);
        }

        if ($invoice->create()) {
            return [
                'success' => true,
                'payment_url' => $invoice->getInvoiceUrl(),
                'token' => $invoice->getToken(),
            ];
        } else {
            return [
                'success' => false,
                'error' => $invoice->response_text,
            ];
        }
    }

    public function verifyWebhook(array $data): bool
    {
        $cfg = config('services.paydunya');
        $masterKey = $cfg['master_key'];

        if (!isset($data['data']['hash']) || !isset($data['data']['status'])) {
            return false;
        }

        $receivedHash = $data['data']['hash'];
        $expectedHash = hash('sha512', $masterKey);

        return hash_equals($expectedHash, $receivedHash);
    }

    public function verifyPayment(string $token): array
    {
        $invoice = new CheckoutInvoice();
        if ($invoice->confirm($token)) {
            return [
                'success' => true,
                'status' => $invoice->getStatus(),
                'order_id' => $invoice->getCustomData('order_id'),
                'customer_name' => $invoice->getCustomerInfo('name'),
                'customer_email' => $invoice->getCustomerInfo('email'),
                'customer_phone' => $invoice->getCustomerInfo('phone'),
                'receipt_url' => $invoice->getReceiptUrl(),
            ];
        } else {
            return [
                'success' => false,
                'error' => $invoice->response_text,
                'status' => $invoice->getStatus(),
            ];
        }
    }
}
