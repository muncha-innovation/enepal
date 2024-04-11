<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'icon'];

    public function facilities() {
        return $this->belongsToMany(Facility::class,'business_type_facilities');
    }

}
