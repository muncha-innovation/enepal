<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Business extends Model
{
    use HasFactory;
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
}
