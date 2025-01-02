<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGender extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function newsItems()
    {
        return $this->belongsToMany(NewsItem::class, 'news_item_gender');
    }
}
