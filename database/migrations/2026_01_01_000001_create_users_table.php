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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 20)->nullable()->unique()->comment('NIM Mahasiswa jika role mahasiswa');
            $table->string('nama', 150);
            $table->string('email', 150)->unique();
            $table->string('no_wa', 25)->nullable()->comment('Nomor WhatsApp aktif untuk tiket & notif');
            $table->string('password');
            $table->enum('role', ['mahasiswa', 'panitia', 'admin'])->default('mahasiswa');
            $table->enum('status', ['aktif', 'nonaktif', 'banned'])->default('aktif');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
