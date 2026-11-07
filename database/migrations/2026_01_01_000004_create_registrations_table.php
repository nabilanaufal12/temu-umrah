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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('nomor_tiket', 50)->unique();
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->enum('status', ['menunggu_pembayaran', 'terkonfirmasi', 'batal'])->default('terkonfirmasi');
            $table->string('token_presensi', 10)->nullable()->comment('Kode unik presensi 6 digit');
            $table->string('qr_code_hash', 100)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'event_id'], 'uq_user_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
