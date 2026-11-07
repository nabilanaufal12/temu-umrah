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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->onDelete('cascade');
            $table->string('order_id', 64)->unique();
            $table->string('transaction_id', 128)->nullable()->unique();
            $table->decimal('jumlah', 12, 2);
            $table->enum('metode_bayar', ['qris', 'virtual_account', 'ewallet'])->default('qris');
            $table->string('channel_detail', 64)->nullable();
            $table->enum('status', ['pending', 'settlement', 'expired', 'failed'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
