<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

/**
 * グローバル（運営）システム設定の Key-Value ストア。
 *
 * 運用者画面（SuperAdmin）から Stripe キー・料金・メール設定を保存し、
 * {@see self::applyToConfig()} を AppServiceProvider::boot() から呼ぶことで
 * 起動時に config() を上書きする。これにより .env を編集せずに
 * 運用者画面だけで設定変更が完結する。
 *
 * テナント分離（BelongsToTenant）の対象外＝全テナント共通のグローバル設定。
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'encrypted'];

    protected $casts = ['encrypted' => 'boolean'];

    /** 暗号化して保存すべきキー（APIシークレット・パスワード類） */
    public const SECRET_KEYS = [
        'stripe_secret',
        'stripe_webhook_secret',
        'mail_password',
    ];

    protected static function booted(): void
    {
        $forget = fn () => Cache::forget('settings.all');
        static::saved($forget);
        static::deleted($forget);
    }

    /**
     * 全設定を復号済みの連想配列で返す（キャッシュ）。
     *
     * @return array<string, string|null>
     */
    public static function allValues(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return static::all()->mapWithKeys(function (Setting $s) {
                $value = $s->value;
                if ($value !== null && $s->encrypted) {
                    try {
                        $value = Crypt::decryptString($value);
                    } catch (\Throwable $e) {
                        $value = null;
                    }
                }
                return [$s->key => $value];
            })->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::allValues()[$key] ?? $default;
    }

    /**
     * 設定を保存する。SECRET_KEYS のキーは暗号化して格納する。
     */
    public static function put(string $key, ?string $value): void
    {
        $encrypted = in_array($key, self::SECRET_KEYS, true);

        $stored = ($encrypted && $value !== null && $value !== '')
            ? Crypt::encryptString($value)
            : $value;

        static::updateOrCreate(['key' => $key], ['value' => $stored, 'encrypted' => $encrypted]);
    }

    /**
     * 保存済み設定を実行時 config() へ上書き適用する。
     * boot() から呼ぶため、テーブル未作成・DB未接続でも安全に握りつぶす。
     */
    public static function applyToConfig(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }
            $v = static::allValues();
        } catch (\Throwable $e) {
            return; // マイグレーション前・DB未接続などは無視
        }

        // 値が入っているときだけ config を上書きするヘルパ
        $set = function (string $config, string $key) use ($v): void {
            if (! empty($v[$key])) {
                config([$config => $v[$key]]);
            }
        };

        // ── Stripe / Cashier ───────────────────────────────
        $set('cashier.key', 'stripe_key');
        $set('cashier.secret', 'stripe_secret');
        $set('cashier.webhook.secret', 'stripe_webhook_secret');
        // 日本円固定（請求書・表示）。既定の usd を上書き。
        config(['cashier.currency' => 'jpy', 'cashier.currency_locale' => 'ja_JP']);

        // ── 料金プラン ─────────────────────────────────────
        $set('plans.standard.price_id', 'stripe_price_id_standard');
        $set('plans.premium.price_id', 'stripe_price_id_premium');
        if (! empty($v['plan_standard_amount'])) {
            config(['plans.standard.amount' => (int) $v['plan_standard_amount']]);
        }
        if (! empty($v['plan_premium_amount'])) {
            config(['plans.premium.amount' => (int) $v['plan_premium_amount']]);
        }

        // ── メール ─────────────────────────────────────────
        if (! empty($v['mail_mailer'])) {
            config(['mail.default' => $v['mail_mailer']]);
        }
        $set('mail.mailers.smtp.host', 'mail_host');
        if (! empty($v['mail_port'])) {
            config(['mail.mailers.smtp.port' => (int) $v['mail_port']]);
        }
        $set('mail.mailers.smtp.username', 'mail_username');
        $set('mail.mailers.smtp.password', 'mail_password');
        $set('mail.mailers.smtp.scheme', 'mail_encryption');
        $set('mail.from.address', 'mail_from_address');
        $set('mail.from.name', 'mail_from_name');
    }
}
