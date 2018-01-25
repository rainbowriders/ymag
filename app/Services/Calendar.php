<?php

namespace App\Services;

use Carbon\Carbon;
use Google_Service_Calendar;

class Calendar
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    static public function of($email)
    {
        return new static($email);
    }

    public function listCalendars()
    {
        return app('google.calendar')->calendarList->listCalendarList();
    }

    public function events($calendarId, $fromTime, $timeZoneOffset)
    {
        $timeMin = Carbon::createFromTimestamp($fromTime = (int) round($fromTime / 1000), null);

        if ($timeZoneOffset != 0) {
            $timeMin->subMinutes($timeZoneOffset);
        }

        $timeMax = Carbon::parse($timeMin)->addMonth();

        //$this->debug($fromTime, $timeMin, $timeMax);

        return app('google.calendar.api')->events->listEvents($calendarId, [
            'showDeleted' => false,
            'singleEvents' => true,
            'orderBy' => 'startTime',
            'timeMin' => $timeMin->toRfc3339String(),
            'timeMax' => $timeMax->toRfc3339String()
        ]);
    }

    /**
     * @param $fromTime
     * @param $timeMin
     * @param $timeMax
     */
    protected function debug($fromTime, $timeMin, $timeMax)
    {
        dd(
            $fromTime,
            Carbon::now()->getTimestamp(),
            $timeMin,
            $timeMax,
            [
                'showDeleted' => false,
                'singleEvents' => true,
                'orderBy' => 'startTime',
                'timeMin' => $timeMin->toRfc3339String(),
                'timeMax' => $timeMax->toRfc3339String()
            ]
        );
    }
}