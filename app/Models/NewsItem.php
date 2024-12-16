<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    protected $fillable = [
        'source_id', 'title', 'description', 'url', 'image',
        'published_at', 'id', 'original_id'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function parentNews()
    {
        return $this->belongsToMany(NewsItem::class, 'news_relationships', 'child_news_id', 'parent_news_id')
            ->withTimestamps();
    }

    public function childNews()
    {
        return $this->belongsToMany(NewsItem::class, 'news_relationships', 'parent_news_id', 'child_news_id')
            ->withTimestamps();
    }

    public function isMainNews()
    {
        return $this->childNews()->exists();
    }

    public function isSubNews()
    {
        return $this->parentNews()->exists();
    }

    public function source()
    {
        return $this->belongsTo(NewsSource::class);
    }

    public function categories()
    {
        return $this->belongsToMany(NewsCategory::class, 'news_item_category');
    }
} 