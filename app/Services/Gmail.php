<?php

namespace App\Services;

use Google_Service_Gmail;
use Google_Service_Gmail_Label;
use Google_Service_Gmail_Message;
use Google_Service_Gmail_ModifyMessageRequest;

class Gmail
{
    const LABEL_SYSTEM = 'system';
    const LABEL_USER = 'user';

    /**
     * User's email.
     *
     * @var string
     */
    protected $email;

    /**
     * Number of messages to take.
     *
     * @var int
     */
    protected $take = 5;

    /**
     * Next page token.
     *
     * @var null
     */
    protected $pageToken = null;

    /**
     * Whether include spam messages or not.
     *
     * @var bool
     */
    protected $includeSpamTrash = false;

    /**
     * Match messages by criteria.
     *
     * @var string
     */
    protected $query = null;

    /**
     * Fetch only labeled messages.
     *
     * @var array
     */
    protected $labelIds = [];

    /**
     * GMail API client
     *
     * @var Google_Service_Gmail
     */
    protected $client;

    public function __construct($email)
    {
        $this->email = $email;

        $this->client = app('google.mail.api');
    }

    /**
     * The user account (email).
     *
     * @param $email
     * @return static
     */
    static public function of($email)
    {
        return new static($email);
    }

    /**
     * Limit number of messages.
     *
     * @param $take
     * @return $this
     */
    public function take($take)
    {
        $this->take = (int) $take;

        return $this;
    }

    public function forPage($pageToken)
    {
        $this->pageToken = $pageToken;

        return $this;
    }

    /**
     * Whether include spam messages or not.
     *
     * @param bool $flag
     * @return $this
     */
    public function withSpamTrash($flag = false)
    {
        $this->includeSpamTrash = (bool) $flag;

        return $this;
    }

    /**
     * Search for messages match criteria
     *
     * @param string $criteria
     * @return $this
     */
    public function match($criteria)
    {
        $this->query = $criteria;

        return $this;
    }

    /**
     * Fetch only messages having desired labels.
     *
     * @param array $labels
     * @return $this
     */
    public function labeledAs(array $labels = [])
    {
        $this->labelIds = (array) $labels;

        return $this;
    }

    /**
     * Fetch the labels.
     *
     * @param string $type
     * @param bool $noCategory
     * @return array
     */
    public function labels($type = self::LABEL_SYSTEM, $noCategory = true)
    {
        $labels = $this->client->users_labels->listUsersLabels($this->email)->getLabels();

        if (!is_null($type) && in_array($type, [static::LABEL_SYSTEM, static::LABEL_USER])) {
            $labels = array_filter($labels, function (Google_Service_Gmail_Label $label) use ($type) {
                return $label->getType() == $type;
            });
        }

        if ($noCategory) {
            $labels = array_filter($labels, function (Google_Service_Gmail_Label $label) {
                return false === stripos($label->getId(), 'CATEGORY_');
            });
        }
        return $labels;
    }

    /**
     * Fetch the messages.
     *
     * @return mixed
     */
    public function messages()
    {
        $args = array_filter([
            'includeSpamTrash' => $this->includeSpamTrash,
            'maxResults' => (int) $this->take,
//            'labelIds' => implode(" ", $this->labelIds),
            'labelIds' => 'INBOX',
            'q' => $this->query,
            'pageToken' => $this->pageToken,
        ], function ($item) {
            return !is_null($item) && ("" !== $item);
        });

        $messages = $this->client->users_messages->listUsersMessages($this->email, $args);

        $nextPageToken = $messages->getNextPageToken();

        $messages = array_map(function (Google_Service_Gmail_Message $message) {
            return $this->get($message->getId());
        }, $messages->getMessages());

        return [
            'messages' => $messages,
            'nextPage' => $nextPageToken
        ];
    }

    /**
     * Fetch the single message by ID.
     *
     * @return GmailMessage
     */
    public function get($id)
    {
        $message = $this->client->users_messages->get(
            $this->email, $id
        );

        return new GmailMessage($message);
    }

    /**
     * Fetch the message attachment.
     *
     * @param $messageId
     * @param $attachmentId
     * @return mixed
     */
    public function attachment($messageId, $attachmentId)
    {
        $attachment = $this->client->users_messages_attachments->get(
            $this->email,
            $messageId,
            $attachmentId
        );

        return $attachment->getData();
    }

    /**
     * Add/Remove message labels.
     *
     * @param $messageId
     * @return Google_Service_Gmail_Message
     */
    public function touch($messageId)
    {
        $modifyRequest = new Google_Service_Gmail_ModifyMessageRequest;
        $modifyRequest->setRemoveLabelIds(['UNREAD']);

        return $this->client->users_messages->modify(
            $this->email,
            $messageId,
            $modifyRequest
        );
    }
}
