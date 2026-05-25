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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('slot_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('kana')->nullable();
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->unsignedTinyInteger('companions')->default(0);
            $table->string('status', 20)->default('reserved'); // reserved/cancelled/attended
            $table->text('memo')->nullable();
            $table->string('cancel_token', 64)->unique()->nullable();
            $table->timestamps();

            $table->index(['slot_id', 'email']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
