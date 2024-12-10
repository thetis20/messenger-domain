<?php

namespace Messenger\Domain\TestsIntegration\Entity;

use Messenger\Domain\Entity\UserInterface;
use Symfony\Component\Uid\Uuid;

class User implements UserInterface
{
    /** @var string */
    private string $email;
    /** @var string */
    private string $username;

    public function __construct(string $email, string $username)
    {
        $this->email = $email;
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getUsualName(): string
    {
        return $this->username;
    }
}
