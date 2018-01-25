<?php

namespace App\Repositories;

use App\Quote;

class QuotesRepository
{
    /**
     * Fetch random quote
     *
     * @return mixed
     */
    public function random($langId)
    {
        return Quote::forLang($langId)->orderBy(\DB::raw('RAND()'))->take(1)->first();
    }
}