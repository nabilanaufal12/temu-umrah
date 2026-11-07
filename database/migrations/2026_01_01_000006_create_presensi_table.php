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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained('registrations')->onDelete('cascade');
            $table->timestamp('waktu_hadir')->useCurrent();
            $table->enum('metode', ['scan_qr', 'token_input', 'manual_panitia'])->default('token_input');
            $table->enum('status', ['hadir', 'tidak_hadir', 'izin'])->default('hadir');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
