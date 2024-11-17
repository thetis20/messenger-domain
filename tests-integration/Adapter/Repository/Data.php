<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;

class Data
{
    private static Data $instance;
    private array $discussions;
    private array $users;
    private array $messages;

    public static function getInstance(): Data
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->users = [
            new User(
                new Uuid('2a5f7dd5-26d7-4f1b-a8ec-3c5ea9ab66f6'),
                'username@email.com',
                'username',
                password_hash('password', PASSWORD_DEFAULT)),
            new User(
                Uuid::v4(),
                'username1@email.com',
                'username1',
                password_hash('password', PASSWORD_DEFAULT))
        ];
        $this->discussions = [new Discussion(
            new Uuid('3feb781c-8a9d-4650-8390-99aaa60efcba'),
            'discussion 1',
            [$this->users[0]]
        )];
        $this->messages = [];
    }

    public function getDiscussions(): array
    {
        return $this->discussions;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public static function setInstance(Data $instance): void
    {
        self::$instance = $instance;
    }

    public function setDiscussions(array $discussions): void
    {
        $this->discussions = $discussions;
    }

    public function setUsers(array $users): void
    {
        $this->users = $users;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }
}
