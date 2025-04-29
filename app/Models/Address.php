<?php

namespace App\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class Address extends Model
{
    use HasFactory, SpatialTrait;
    protected $guarded = [];
    protected $spatialFields = ['location'];

    protected static function booted()
    {
        static::creating(function ($address) {
            if (empty($address->location)) {
                $address->location = DB::raw("ST_GeomFromText('POINT(0 0)')");
            }
        });
    }
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