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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ormawa_id')->constrained('ormawa')->onDelete('cascade');
            $table->string('judul', 255);
            $table->string('slug', 255)->unique();
            $table->enum('kategori', ['SEMINAR', 'WORKSHOP', 'LOMBA', 'BAZAR']);
            $table->enum('tipe_biaya', ['gratis', 'berbayar'])->default('gratis');
            $table->decimal('harga', 12, 2)->default(0.00);
            $table->integer('kuota_maks')->default(100);
            $table->integer('kuota_terisi')->default(0);
            $table->date('tanggal_kegiatan');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('lokasi', 255);
            $table->text('deskripsi');
            $table->text('manfaat_fasilitas')->nullable();
            $table->string('banner_path', 255)->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'published', 'ongoing', 'selesai', 'dibatalkan', 'ditolak'])->default('pending_approval');
            $table->text('catatan_admin')->nullable()->comment('Alasan revisi / penolakan dari BAAK');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null')->comment('Super Admin yang menyetujui');
            $table->timestamp('disetujui_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
