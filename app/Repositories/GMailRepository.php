<?php

namespace App\Repositories;

use App\Services\Gmail;
use Cache;
use Illuminate\Http\Request;

class GMailRepository
{
    protected $cacheMessageFor = 24 * 60;

    /**
     * Fetch GMail messages
     *
     * @param $userId
     * @param Request $request
     */
    public function lists($userId, Request $request)
    {
        return $this->fetchRemoteMessages($userId, $request);
    }

    public function get($userId, $messageId)
    {
        $cacheKey = "{$userId}_{$messageId}_message";
        $cacheKey = md5($cacheKey);

        return Cache::remember($cacheKey, $this->cacheMessageFor(), function () use ($userId, $messageId) {
            return $this->fetchSingleMessage($userId, $messageId);
        });
    }

    public function touch($userId, $messageId)
    {
        $gMail = Gmail::of($userId);

        return $gMail->touch($messageId);
    }

    /**
     * Fetch remote messages
     *
     * @param $userId string
     * @param Request $request
     * @return mixed
     */
    protected function fetchRemoteMessages($userId, Request $request)
    {
        $gMail = Gmail::of($userId);

        return $gMail
            ->match($request->get('q', null))
            ->withSpamTrash('false' == $request->get('includeSpamTrash'))
            ->take((int) $request->get('maxResults', 10))
            ->forPage($request->get('nextPageToken', null))
            ->messages();
    }

    /**
     * Fetch remote messages
     *
     * @param $userId string
     * @param $messageId
     * @return mixed
     */
    protected function fetchSingleMessage($userId, $messageId)
    {
        $gMail = Gmail::of($userId);

        return $gMail->get($messageId);
    }

    public function fetchAttachment($userId, $messageId, $attachmentId)
    {
        $gMail = Gmail::of($userId);

        return $gMail->attachment($messageId, $attachmentId);
    }

    protected function cacheMessageFor()
    {
        return ($this->localEnv() ? 0 : $this->cacheMessageFor);
    }

    /**
     * @return bool
     */
    protected function localEnv()
    {
        return 'local' == app()->environment();
    }
}