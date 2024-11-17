<?php

namespace Messenger\Domain\Response;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Message;

class SendMessageResponse
{
    private Discussion $discussion;
    private Message $message;

    public function __construct(Discussion $discussion, Message $message)
    {
        $this->discussion = $discussion;
        $this->message = $message;
    }

    public function getDiscussion(): Discussion
    {
        return $this->discussion;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }
}
