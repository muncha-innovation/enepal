<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = ['gallery_id', 'image'];
    use HasFactory;

    public function gallery() {
        return $this->belongsTo(Gallery::class);
    }

    public function getThumbnailAttribute() {
        return \Storage::disk('public')->url($this->image);
    }
}
