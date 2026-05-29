<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class BillingController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription('default');
        $invoice = null;

        if ($tenant->hasStripeId()) {
            try {
                $invoices = $tenant->invoices(true);
                $invoice = $invoices->first();
            } catch (\Exception $e) {
                // Stripe not configured or no invoices yet
            }
        }

        $plan = config('plans.standard');
        return view('admin.billing.index', compact('tenant', 'subscription', 'invoice', 'plan'));
    }

    public function portal(Request $request)
    {
        $tenant = auth()->user()->tenant;

        if (! $tenant->hasStripeId()) {
            return back()->with('error', 'Stripeカスタマー情報がまだ設定されていません。');
        }

        return $tenant->redirectToBillingPortal(
            route('admin.billing.index')
        );
    }

    public function checkout(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $priceId = config('plans.standard.price_id');

        if (! $priceId) {
            return back()->with('error', 'プランが設定されていません。');
        }

        return $tenant->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('admin.billing.index') . '?checkout=success',
                'cancel_url'  => route('admin.billing.index'),
            ]);
    }

    public function invoice(string $invoiceId)
    {
        $tenant = auth()->user()->tenant;

        return $tenant->downloadInvoice($invoiceId, [
            'vendor'  => config('app.name'),
            'product' => 'スタンダードプラン',
        ]);
    }
}
