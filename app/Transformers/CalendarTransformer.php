<?php

namespace App\Transformers;

use Google_Service_Calendar_CalendarListEntry;
use League\Fractal\TransformerAbstract;

class CalendarTransformer extends TransformerAbstract
{
    public function transform(Google_Service_Calendar_CalendarListEntry $calendar)
    {   
        return [
            'id' => $calendar->getId(),
            'summary' => $calendar->getSummary(),
            'primary' => $calendar->getPrimary(),
        ];
    }
}
