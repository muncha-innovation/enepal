<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title','short_description', 'content','image','slug', 'active'];
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function business() {
        return $this->belongsTo(Business::class);
    }
    protected static function boot() {
        parent::boot();
    
        static::created(function($post){
            $post->slug = \Str::slug($post->title).'-'.$post->id;
        $post->save();
        });
        }
}
