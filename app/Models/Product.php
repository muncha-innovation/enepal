<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, HasTranslations;
    protected $fillable = ['name', 'description', 'price', 'currency', 'image', 'slug', 'active'];
    protected $translatable = ['name', 'description'];
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
            if($product->hasTranslation('name','en')) {
                $slug = \Str::slug($product->getTranslation('name','en')).'-'.$product->id;
            } else if ($product->hasTranslation('name','np')) {
                $slug = \Str::slug($product->getTranslation('name','np')).'-'.$product->id;
            } else {
                $slug = 'product-'.$product->id;
            }
            $product->slug = $slug;
        $product->save();
        });
    }
}
