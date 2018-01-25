<?php

namespace App\Http\Controllers\Api;

use App\Repositories\QuotesRepository;
use App\Transformers\QuoteTransformer;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Restable;

class QuotesController extends Controller
{
    /**
     * @var QuotesRepository
     */
    protected $quotesRepository;

    public function __construct(QuotesRepository $quotesRepository)
    {
        $this->quotesRepository = $quotesRepository;
    }

    /**
     * Retrieve random quote
     *
     * @return object
     */
    public function random()
    {
        $me = Auth::guard('api')->user();

        return Restable::single(
            $this->quotesRepository->random($me->lang()),
            new QuoteTransformer
        );
    }
}
