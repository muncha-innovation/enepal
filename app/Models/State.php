<?php

namespace App\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory, SpatialTrait;
    protected $table = 'states';
    protected $guarded = [];
    protected $spatialFields = ['location'];

    public function country() {
        return $this->belongsTo(Country::class,'country_id');
    }
}
