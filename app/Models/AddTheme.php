<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddTheme extends Model
{
    protected $table = 'add_themes';

    protected $fillable = [
        'themeName',
        'themeImage',
    ];
}
