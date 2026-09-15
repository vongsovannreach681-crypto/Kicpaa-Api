<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MySetting extends Model
{
    protected $fillable = [
        'title',
        'short_title',
        'image',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}