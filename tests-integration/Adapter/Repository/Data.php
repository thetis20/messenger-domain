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
        $discussionIds = [
            "5142abe2-21e2-4363-ba31-d0271f94824e",
            "be5807da-3097-4e51-8e9b-885f58b076eb",
            "d4a872f8-bc04-4b90-a789-0d5935e404f1",
            "ea0cb21c-330c-41d9-82b5-a6a1f9ea940c",
            "8dada3a9-f7fa-488c-9657-c4caa3fc2a35",
            "2eaded3c-6a0e-46b7-877e-555f9fc26740",
            "4e313947-e49c-4932-aeb8-72cca5eca918",
            "de9831b1-fe68-4a6a-891a-3e94367392a3",
            "53b9b266-bbaf-4042-97a7-7e34ba075685",
            "ae181ebf-4742-4cb9-a974-9f63b91373af",
            "45eb17ea-e3f5-414a-adbc-b807705ab3d9"];
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
                $discussion = new Discussion(
                    isset($discussionIds[$i + $y - 2]) ? new Uuid($discussionIds[$i + $y - 2]) : Uuid::v4(),
                    "discussion $i/$y");
                $discussion->addMember($this->members[$i]);
                $discussion->addMember($this->members[$y]);
                $this->discussions[] = $discussion;
            }
        }
        $this->messages = [];
        foreach ($this->discussions as $discussion) {
            $countMembers = count($discussion->getDiscussionMembers());
            for ($i = 1; $i <= 10; $i++) {
                $this->messages[] = new Message(
                    Uuid::v4(),
                    'message ' . $i,
                    $discussion->getDiscussionMembers()[$i % $countMembers]->getMember(),
                    $discussion->getId()
                );
            }
        }

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
