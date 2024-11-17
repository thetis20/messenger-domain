<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\TestsIntegration\Entity\User;

class UserRepository
{
    public function findOneByUsername(string $username): ?User
    {
        $index = array_search($username, array_map(function ($user){
            return $user->getUsername();
        },Data::getInstance()->getUsers()));
        return $index === false ? null : Data::getInstance()->getUsers()[$index];
    }
}
