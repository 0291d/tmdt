<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'order_id',
        'provider',
        'currency',
        'amount',
        'txn_ref',
        'gateway_txn_no',
        'status',
        'response_code',
        'bank_code',
        'card_type',
        'secure_hash',
        'paid_at',
        'payload',
    ];

    protected $casts = [
        'amount' => 'integer',
        'payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

