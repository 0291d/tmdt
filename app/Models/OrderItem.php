<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class OrderItem extends Model
{
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted(): void
    {
        static::creating(function (OrderItem $item) {
            if (is_null($item->price) && $item->product_id) {
                $item->price = Product::whereKey($item->product_id)->value('price');
            }
        });

        $recalc = function (OrderItem $item) {
            if (!$item->order_id) {
                return;
            }
            $order = Order::with('items')->find($item->order_id);
            if ($order) {
                $subtotal = $order->items->sum(function ($i) {
                    return (int) $i->quantity * (float) $i->price;
                });
                $percent = (int) ($order->discount_percent ?? 0);
                $discount = $percent ? (int) floor($subtotal * $percent / 100) : 0;
                $final = max(0, (int) $subtotal - $discount);
                // Update directly to bypass any mutators that block manual assignment
                $order->newQuery()->whereKey($order->id)->update([
                    'discount_amount' => $discount,
                    'total' => $final,
                ]);
            }
        };

        static::saved($recalc);
        static::deleted($recalc);
    }
}
