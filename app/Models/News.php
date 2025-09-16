<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    // Bài viết tin tức, có thể đính kèm nhiều ảnh (polymorphic)
    use HasFactory;

    protected $fillable = ['title', 'content'];

    // Ảnh của bài viết (polymorphic)
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }


}
