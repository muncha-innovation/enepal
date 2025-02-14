<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'url',
        'image',
        'published_at',
        'id',
        'original_id',
        'is_active',
        'is_rejected',
        'is_featured',
        'sourceable_id',
        'sourceable_type',
        'created_by',
        'language',

    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
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

    public function sourceable()
    {
        return $this->morphTo();
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
        return $this->belongsToMany(UserGender::class, 'news_item_gender', 'news_item_id', 'gender_id');
    }

    public function ageGroups()
    {
        return $this->belongsToMany(AgeGroup::class, 'news_item_age_group');
    }

    public function getFormattedLocationsAttribute()
    {
        $locations = $this->locations->map(function($location) {
            return [
                'id' => $location->place_id ?: $location->name,
                'text' => $location->name,
                'selected' => true
            ];
        })->toArray();
        return $locations;
    }
}
