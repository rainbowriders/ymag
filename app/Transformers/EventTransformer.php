<?php

namespace App\Transformers;

use Carbon\Carbon;
use Google_Service_Calendar_Event;
use League\Fractal\TransformerAbstract;

class EventTransformer extends TransformerAbstract
{
    public function transform(Google_Service_Calendar_Event $event)
    {
        return [
            'id' => $event->getId(),
            'summary' => $event->getSummary(),
            'link' => $event->getHtmlLink(),
            'location' => $event->getLocation(),
            'status' => $event->getStatus(),
            'kind' => $event->getKind(),
            'reminders' => $event->getReminders(),
            'attendees' => $event->getAttendees(),
            'start' => $this->dateTime($event->getStart()),
            'end' => $this->dateTime($event->getEnd()),
            'allDay' => $this->allDayEvent($event),
            'visibility' => $event->getVisibility(),
            'birthday' => false !== stripos($event->getId(), 'BIRTHDAY'),
            //'object' => $event->toSimpleObject(),
            //'methods' => get_class_methods($event)
        ];
    }

    /**
     * @param Google_Service_Calendar_Event $event
     * @return bool
     */
    protected function allDayEvent(Google_Service_Calendar_Event $event)
    {
        return (is_null($event->getStart()->dateTime) && is_null($event->getEnd()->dateTime));
    }

    private function dateTime($eventDateTime)
    {
        if (!is_null($eventDateTime->date) && is_null($eventDateTime->dateTime)) {
            $value = Carbon::parse($eventDateTime->date);

            return [
                'date' => $value->toFormattedDateString(),
                'formattedDate' => $value->toFormattedDateString(),
                'time' => null,
            ];
        }
        $value = Carbon::parse($eventDateTime->dateTime);

        return [
            'date' => $value->toFormattedDateString(),
            'formattedDate' => $value->toFormattedDateString(),
            'time' => $value->format('H:i'),
        ];
    }
}
