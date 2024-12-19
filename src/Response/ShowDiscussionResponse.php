<?php

namespace Messenger\Domain\Response;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Message;

class ShowDiscussionResponse
{
    /** @var Message[] */
    private array $messages;
    private int $total;
    private int $limit;
    private int $page;
    private Discussion $discussion;

    /**
     * @param Discussion $discussion
     * @param Message[] $messages
     * @param int $total
     * @param int $limit
     * @param int $page
     */
    public function __construct(Discussion $discussion, array $messages, int $total, int $limit, int $page)
    {
        $this->discussion = $discussion;
        $this->messages = $messages;
        $this->total = $total;
        $this->limit = $limit;
        $this->page = $page;
    }

    public function getDiscussion(): Discussion
    {
        return $this->discussion;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getTotalPages(): int
    {
        return (int) ceil($this->total / $this->limit);
    }

    public function hasPreviousPage(): bool
    {
        return $this->page > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->page < $this->getTotalPages();
    }

    public function getNextPage(): ?int
    {
        if (!$this->hasNextPage()) {
            return null;
        }
        return $this->page + 1;
    }

    public function getPreviousPage(): ?int
    {
        if (!$this->hasPreviousPage()) {
            return null;
        }
        return $this->page - 1;
    }

}
