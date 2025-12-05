<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function galleryImages()
    {
        return $this->hasMany(GalleryImage::class, 'galleries_id');
    }
}
