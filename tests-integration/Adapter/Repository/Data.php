<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\Entity\Member;
use Messenger\Domain\Entity\Message;
use Messenger\Domain\TestsIntegration\Entity\User;
use Messenger\Domain\Entity\Discussion;
use Symfony\Component\Uid\Uuid;

final class Data
{
    private static Data $instance;
    /** @var Discussion[] */
    private array $discussions;
    /** @var User[] */
    private array $users;
    /** @var Message[] */
    private array $messages;
    /** @var Member[] */
    private array $members;

    public static function getInstance(): Data
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->users = [new User('username@email.com', 'username')];
        for ($i = 1; $i <= 10; $i++) {
            $this->users[] = new User("username$i@email.com", "username$i");
        }
        $this->members = [new Member('username@email.com', 'username', 'username')];

        for ($i = 1; $i <= 10; $i++) {
            $this->members[] = new Member("username$i@email.com", "username$i", "username$i");
        }
        $this->discussions = [];

        for ($i = 1; $i <= 10; $i++) {
            for ($y = 1; $y <= 10; $y++) {
                if ($i > $y) {
                    continue;
                }
                $discussion = new Discussion(Uuid::v4(), "discussion $i/$y");
                $discussion->addMember($this->members[$i]);
                $discussion->addMember($this->members[$y]);
                $this->discussions[] = $discussion;
            }
        }
        $this->messages = [];
    }

    /**
     * @return Discussion[]
     */
    public function getDiscussions(): array
    {
        return $this->discussions;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    public static function setInstance(Data $instance): void
    {
        self::$instance = $instance;
    }

    /**
     * @param Discussion[] $discussions
     * @return void
     */
    public function setDiscussions(array $discussions): void
    {
        $this->discussions = $discussions;
    }

    /**
     * @param User[] $users
     * @return void
     */
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param Message[] $messages
     * @return void
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    /**
     * @return Member[]
     */
    public function getMembers(): array
    {
        return $this->members;
    }

    /**
     * @param Member[] $members
     * @return void
     */
    public function setMembers(array $members): void
    {
        $this->members = $members;
    }
}
