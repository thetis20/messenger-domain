<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Entity\Message;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Gateway\LoggerInterface;
use Messenger\Domain\Gateway\MessageGateway;
use Messenger\Domain\Gateway\NotificationGateway;
use Messenger\Domain\Presenter\SendMessagePresenterInterface;
use Messenger\Domain\Request\SendMessageRequest;
use Messenger\Domain\Response\SendMessageResponse;

final readonly class SendMessage
{

    public function __construct(
        private MessageGateway      $messageGateway,
        private DiscussionGateway   $discussionGateway,
        private NotificationGateway $notificationGateway,
        private LoggerInterface     $logger)
    {
    }

    public function execute(SendMessageRequest $request, SendMessagePresenterInterface $presenter): void
    {
        $this->notificationGateway->beginTransaction();
        $discussion = $request->getDiscussion();
        $message = Message::fromRequest($request);
        $this->messageGateway->insert($message);
        foreach ($discussion->getDiscussionMembers() as $discussionMember) {
            if ($discussionMember->getMember()->getEmail() === $request->getAuthor()->getEmail()) {
                $discussionMember->markAsSeen();
                continue;
            }
            $discussionMember->markAsUnseen();
            $this->notificationGateway->send('newMessage', $discussionMember->getMember()->getEmail(), [
                'discussion' => $discussion,
                'message' => $message,
                'member' => $discussionMember->getMember()
            ]);
        }

        $this->discussionGateway->update($discussion);
        $presenter->present(new SendMessageResponse($request->getDiscussion(), $message));
        $this->notificationGateway->closeTransaction();
        $this->logger->notice('Send message to discussion', [
            'discussion' => $discussion,
            'user' => $request->getAuthor()->getEmail(),
            'message' => $message]);
    }
}
