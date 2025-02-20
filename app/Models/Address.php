<?php

namespace App\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory, SpatialTrait;
    protected $guarded = [];
    protected $spatialFields = ['location'];
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
        return $this->location?->getLat() . ',' . $this->location?->getLng();
    }
}