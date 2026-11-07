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
        Schema::create('ormawa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_ormawa', 150);
            $table->enum('tingkat', ['universitas', 'fakultas', 'himpunan', 'ukm']);
            $table->string('ketua', 150)->nullable();
            $table->string('pembina', 150)->nullable();
            $table->string('no_sk', 100)->nullable();
            $table->string('dokumen_sk', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->enum('status_verifikasi', ['pending', 'verified', 'rejected'])->default('pending');
            $table->integer('kuota_panitia_maks')->default(2)->comment('Aturan Tata Kelola BAAK: Maksimal 2 panitia aktif per Ormawa');
            $table->integer('jumlah_panitia_aktif')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ormawa');
    }
};
