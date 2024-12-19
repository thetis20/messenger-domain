<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Entity\Message;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Gateway\MessageGateway;
use Messenger\Domain\Presenter\SendMessagePresenterInterface;
use Messenger\Domain\Request\SendMessageRequest;
use Messenger\Domain\Response\SendMessageResponse;

final readonly class SendMessage
{

    public function __construct(
        private MessageGateway $messageGateway,
        private DiscussionGateway $discussionGateway)
    {
    }

    public function execute(SendMessageRequest $request, SendMessagePresenterInterface $presenter): void
    {
        $discussion = $request->getDiscussion();
        $message = Message::fromRequest($request);
        $this->messageGateway->insert($message);

        $discussion->markAsUnseen();
        $discussion->markAsSeen([$request->getAuthor()->getEmail()]);
        $this->discussionGateway->update($discussion);
        $presenter->present(new SendMessageResponse($request->getDiscussion(), $message));
    }
}
