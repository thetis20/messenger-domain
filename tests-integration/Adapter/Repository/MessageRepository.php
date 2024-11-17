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
}
