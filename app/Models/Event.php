<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'ormawa_id',
        'judul',
        'slug',
        'kategori',
        'tipe_biaya',
        'harga',
        'kuota_maks',
        'kuota_terisi',
        'tanggal_kegiatan',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'deskripsi',
        'manfaat_fasilitas',
        'banner_path',
        'certificate_template_path',
        'status',
        'catatan_admin',
        'disetujui_oleh',
        'disetujui_pada',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'kuota_maks' => 'integer',
        'kuota_terisi' => 'integer',
        'tanggal_kegiatan' => 'date',
        'manfaat_fasilitas' => 'array',
        'disetujui_pada' => 'datetime',
    ];

    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class, 'ormawa_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'event_id');
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
