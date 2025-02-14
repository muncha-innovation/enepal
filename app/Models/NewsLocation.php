<?php

namespace App\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class NewsLocation extends Model
{
    use SpatialTrait;

    protected $fillable = [
        'name', 
        'place_id',  
        'location',
        'country_id',
        'state_id',
        'news_item_id'
    ];
    

    protected $spatialFields = [
        'location'
    ];

    public function newsItem()
    {
        return $this->belongsTo(NewsItem::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}