<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignOption extends Model
{
    protected $fillable = [
        'type',
        'slug',
        'name',
        'swatch',
    ];
}
