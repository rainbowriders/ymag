<?php

namespace App\Transformers;

use Google_Service_Gmail_Label;
use League\Fractal\TransformerAbstract;

class GmailLabelTransformer extends TransformerAbstract
{
    public function transform(Google_Service_Gmail_Label $label)
    {
        return [
            'id' => $label->getId(),
            'name' => $label->getName(),
            'system' => $label->getType(),
        ];
    }
}