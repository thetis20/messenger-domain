<?php

namespace Messenger\Domain\Request;

use Assert\AssertionFailedException;
use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Exception\NotAMemberOfTheDiscussionException;
use Messenger\Domain\Entity\UserInterface;
use Assert\Assertion;
use Messenger\Domain\Exception\SendMessageForbiddenException;

class SendMessageRequest
{
    private string $message;
    private Member $author;
    private Discussion $discussion;

    public function __construct(string $message, Member $author, Discussion $discussion)
    {
        $this->message = $message;
        $this->author = $author;
        $this->discussion = $discussion;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getAuthor(): Member
    {
        return $this->author;
    }

    public function getDiscussion(): Discussion
    {
        return $this->discussion;
    }
}
