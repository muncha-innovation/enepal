<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessHours extends Model
{
    use HasFactory;
    
    protected $fillable = ['business_id', 'day', 'open_time', 'close_time', 'is_open'];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    // Add mutators to format time without seconds
    public function getOpenTimeAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : null;
    }

    public function getCloseTimeAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : null;
    }

    public function setOpenTimeAttribute($value)
    {
        $this->attributes['open_time'] = $value ? date('H:i:s', strtotime($value)) : null;
    }

    public function setCloseTimeAttribute($value)
    {
        $this->attributes['close_time'] = $value ? date('H:i:s', strtotime($value)) : null;
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
