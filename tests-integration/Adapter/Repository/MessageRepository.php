<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\Entity\Message;
use Messenger\Domain\Gateway\MessageGateway;

class MessageRepository implements MessageGateway
{

    public function insert(Message $message): void
    {
        $messages = Data::getInstance()->getMessages();
        $messages[] = $message;
        Data::getInstance()->setMessages($messages);
    }

    public function countBy(array $filters): int
    {
        $count = 0;
        foreach (Data::getInstance()->getMessages() as $message) {

            if (isset($filters['discussion.id']) &&
                $filters['discussion.id'] !== $message->getDiscussionId()->toString()) {
                continue;
            }
            $count++;
        }
        return $count;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $filters, array $options): array
    {
        $offset = $options['offset'] ?? 0;
        $limit = $options['limit'] ?? 10;
        $messages = [];
        foreach (Data::getInstance()->getMessages() as $message) {
            if (isset($filters['discussion.id']) &&
                $filters['discussion.id'] !== $message->getDiscussionId()->toString()) {
                continue;
            }
            if ($offset > 0) {
                $offset--;
                continue;
            }
            $messages[] = $message;
            if (count($messages) === $limit) {
                break;
            }
        }
        return $messages;
    }
}
