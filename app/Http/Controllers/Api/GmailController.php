<?php

namespace App\Http\Controllers\Api;

use App\Repositories\GMailRepository;
use App\Transformers\GmailMessageTransformer;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Restable;

class GmailController extends Controller
{
    /**
     * @var GMailRepository
     */
    private $gMailRepository;

    public function __construct(GMailRepository $gMailRepository)
    {
        $this->gMailRepository = $gMailRepository;
    }

    public function lists(Request $request)
    {
        $me = Auth::guard('api')->user();

        $messages = $this->gMailRepository->lists(
            $me->email,
            $request
        );
        return [
            'messages' => array_map(function ($item) {
                return (new GmailMessageTransformer)->transform($item);
            }, $messages['messages']),
            'nextPage' => $messages['nextPage']
        ];
    }

    public function get($messageId)
    {
        $me = Auth::guard('api')->user();

        $message = $this->gMailRepository->get(
            $me->email,
            $messageId
        );

        return Restable::single($message, new GmailMessageTransformer);
    }

    public function touch($messageId)
    {
        $me = Auth::guard('api')->user();

        $response = $this->gMailRepository->touch(
            $me->email,
            $messageId
        );

        return Restable::single($response, function () {
            return func_get_args();
        });
    }
}
