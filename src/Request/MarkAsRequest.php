<?php

namespace Messenger\Domain\Request;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Entity\UserInterface;

class MarkAsRequest
{
    private bool $seen;
    private UserInterface $author;
    private Discussion $discussion;

    public function __construct(UserInterface $author, Discussion $discussion, bool $seen = true)
    {
        $this->discussion = $discussion;
        $this->author = $author;
        $this->seen = $seen;
    }

    public function isSeen(): bool
    {
        return $this->seen;
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
