<?php

namespace App\Services;

use App\Base64;
use Carbon\Carbon;
use Google_Service_Gmail_Message;
use Google_Service_Gmail_MessagePart;

class GmailMessage
{
    const BODY_PLAIN = 'plain';

    const BODY_HTML = 'html';

    /**
     * @var Google_Service_Gmail_Message
     */
    protected $message;

    protected $headers = [];

    protected $attachments = [];

    public function __construct(Google_Service_Gmail_Message $message)
    {
        $this->message = $message;
    }

    /**
     * Retrieve header value.
     *
     * @param null $key
     * @return mixed
     * @throws \Exception
     */
    public function header($key = null)
    {
        $headers = $this->fetchHeaders();

        if (null === $key) {
            return $headers;
        }

        if (!array_has($this->headers, $key)) {
            throw new \Exception('No value found for header: ' . $key);
        }

        return $this->headers[$key];
    }

    public function from()
    {
        $from = $this->header('From');

        preg_match('~^([^\<]+)\s+\<([^\>]+)\>$~si', $from, $matches);

        $matches = array_slice($matches, 1);

        $matches = array_map(function ($item) {
            return trim($item, '"\'');
        }, $matches);

        return array_reverse($matches);
    }

    /**
     * Fetch the message body.
     *
     * @param $message
     * @return mixed
     */
    public function body($message = null)
    {
        if (null === $message) {
            $message = $this->message->getPayload();

            $this->attachments = $this->collectAttachments($message);
        }

        if (empty($parts = $message->getParts())) {
            $type = $message->getMimeType();
            $type = $this->messageType($type);

            $body = Base64::decode(
                $message->getBody()->getData()
            );

            if ('html' == $type) {
                $body = $this->handleContentID($body);
            }

            if ('plain' == $type) {
                $body = nl2br($body);
            }

            return [$type => $body];
        } else {
            $body = [];

            foreach ($parts as $message) {
                if ($this->isContent($message)) {
                    $body += $this->body($message);
                }
            }
        }

        return $body;
    }

    /**
     * Get the message sent time.
     *
     * @return string
     */
    public function relativeTime()
    {
        return Carbon::createFromTimestamp(
            $this->message->getInternalDate() / 1000
        )->diffForHumans();
    }

    /**
     * Expose the internal Google Message API.
     *
     * @return mixed
     */
    public function expose()
    {
        return get_class_methods($this->message);
    }

    /**
     * Proxy calls to Google_Message class.
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->message, $method], $args);
    }

    public function __get($name)
    {
        return $this->message->$name;
    }

    /**
     * Fetch all headers
     *
     * @return mixed
     */
    protected function fetchHeaders()
    {
        if (empty ($this->headers)) {
            foreach ($this->message->getPayload()->getHeaders() as $header) {
                $this->headers[$header->getName()] = $header->getValue();
            }
        }

        return $this->headers;
    }

    /**
     * @param $type
     * @return mixed
     */
    protected function messageType($type)
    {
        $mime = explode('/', $type);
        $type = array_pop($mime);

        return $type;
    }

    protected function collectAttachments($message)
    {
        $attachments = [];

        foreach ($message->getParts() as $part) {
            if ($this->isAttachment($part)) {
                $attachments[] = $part;
            }

            if ($part->getParts()) {
                $attachments = array_merge(
                    $attachments,
                    $this->collectAttachments($part)
                );
            }
        }

        return $attachments;
    }

    protected function isAttachment(Google_Service_Gmail_MessagePart $part)
    {
        return $part->getFilename() || $part->getBody()->getAttachmentId();
    }

    protected function isContent(Google_Service_Gmail_MessagePart $part)
    {
        return in_array(
            $part->getMimeType(),
            ['text/plain', 'text/html', 'multipart/alternative', 'multipart/related']
        );
    }

    /**
     * Handle the Content-ID for HTML messages.
     *
     * @param $body
     * @return mixed
     */
    private function handleContentID($body)
    {
        $body = preg_replace_callback('~src="cid:(.+)"~isUm', function ($matches) {
            $cid = $matches[1];

            if ($attachmentId = $this->findCidContent($cid)) {
                return 'src="'.route('gmail.attachment', [
                    'message_id' => $this->message->getId(),
                    'attachment_id' => $attachmentId
                ]).'"';
            }

            return "/blank.gif";
        }, $body);

        return $body;
    }

    /**
     * Find the Content-ID attachment.
     *
     * @param $cid
     * @return array|null
     */
    private function findCidContent($cid)
    {
        foreach ($this->attachments as $attachment) {
            foreach ($attachment->getHeaders() as $header) {
                if ($header->getName() == 'Content-ID' && $header->getValue() == '<' . $cid . '>') {
                    return $attachment->getBody()->getAttachmentId();
                }
            }
        }

        return null;
    }
}
