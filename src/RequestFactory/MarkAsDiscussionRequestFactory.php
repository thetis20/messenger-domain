<?php

namespace Messenger\Domain\RequestFactory;

use Messenger\Domain\Entity\UserInterface;
use Messenger\Domain\Exception\DiscussionNotFoundException;
use Messenger\Domain\Exception\MarkAsDiscussionForbiddenException;
use Messenger\Domain\Exception\NotAMemberOfTheDiscussionException;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Request\MarkAsDiscussionRequest;

final readonly class MarkAsDiscussionRequestFactory
{
    public function __construct(private DiscussionGateway $discussionGateway)
    {
    }

    /**
     * @param UserInterface $author
     * @param string $discussionId
     * @param bool $seen
     * @return MarkAsDiscussionRequest
     * @throws DiscussionNotFoundException
     * @throws MarkAsDiscussionForbiddenException
     * @throws NotAMemberOfTheDiscussionException
     */
    public function create(UserInterface $author, string $discussionId, bool $seen = true): MarkAsDiscussionRequest
    {
        if (!in_array('ROLE_USER', $author->getRoles())) {
            throw new MarkAsDiscussionForbiddenException($author);
        }
        $discussion = $this->discussionGateway->find($discussionId);

        if ($discussion === null) {
            throw new DiscussionNotFoundException($discussionId);
        }

        $discussionMember = $discussion->findDiscussionMemberByEmail($author->getEmail());

        if (!$discussionMember) {
            throw new NotAMemberOfTheDiscussionException();
        }

        return new MarkAsDiscussionRequest($author, $discussion, $seen);
    }

}