<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
    public function country() {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function state() {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function getCoordinatesAttribute() {
        return $this->latitude . ',' . $this->longitude;
    }
}