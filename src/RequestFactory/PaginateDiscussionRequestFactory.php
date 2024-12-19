<?php

namespace Messenger\Domain\RequestFactory;

use Messenger\Domain\Entity\UserInterface;
use Messenger\Domain\Exception\PaginateDiscussionForbiddenException;
use Messenger\Domain\Request\PaginateDiscussionRequest;

final class PaginateDiscussionRequestFactory
{
    /**
     * @param UserInterface $user
     * @param array{page?: int, limit?: int} $options
     * @return PaginateDiscussionRequest
     * @throws PaginateDiscussionForbiddenException
     */
    public function create(UserInterface $user, array $options = []): PaginateDiscussionRequest
    {
        if (!in_array('ROLE_USER', $user->getRoles())) {
            throw new PaginateDiscussionForbiddenException($user);
        }

        return new PaginateDiscussionRequest($user, $options['page'] ?? 1, $options['limit'] ?? 10);
    }

}