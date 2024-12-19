<?php

namespace Messenger\Domain\RequestFactory;

use Messenger\Domain\Entity\UserInterface;
use Messenger\Domain\Exception\DiscussionNotFoundException;
use Messenger\Domain\Exception\ShowDiscussionForbiddenException;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Request\ShowDiscussionRequest;

final readonly class ShowDiscussionRequestFactory
{

    public function __construct(private DiscussionGateway $discussionGateway)
    {
    }

    /**
     * @param UserInterface $user
     * @param string $discussionId
     * @param array{page?: int, limit?: int} $options
     * @return ShowDiscussionRequest
     * @throws DiscussionNotFoundException
     * @throws ShowDiscussionForbiddenException
     */
    public function create(UserInterface $user, string $discussionId, array $options = []): ShowDiscussionRequest
    {
        if (!in_array('ROLE_USER', $user->getRoles())) {
            throw new ShowDiscussionForbiddenException($user);
        }
        $discussion = $this->discussionGateway->find($discussionId);

        if ($discussion === null) {
            throw new DiscussionNotFoundException($discussionId);
        }

        if (!$discussion->isMember($user->getEmail())) {
            throw new ShowDiscussionForbiddenException($user);
        }

        return new ShowDiscussionRequest($user, $discussion, $options['page'] ?? 1, $options['limit'] ?? 10);
    }

}