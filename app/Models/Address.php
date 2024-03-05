<?php

namespace App\Models;

use App\Traits\ViewDateInJapanese;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;
    use ViewDateInJapanese;


    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}