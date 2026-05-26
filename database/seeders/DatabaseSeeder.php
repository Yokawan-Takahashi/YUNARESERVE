<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\FormField;
use App\Models\Slot;
use App\Models\Category;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. 運営テナント + superadmin ──────────────────────────────────────
        $operator = Tenant::firstOrCreate(
            ['slug' => 'operator'],
            ['company_name' => 'YUNARI RESERVE 運営', 'status' => 'active']
        );

        User::firstOrCreate(
            ['email' => 'superadmin@yunari.jp'],
            [
                'tenant_id' => $operator->id,
                'name'      => '運営管理者',
                'password'  => Hash::make('password'),
                'role'      => 'superadmin',
            ]
        );

        // ── 2. テナントA: デモ整体院 ───────────────────────────────────────────
        $demo = Tenant::firstOrCreate(
            ['slug' => 'demo'],
            [
                'company_name'         => 'デモ整体院',
                'industry'             => '医療・ヘルスケア',
                'color'                => '#0ea5e9',
                'notify_email'         => 'admin@demo.example',
                'cancel_deadline_days' => 2,
                'privacy_policy_url'   => 'https://example.com/privacy',
                'status'               => 'active',
            ]
        );

        User::firstOrCreate(['email' => 'owner@demo.example'], [
            'tenant_id' => $demo->id, 'name' => 'オーナー 山田',
            'password'  => Hash::make('password'), 'role' => 'owner',
        ]);
        User::firstOrCreate(['email' => 'staff@demo.example'], [
            'tenant_id' => $demo->id, 'name' => 'スタッフ 鈴木',
            'password'  => Hash::make('password'), 'role' => 'staff',
        ]);
        User::firstOrCreate(['email' => 'viewer@demo.example'], [
            'tenant_id' => $demo->id, 'name' => '閲覧者 田中',
            'password'  => Hash::make('password'), 'role' => 'viewer',
        ]);

        app()->instance('tenant', $demo);

        $catA = Category::firstOrCreate(
            ['tenant_id' => $demo->id, 'name' => '骨盤矯正'],
            ['icon' => '🦴', 'sort' => 1, 'active' => true]
        );
        $catB = Category::firstOrCreate(
            ['tenant_id' => $demo->id, 'name' => '全身ほぐし'],
            ['icon' => '💆', 'sort' => 2, 'active' => true]
        );

        FormField::firstOrCreate(
            ['tenant_id' => $demo->id, 'label' => 'ご来院のきっかけ'],
            ['type' => 'select', 'options' => ['Web検索', '紹介', 'SNS', 'チラシ'], 'required' => false, 'sort' => 1]
        );
        FormField::firstOrCreate(
            ['tenant_id' => $demo->id, 'label' => '症状・お悩み'],
            ['type' => 'textarea', 'required' => false, 'sort' => 2]
        );

        $eventA = Event::firstOrCreate(
            ['tenant_id' => $demo->id, 'title' => '骨盤矯正 60分コース'],
            [
                'category_id' => $catA->id,
                'description' => '骨盤の歪みを丁寧に整える60分コースです。初回の方もお気軽にどうぞ。',
                'location'    => '1F施術室',
                'status'      => 'published',
            ]
        );
        $eventB = Event::firstOrCreate(
            ['tenant_id' => $demo->id, 'title' => '全身ほぐし 90分コース'],
            [
                'category_id' => $catB->id,
                'description' => '全身をじっくりほぐす90分の贅沢コース。疲労回復におすすめです。',
                'location'    => '2F VIPルーム',
                'status'      => 'published',
            ]
        );
        Event::firstOrCreate(
            ['tenant_id' => $demo->id, 'title' => '【準備中】新メニュー試験導入'],
            ['description' => '近日公開予定の新メニューです。', 'status' => 'draft']
        );

        $today = now()->addDays(3);
        foreach (range(0, 6) as $i) {
            $date = $today->copy()->addDays($i * 2)->format('Y-m-d');
            foreach ([['10:00','11:00'], ['13:00','14:00'], ['15:30','16:30']] as [$s, $e]) {
                Slot::firstOrCreate(
                    ['event_id' => $eventA->id, 'date' => $date, 'start_time' => $s],
                    ['end_time' => $e, 'capacity' => 3, 'status' => 'open']
                );
            }
        }
        foreach (range(0, 3) as $i) {
            $date = $today->copy()->addDays($i * 3 + 1)->format('Y-m-d');
            Slot::firstOrCreate(
                ['event_id' => $eventB->id, 'date' => $date, 'start_time' => '14:00'],
                ['end_time' => '15:30', 'capacity' => 2, 'status' => 'open']
            );
        }

        // ── 3. テナントB: サンプル英会話スクール ──────────────────────────────
        $school = Tenant::firstOrCreate(
            ['slug' => 'school'],
            [
                'company_name'         => 'サンプル英会話スクール',
                'industry'             => '教育・スクール',
                'color'                => '#16a34a',
                'notify_email'         => 'admin@school.example',
                'cancel_deadline_days' => 3,
                'status'               => 'active',
            ]
        );

        User::firstOrCreate(['email' => 'owner@school.example'], [
            'tenant_id' => $school->id, 'name' => 'オーナー 佐藤',
            'password'  => Hash::make('password'), 'role' => 'owner',
        ]);
        User::firstOrCreate(['email' => 'staff@school.example'], [
            'tenant_id' => $school->id, 'name' => 'スタッフ 伊藤',
            'password'  => Hash::make('password'), 'role' => 'staff',
        ]);
        User::firstOrCreate(['email' => 'admin@school.example'], [
            'tenant_id' => $school->id, 'name' => '管理者 渡辺',
            'password'  => Hash::make('password'), 'role' => 'admin',
        ]);

        app()->instance('tenant', $school);

        $catS1 = Category::firstOrCreate(
            ['tenant_id' => $school->id, 'name' => '初級コース'],
            ['icon' => '🔰', 'sort' => 1, 'active' => true]
        );
        $catS2 = Category::firstOrCreate(
            ['tenant_id' => $school->id, 'name' => '上級コース'],
            ['icon' => '🏆', 'sort' => 2, 'active' => true]
        );

        FormField::firstOrCreate(
            ['tenant_id' => $school->id, 'label' => '英語レベル'],
            ['type' => 'select', 'options' => ['初心者', '日常会話程度', 'ビジネスレベル', 'ネイティブ級'], 'required' => true, 'sort' => 1]
        );
        FormField::firstOrCreate(
            ['tenant_id' => $school->id, 'label' => '学習目標'],
            ['type' => 'textarea', 'required' => false, 'sort' => 2]
        );

        $eventS1 = Event::firstOrCreate(
            ['tenant_id' => $school->id, 'title' => '初級英会話 体験レッスン'],
            [
                'category_id' => $catS1->id,
                'description' => 'はじめての方向け50分体験レッスン。担当講師が丁寧に指導します。',
                'location'    => 'Room 101',
                'status'      => 'published',
            ]
        );
        $eventS2 = Event::firstOrCreate(
            ['tenant_id' => $school->id, 'title' => 'ビジネス英語 集中講座'],
            [
                'category_id' => $catS2->id,
                'description' => 'プレゼン・交渉・メールに特化した90分コース。',
                'location'    => 'Room 203',
                'status'      => 'published',
            ]
        );

        foreach (range(0, 4) as $i) {
            $date = $today->copy()->addDays($i * 2)->format('Y-m-d');
            foreach ([['10:00','10:50'], ['14:00','14:50'], ['18:00','18:50']] as [$s, $e]) {
                Slot::firstOrCreate(
                    ['event_id' => $eventS1->id, 'date' => $date, 'start_time' => $s],
                    ['end_time' => $e, 'capacity' => 4, 'status' => 'open']
                );
            }
        }
        foreach (range(0, 2) as $i) {
            $date = $today->copy()->addDays($i * 4 + 2)->format('Y-m-d');
            Slot::firstOrCreate(
                ['event_id' => $eventS2->id, 'date' => $date, 'start_time' => '13:00'],
                ['end_time' => '14:30', 'capacity' => 6, 'status' => 'open']
            );
        }

        // ─────────────────────────────────────────────────────────────────────
        $this->command->info('✅ シードデータ投入完了');
        $this->command->table(
            ['テナント', 'ロール', 'メール', 'パスワード'],
            [
                ['（運営）',              'superadmin', 'superadmin@yunari.jp',  'password'],
                ['デモ整体院',            'owner',      'owner@demo.example',    'password'],
                ['デモ整体院',            'staff',      'staff@demo.example',    'password'],
                ['デモ整体院',            'viewer',     'viewer@demo.example',   'password'],
                ['サンプル英会話スクール', 'owner',      'owner@school.example',  'password'],
                ['サンプル英会話スクール', 'staff',      'staff@school.example',  'password'],
                ['サンプル英会話スクール', 'admin',      'admin@school.example',  'password'],
            ]
        );
    }
}
