<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsTag extends Model
{
    protected $fillable = ['name', 'usage_count'];

    public function news()
    {
        return $this->belongsToMany(NewsItem::class, 'news_item_tag');
    }
} 