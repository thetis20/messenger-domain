<?php

namespace Messenger\Domain\Request;

use Assert\AssertionFailedException;
use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Exception\CreateDiscussionForbiddenException;
use Messenger\Domain\Exception\NotAMemberOfTheDiscussionException;
use Messenger\Domain\Entity\UserInterface;
use Assert\Assertion;
use Messenger\Domain\Exception\SendMessageForbiddenException;

class SendMessageRequest
{
    private string $message;
    private Member $author;
    private Discussion $discussion;

    /**
     * @throws AssertionFailedException
     * @throws NotAMemberOfTheDiscussionException
     * @throws SendMessageForbiddenException
     */
    public static function create(string $message, Discussion $discussion, UserInterface $author): SendMessageRequest
    {
        if (!in_array('ROLE_USER', $author->getRoles())) {
            throw new SendMessageForbiddenException($author, $discussion);
        }

        $discussionMember = $discussion->findDiscussionMemberByEmail($author->getEmail());
        if (!$discussionMember) {
            throw new NotAMemberOfTheDiscussionException();
        }

        Assertion::notBlank($message);

        return new SendMessageRequest($message, $discussionMember->getMember(), $discussion);
    }

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
