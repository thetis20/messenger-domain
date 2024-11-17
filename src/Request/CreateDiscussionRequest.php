<?php

namespace Messenger\Domain\Request;

use Assert\Assertion;
use Messenger\Domain\Entity\User;


class CreateDiscussionRequest
{
    private string $name;
    /** @var array<User> */
    private array $users;

    public static function create(string $name, array $users, User $author): CreateDiscussionRequest
    {

        Assertion::notBlank($name);
        $users = array_reduce([...$users, $author], function ($res, ?User $item) {
            if (!$item instanceof User) {
                return $res;
            }
            if (!in_array($item->getId()->toString(), array_map(function (User $user) {
                return $user->getId()->toString();
            }, $res))) {
                $res[] = $item;
            }
            return $res;
        }, []);
        Assertion::minCount($users, 2);
        return new CreateDiscussionRequest($name, $users);
    }
    /**
     * @param string $name
     * @param array $users
     */
    public function __construct(string $name, array $users)
    {
        $this->name = $name;
        $this->users = $users;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsers(): array
    {
        return $this->users;
    }
}
