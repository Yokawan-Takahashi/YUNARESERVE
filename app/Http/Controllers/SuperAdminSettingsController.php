<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * 運営のシステム設定画面。
 * Stripe・料金・メール設定を DB に保存し、起動時に config へ反映する。
 */
class SuperAdminSettingsController extends Controller
{
    /** 空欄で送信されたら既存値を維持するキー（マスク表示するシークレット類） */
    private const KEEP_IF_BLANK = [
        'stripe_secret',
        'stripe_webhook_secret',
        'mail_password',
    ];

    public function index()
    {
        $settings = Setting::allValues();

        return view('superadmin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // Stripe
            'stripe_key'                => 'nullable|string|max:255',
            'stripe_secret'             => 'nullable|string|max:255',
            'stripe_webhook_secret'     => 'nullable|string|max:255',
            'stripe_price_id_standard'  => 'nullable|string|max:255',
            'stripe_price_id_premium'   => 'nullable|string|max:255',
            // 料金（表示用・円）
            'plan_standard_amount'      => 'nullable|integer|min:0|max:10000000',
            'plan_premium_amount'       => 'nullable|integer|min:0|max:10000000',
            // メール
            'mail_mailer'               => 'nullable|in:sendmail,smtp,log',
            'mail_host'                 => 'nullable|string|max:255',
            'mail_port'                 => 'nullable|integer|min:1|max:65535',
            'mail_username'             => 'nullable|string|max:255',
            'mail_password'             => 'nullable|string|max:255',
            'mail_encryption'           => 'nullable|in:tls,smtps',
            'mail_from_address'         => 'nullable|email:rfc|max:255',
            'mail_from_name'            => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            // シークレットは空欄なら上書きしない（既存値を保持）
            if (in_array($key, self::KEEP_IF_BLANK, true) && ($value === null || $value === '')) {
                continue;
            }
            Setting::put($key, ($value === '' || $value === null) ? null : (string) $value);
        }

        return redirect()
            ->route('superadmin.settings.index')
            ->with('success', 'システム設定を保存しました。');
    }
}
