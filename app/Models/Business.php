<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Business extends Model
{
    use HasFactory;
    static $ROLES = ['owner', 'admin', 'member'];
    
    protected $guarded = [];

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function users()
    { 
        return $this->belongsToMany(User::class)->withPivot(['role','position']);
    }
    public function type() {
        return $this->belongsTo(BusinessType::class, 'type_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function contactPerson() {
        return $this->belongsTo(User::class, 'contact_person_id');
    }

    public function posts() {
        return $this->hasMany(Post::class, 'business_id');
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
    public function notices() {
        return $this->hasMany(Notice::class);
    }
}
