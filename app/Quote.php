<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'language', 'show_at', 'quote', 'author'
    ];

    public function scopeForLang($query, $lang = 'en')
    {
        return $query->where('language', $lang);
    }
}
