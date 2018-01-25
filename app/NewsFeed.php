<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsFeed extends Model
{
    protected $fillable = [
        'user_id', 'language', 'name', 'url', 'categories',
    ];

    public function scopeOfLang($query, $lang = 'en')
    {
        return $query->where('language', $lang);
    }

    public function categories()
    {
        $keys = explode(',', $this->categories) ?: [];
        $keys = array_map('trim', $keys);

        $values = array_map(function ($value) {
            return title_case($value);
        }, $keys);

        return array_combine($keys, $values);
    }
}
