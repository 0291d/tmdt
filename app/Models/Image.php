<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['imageable_id', 'imageable_type', 'path', 'is_main'];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function existsOnDisk(): bool
    {
        return Storage::disk('public')->exists($this->path);
    }
}
