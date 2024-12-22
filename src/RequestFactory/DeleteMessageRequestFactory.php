<?php

namespace Messenger\Domain\RequestFactory;

use Messenger\Domain\Entity\UserInterface;
use Messenger\Domain\Exception\DeleteMessageForbiddenException;
use Messenger\Domain\Exception\DiscussionNotFoundException;
use Messenger\Domain\Exception\MarkAsDiscussionForbiddenException;
use Messenger\Domain\Exception\MessageNotFoundException;
use Messenger\Domain\Exception\NotAMemberOfTheDiscussionException;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Gateway\MessageGateway;
use Messenger\Domain\Request\DeleteMessageRequest;
use Messenger\Domain\Request\MarkAsDiscussionRequest;

final readonly class DeleteMessageRequestFactory
{
    public function __construct(private MessageGateway $messageGateway)
    {
    }

    /**
     * @param UserInterface $author
     * @param string $messageId
     * @return DeleteMessageRequest
     * @throws DeleteMessageForbiddenException
     * @throws MessageNotFoundException
     */
    public function create(UserInterface $author, string $messageId): DeleteMessageRequest
    {
        if (!in_array('ROLE_USER', $author->getRoles())) {
            throw new DeleteMessageForbiddenException($author);
        }
        $message = $this->messageGateway->find($messageId);

        if ($message === null) {
            throw new MessageNotFoundException($messageId);
        }
        if ($message->getAuthor()->getEmail() !== $author->getEmail()) {
            throw new DeleteMessageForbiddenException($author, $message);
        }

        return new DeleteMessageRequest($author, $message);
    }

}