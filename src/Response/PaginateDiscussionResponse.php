<?php

namespace Messenger\Domain\Response;

use Messenger\Domain\Entity\Discussion;

class PaginateDiscussionResponse
{
    /** @var Discussion[] */
    private array $discussions;
    /** @var int */
    private int $total;
    /** @var int */
    private int $limit;
    /** @var int */
    private int $page;

    /**
     * @param Discussion[] $discussions
     * @param int $total
     * @param int $limit
     * @param int $page
     */
    public function __construct(array $discussions, int $total, int $limit, int $page)
    {
        $this->discussions = $discussions;
        $this->total = $total;
        $this->limit = $limit;
        $this->page = $page;
    }

    /**
     * @return Discussion[]
     */
    public function getDiscussions(): array
    {
        return $this->discussions;
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
