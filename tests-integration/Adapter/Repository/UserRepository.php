<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\TestsIntegration\Entity\User;

class UserRepository
{
    /** @var array{users: User[]}  */
    private array $data;

    /**
     * @param array{users: User[]} $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function findOneByUsername(string $username): ?User
    {
        $index = array_search($username, array_map(function ($user) {
            return $user->getUsername();
        }, $this->data['users']));
        return $index === false ? null : $this->data['users'][$index];
    }
}
