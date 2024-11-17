<?php

namespace Messenger\Domain\Request;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Exception\NotAMemberOfTheDiscussionException;
use Messenger\Domain\Entity\UserInterface;
use Assert\Assertion;

class SendMessageRequest
{
    private string $message;
    private UserInterface $author;
    private Discussion $discussion;

    public static function create(string $message, Discussion $discussion,  UserInterface $author): SendMessageRequest
    {

        Assertion::notBlank($message);
        if (!$discussion->isMember($author)) {
            throw new NotAMemberOfTheDiscussionException();
        }
        return new SendMessageRequest($message, $author, $discussion);
    }

    public function __construct(string $message, UserInterface $author, Discussion $discussion)
    {
        $this->message = $message;
        $this->author = $author;
        $this->discussion = $discussion;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getAuthor(): UserInterface
    {
        return $this->author;
    }

    public function getDiscussion(): Discussion
    {
        return $this->discussion;
    }
}
