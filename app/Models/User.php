<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    // Người dùng hệ thống: có vai trò (role), có customer profile, orders, comments, wishlists
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function canAccessFilament(): bool
    {
        return strcasecmp((string) ($this->role ?? ''), 'admin') === 0;
    }

    // Hồ sơ khách hàng gắn với user
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    // Các đơn hàng do user đặt (thông qua Customer)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Bình luận do user tạo
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Bản ghi sản phẩm yêu thích của user
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}

