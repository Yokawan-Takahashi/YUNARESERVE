<?php

namespace App\Listeners;

use App\Models\Tenant;
use Laravel\Cashier\Events\WebhookReceived;

class HandleStripeWebhook
{
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $type    = $payload['type'] ?? null;

        match ($type) {
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($payload),
            'invoice.payment_failed'        => $this->handlePaymentFailed($payload),
            'invoice.paid'                  => $this->handleInvoicePaid($payload),
            default                         => null,
        };
    }

    private function handleSubscriptionDeleted(array $payload): void
    {
        $customerId = $payload['data']['object']['customer'] ?? null;
        if (! $customerId) return;

        Tenant::withoutGlobalScopes()
            ->where('stripe_id', $customerId)
            ->update(['status' => 'suspended', 'plan' => null]);
    }

    private function handlePaymentFailed(array $payload): void
    {
        $customerId      = $payload['data']['object']['customer'] ?? null;
        $attemptCount    = $payload['data']['object']['attempt_count'] ?? 1;
        if (! $customerId) return;

        // 3回目以降の失敗でsuspended
        if ($attemptCount >= 3) {
            Tenant::withoutGlobalScopes()
                ->where('stripe_id', $customerId)
                ->update(['status' => 'suspended']);
        }
    }

    private function handleInvoicePaid(array $payload): void
    {
        $customerId = $payload['data']['object']['customer'] ?? null;
        if (! $customerId) return;

        Tenant::withoutGlobalScopes()
            ->where('stripe_id', $customerId)
            ->where('status', 'suspended')
            ->update(['status' => 'active']);
    }
}
