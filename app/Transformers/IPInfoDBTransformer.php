<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class IPInfoDBTransformer extends TransformerAbstract
{
    public function transform($object)
    {
        return (array) $object;
    }
}
