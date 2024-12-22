<?php

namespace Messenger\Domain\Gateway;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Entity\Message;
use Messenger\Domain\Exception\NotificationDoesNotExistException;
use Messenger\Domain\Exception\UnclosedTransactionException;
use ReflectionClass;
use ReflectionMethod;

abstract class NotificationGateway
{
    /** @var array<string|array<string|mixed>>[] */
    private array $notifications = [];
    /** @var string[] */
    private array $keys;

    public function __construct()
    {
        $this->keys = [];
        $class = new ReflectionClass(NotificationGateway::class);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (!$method->isAbstract()) {
                continue;
            }
            $this->keys[] = $method->getName();
        }
    }

    /**
     * @throws UnclosedTransactionException
     */
    public function beginTransaction(): void
    {
        if (count($this->notifications) !== 0) {
            // @codeCoverageIgnoreStart
            throw new UnclosedTransactionException();
            // @codeCoverageIgnoreEnd
        }
        $this->notifications = [];
    }

    /**
     * @param string $email
     * @param array{discussion: Discussion, member: Member} $params
     * @return void
     */
    abstract public function invitesDiscussion(string $email, array $params): void;

    /**
     * @param string $email
     * @param array{discussion: Discussion, member: Member} $params
     * @return void
     */
    abstract public function invitesMemberDiscussion(string $email, array $params): void;

    /**
     * @param string $email
     * @param array{discussion: Discussion, member: Member, message: Message} $params
     * @return void
     */
    abstract public function newMessage(string $email, array $params): void;


    /**
     * @param array<string, mixed> $params
     * @throws NotificationDoesNotExistException
     */
    public function send(string $key, string $email, array $params): void
    {
        if (!in_array($key, $this->keys)) {
            // @codeCoverageIgnoreStart
            throw new NotificationDoesNotExistException($key);
            // @codeCoverageIgnoreEnd
        }
        $this->notifications[] = [$key, $email, $params];
    }

    public function closeTransaction(): void
    {
        while (count($this->notifications) !== 0) {
            $notification = array_shift($this->notifications);
            $method = $notification[0];
            $this->$method($notification[1], $notification[2]);
        }
    }
}
