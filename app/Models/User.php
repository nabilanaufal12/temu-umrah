<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nim',
        'nama',
        'email',
        'no_wa',
        'foto',
        'password',
        'role',
        'status',
        'bio',
        'username',
        'github_username',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'avatar_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * URL Foto Avatar Pengguna
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->foto) {
            if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
                return $this->foto;
            }
            return asset('storage/' . $this->foto);
        }
        return null;
    }

    /**
     * Relasi ke Ormawa (jika role panitia)
     */
    public function ormawa()
    {
        return $this->hasOne(Ormawa::class, 'user_id');
    }

    /**
     * Relasi ke Pendaftaran Event
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class, 'user_id');
    }

    /**
     * Relasi ke Notifikasi Sistem
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
}
