<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignSetting extends Model
{
    protected $fillable = [
        'theme',
        'background',
        'text_color',
        'box_color',
    ];
}
