<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('company_name');
            $table->string('industry')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('color', 7)->default('#4F46E5');
            $table->string('status', 20)->default('active');
            $table->json('features')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->string('plan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
