<?php

namespace Messenger\Domain\Request;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Entity\Message;
use Messenger\Domain\Entity\UserInterface;

class DeleteMessageRequest
{
    private UserInterface $author;
    private Message $message;

    public function __construct(UserInterface $author, Message $message)
    {
        $this->message = $message;
        $this->author = $author;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function getAuthor(): UserInterface
    {
        return $this->author;
    }
    
}
