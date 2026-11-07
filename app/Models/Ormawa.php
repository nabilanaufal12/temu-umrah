<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ormawa extends Model
{
    use HasFactory;

    protected $table = 'ormawa';

    protected $fillable = [
        'user_id',
        'nama_ormawa',
        'tingkat',
        'ketua',
        'pembina',
        'no_sk',
        'dokumen_sk',
        'deskripsi',
        'logo_path',
        'status_verifikasi',
        'kuota_panitia_maks',
        'jumlah_panitia_aktif',
    ];

    protected $casts = [
        'kuota_panitia_maks' => 'integer',
        'jumlah_panitia_aktif' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'ormawa_id');
    }

    public function canAddPanitia(): bool
    {
        return ($this->jumlah_panitia_aktif ?? 0) < ($this->kuota_panitia_maks ?? 2);
    }
}
