<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    protected $fillable = [
        'source_id', 'title', 'description', 'url', 'image',
        'published_at', 'id', 'original_id','is_active','min_age','max_age'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
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

    public function locations()
    {
        return $this->hasMany(NewsLocation::class);
    }

    public function tags()
    {
        return $this->belongsToMany(NewsTag::class, 'news_item_tag');
    }

    public function genders()
    {
        return $this->belongsToMany(UserGender::class, 'news_item_gender','news_item_id','gender_id');
    }
}