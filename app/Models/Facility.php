<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'input_type', 'icon'];

    public function businessTypes() {
        return $this->belongsToMany(BusinessType::class,'business_type_facilities');
    }
}
