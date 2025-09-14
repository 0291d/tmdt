<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Customer extends Model
{
     use HasUuids, HasFactory;
     public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'phone', 'address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
