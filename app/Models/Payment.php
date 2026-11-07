<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'registration_id',
        'order_id',
        'transaction_id',
        'jumlah',
        'metode_bayar',
        'channel_detail',
        'status',
        'gateway_response',
        'settlement_time',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'gateway_response' => 'array',
        'settlement_time' => 'datetime',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }

    public function isSettled(): bool
    {
        return $this->status === 'settlement';
    }
}
