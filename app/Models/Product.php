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
}
