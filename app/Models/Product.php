<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'currency', 'image', 'slug', 'active'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function user() {
        return $this->belongsTo(User::class,'created_by');
    }
    protected static function boot() {
        parent::boot();
    
        static::created(function($product){
            $product->slug = \Str::slug($product->title).'-'.$product->id;
        $product->save();
        });
        }
}
