<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Vendor extends Model
{
    protected $fillable = ['name', 'api_key'];

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
    
    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_vendor')
            ->withTimestamps();
    }
}
