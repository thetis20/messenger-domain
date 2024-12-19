<?php

namespace Messenger\Domain\Request;

use Messenger\Domain\Entity\Discussion;

class ShowDiscussionRequest
{
    /** @var int */
    private int $page;
    /** @var int */
    private int $limit;
    private Discussion $discussion;

    /**
     * @param Discussion $discussion
     * @param int $page
     * @param int $limit
     */
    public function __construct(Discussion $discussion, int $page = 1, int $limit = 10)
    {
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

    public function getDiscussion(): Discussion
    {
        return $this->discussion;
    }
}
