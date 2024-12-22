<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Entity\Message;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Gateway\LoggerInterface;
use Messenger\Domain\Gateway\MessageGateway;
use Messenger\Domain\Presenter\DeleteMessagePresenterInterface;
use Messenger\Domain\Request\DeleteMessageRequest;
use Messenger\Domain\Response\DeleteMessageResponse;

final readonly class DeleteMessage
{

    public function __construct(
        private MessageGateway $messageGateway,
        private LoggerInterface   $logger)
    {
    }

    public function execute(DeleteMessageRequest $request, DeleteMessagePresenterInterface $presenter): void
    {
        $message = $request->getMessage()->delete();
        $this->messageGateway->update($message);

        $presenter->present(new DeleteMessageResponse($message));
        $this->logger->notice('Delete message', [
            'message' => $message,
            'user' => $request->getAuthor()->getEmail()]);
    }
}
