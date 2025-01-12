<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsSource extends Model
{
    protected $fillable = ['name', 'url', 'logo', 'is_active', 'type', 'language', ];

    public function newsItems()
    {
        return $this->morphMany(NewsItem::class, 'sourceable');
    }
}
