<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsSource extends Model
{
    protected $fillable = ['name', 'url', 'logo', 'is_active'];

    public function newsItems()
    {
        return $this->hasMany(NewsItem::class, 'source_id');
    }
} 