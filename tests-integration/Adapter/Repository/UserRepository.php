<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;

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
