<?php

namespace App\Transformers;

use App\Services\GmailMessage;
use League\Fractal\TransformerAbstract;

class GmailMessageTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'body',
    ];

    public function transform(GmailMessage $message)
    {   
        return [
            'id' => $message->getId(),
            'snippet' => $message->getSnippet(),
            'subject' => $message->header('Subject'),
            'from' => $message->from(),
            'labels' => $message->getLabelIds(),
            'date' => $message->getInternalDate(),
        ];
    }

    public function includeBody(GmailMessage $message)
    {
        return $this->item($message, function ($message) {
            return $message->body();
        });
    }
}