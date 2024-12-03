<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryImage extends Model
{
    protected $fillable = ['gallery_id', 'image','business_id','original_filename', 'title'];
    use HasFactory;

    public function gallery() {
        return $this->belongsTo(Gallery::class);
    }
    public function business() {
        return $this->belongsTo(Business::class);
    }
    public function getThumbnailAttribute() {
        $imageName = explode('/', $this->image)[2];
        
        return Storage::disk('public')->url('gallery/' . $this->business_id . '/thumbnail/' . $imageName);
    }

}
