<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsLocation extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude', 'radius'];

    public function newsItem()
    {
        return $this->belongsTo(NewsItem::class);
    }
} 