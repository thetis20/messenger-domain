<?php

namespace Messenger\Domain\Request;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\UserInterface;
use Messenger\Domain\Exception\ShowDiscussionForbiddenException;

class ShowDiscussionRequest
{
    /** @var int */
    private int $page;
    /** @var int */
    private int $limit;
    /** @var UserInterface */
    private UserInterface $user;
    private Discussion $discussion;

    /**
     * @param UserInterface $user
     * @param Discussion $discussion
     * @param int $page
     * @param int $limit
     */
    public function __construct(UserInterface $user, Discussion $discussion, int $page = 1, int $limit = 10)
    {
        $this->user = $user;
        $this->discussion = $discussion;
        $this->page = $page;
        $this->limit = $limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getDiscussion(): Discussion
    {
        return $this->discussion;
    }
}
