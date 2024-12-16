<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class NewsCategory extends Model
{
    use NodeTrait;

    protected $fillable = ['name', 'slug', 'type'];

    public function newsItems()
    {
        return $this->belongsToMany(NewsItem::class, 'news_item_category');
    }
} 