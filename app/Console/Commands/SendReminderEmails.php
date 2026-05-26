<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Services\MailService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendReminderEmails extends Command
{
    protected $signature   = 'reminder:send {--date= : 送信対象日 Y-m-d（省略時は翌日）}';
    protected $description = '翌日開催のイベント予約者へリマインドメールを送信';

    public function handle(MailService $mailService): int
    {
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::tomorrow();

        $this->info("対象日: {$targetDate->toDateString()}");

        $reservations = Reservation::withoutGlobalScopes()
            ->with(['event', 'slot', 'tenant'])
            ->whereHas('slot', fn($q) => $q->whereDate('date', $targetDate))
            ->where('status', 'reserved')
            ->get();

        if ($reservations->isEmpty()) {
            $this->info('送信対象なし');
            return self::SUCCESS;
        }

        $sent    = 0;
        $skipped = 0;

        foreach ($reservations as $reservation) {
            $tenant = $reservation->tenant;

            // features.reminder が明示的に false ならスキップ
            if (isset($tenant->features['reminder']) && !$tenant->features['reminder']) {
                $skipped++;
                continue;
            }

            try {
                $mailService->sendReminder($reservation, $tenant);
                $sent++;
                $this->line("  送信: {$reservation->email}");
            } catch (\Exception $e) {
                $this->warn("  失敗: {$reservation->email} — {$e->getMessage()}");
            }
        }

        $this->info("完了: 送信 {$sent} 件 / スキップ {$skipped} 件");
        return self::SUCCESS;
    }
}
