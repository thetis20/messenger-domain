<?php

namespace Messenger\Domain\RequestFactory;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Messenger\Domain\Entity\UserInterface;
use Messenger\Domain\Exception\DiscussionNotFoundException;
use Messenger\Domain\Exception\NotAMemberOfTheDiscussionException;
use Messenger\Domain\Exception\SendMessageForbiddenException;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Request\SendMessageRequest;

final readonly class SendMessageRequestFactory
{
    public function __construct(private DiscussionGateway $discussionGateway)
    {
    }

    /**
     * @param UserInterface $author
     * @param string $discussionId
     * @param string $message
     * @return SendMessageRequest
     * @throws DiscussionNotFoundException
     * @throws NotAMemberOfTheDiscussionException
     * @throws SendMessageForbiddenException
     * @throws AssertionFailedException
     */
    public function create(UserInterface $author, string $discussionId, string $message): SendMessageRequest
    {
        if (!in_array('ROLE_USER', $author->getRoles())) {
            throw new SendMessageForbiddenException($author);
        }
        $discussion = $this->discussionGateway->find($discussionId);

        if ($discussion === null) {
            throw new DiscussionNotFoundException($discussionId);
        }

        $discussionMember = $discussion->findDiscussionMemberByEmail($author->getEmail());
        if (!$discussionMember) {
            throw new NotAMemberOfTheDiscussionException();
        }

        Assertion::notBlank($message);

        return new SendMessageRequest($message, $discussionMember->getMember(), $discussion);
    }

}