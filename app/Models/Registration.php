<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $table = 'registrations';

    protected $fillable = [
        'user_id',
        'event_id',
        'nomor_tiket',
        'tanggal_daftar',
        'status',
        'token_presensi',
        'qr_code_hash',
    ];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'registration_id');
    }

    public function presensi()
    {
        return $this->hasOne(Presensi::class, 'registration_id');
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'registration_id');
    }
}
