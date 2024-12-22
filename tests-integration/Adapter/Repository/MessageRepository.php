<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Entity\Message;
use Messenger\Domain\Gateway\MessageGateway;
use Messenger\Domain\TestsIntegration\Entity\User;
use Symfony\Component\Uid\Uuid;

class MessageRepository implements MessageGateway
{
    /** @var array{users: User[], discussions:Discussion[], members: Member[], messages: Message[]} */
    private array $data;

    /**
     * @param array{users: User[], discussions:Discussion[], members: Member[], messages: Message[]} $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function insert(Message $message): void
    {
        $this->data['messages'][] = $message;
    }

    public function countBy(array $filters): int
    {
        $count = 0;
        foreach ($this->data['messages'] as $message) {

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
        foreach ($this->data['messages'] as $message) {
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

    public function update(Message $message): void
    {
        foreach ($this->data['messages'] as $key => $m) {
            if ($m->getId()->toString() === $message->getId()->toString()) {
                $this->data['messages'][$key] = $message;
            }
        }
    }

    public function find(string $id): ?Message
    {
        foreach ($this->data['messages'] as $message) {
            if ($message->getId()->toString() === $id) {
                return $message;
            }
        }
        return null;
    }
}
