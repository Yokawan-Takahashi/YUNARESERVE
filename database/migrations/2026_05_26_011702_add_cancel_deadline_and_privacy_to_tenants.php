<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedTinyInteger('cancel_deadline_days')->nullable()->after('notify_email');
            $table->string('privacy_policy_url')->nullable()->after('cancel_deadline_days');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['cancel_deadline_days', 'privacy_policy_url']);
        });
    }
};
