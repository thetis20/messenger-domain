<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Entity\Message;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Gateway\MessageGateway;
use Messenger\Domain\Presenter\SendMessagePresenterInterface;
use Messenger\Domain\Request\SendMessageRequest;
use Messenger\Domain\Response\SendMessageResponse;

class SendMessage
{
    private MessageGateway $messageGateway;
    private DiscussionGateway $discussionGateway;

    public function __construct(MessageGateway $messageGateway, DiscussionGateway $discussionGateway)
    {
        $this->messageGateway = $messageGateway;
        $this->discussionGateway = $discussionGateway;
    }

    public function execute(SendMessageRequest $request, SendMessagePresenterInterface $presenter): void
    {
        $message = Message::fromCreation($request);
        $this->messageGateway->insert($message);
        $discussion = $request->getDiscussion();
        $discussion->markAsUnseen();
        $discussion->markAsSeen([$request->getAuthor()->getEmail()]);
        $this->discussionGateway->update($discussion);
        $presenter->present(new SendMessageResponse($request->getDiscussion(), $message));
    }
}
