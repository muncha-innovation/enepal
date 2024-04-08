<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    protected $fillable = ['title','cover_image', 'business_id', 'user_id'];
    public function business() {
        return $this->belongsTo(Business::class);
    }

    public function images() {
        return $this->hasMany(GalleryImage::class);
    }
}
