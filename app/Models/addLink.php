<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class addLink extends Model
{
    protected $fillable = [
        'title',
        'URL',
        'image',
        'position',
    ];
}
