<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    // Model ảnh dùng polymorphic (imageable) cho Product, News...
    // Cung cấp accessor url để lấy link public và tiện kiểm tra tồn tại trên disk.
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['imageable_id', 'imageable_type', 'path', 'is_main'];

    // Quan hệ polymorphic tới thực thể sở hữu ảnh (product/news)
    public function imageable()
    {
        return $this->morphTo();
    }

    // Accessor: $image->url trả về URL public từ storage 'public'
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    // Kiểm tra file có tồn tại trên disk 'public' không
    public function existsOnDisk(): bool
    {
        return Storage::disk('public')->exists($this->path);
    }
}
