<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Entity\Message;
use Messenger\Domain\Gateway\MessageGateway;
use Messenger\Domain\Presenter\SendMessagePresenterInterface;
use Messenger\Domain\Request\SendMessageRequest;
use Messenger\Domain\Response\SendMessageResponse;

class SendMessage
{
    private MessageGateway $messageGateway;

    public function __construct(MessageGateway $messageGateway)
    {
        $this->messageGateway = $messageGateway;
    }

    public function execute(SendMessageRequest $request, SendMessagePresenterInterface $presenter): void
    {
        $message = Message::fromCreation($request);
        $this->messageGateway->insert($message);
        $presenter->present(new SendMessageResponse($request->getDiscussion(), $message));
    }
}
