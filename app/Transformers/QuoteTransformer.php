<?php

namespace App\Transformers;

use App\Quote;
use League\Fractal\TransformerAbstract;

class QuoteTransformer extends TransformerAbstract
{
    public function transform(Quote $quote)
    {
        return [
            'id' => $quote->id,
            'quote' => $quote->quote,
            'author' => $quote->author,
        ];
    }
}