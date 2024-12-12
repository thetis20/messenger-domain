<?php

namespace Messenger\Domain\Request;

use Messenger\Domain\Entity\UserInterface;
use Messenger\Domain\Exception\PaginateDiscussionForbiddenException;

class PaginateDiscussionRequest
{
    /** @var int */
    private int $page;
    /** @var int */
    private int $limit;
    /** @var UserInterface */
    private UserInterface $user;

    /**
     * @param UserInterface $user
     * @param array{limit?: int, page?: int} $options
     * @return PaginateDiscussionRequest
     * @throws PaginateDiscussionForbiddenException
     */
    public static function create(UserInterface $user, array $options = []): PaginateDiscussionRequest
    {
        if (!in_array('ROLE_USER', $user->getRoles())) {
            throw new PaginateDiscussionForbiddenException($user);
        }

        return new PaginateDiscussionRequest($user, $options['page'] ?? 1, $options['limit'] ?? 10);
    }

    /**
     * @param UserInterface $user
     * @param int $page
     * @param int $limit
     */
    public function __construct(UserInterface $user, int $page = 1, int $limit = 10)
    {
        $this->user = $user;
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
}
