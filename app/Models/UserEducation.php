<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEducation extends Model
{
    protected $fillable = ['user_id', 'degree', 'institution', 'start_date','end_date', 'type'];


    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
