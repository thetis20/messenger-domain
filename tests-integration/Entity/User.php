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
    /**
     * @var string[]
     */
    private array $roles;

    /**
     * @param string $email
     * @param string $username
     * @param string[] $roles
     */
    public function __construct(string $email, string $username, array $roles = ['ROLE_USER'])
    {
        $this->email = $email;
        $this->username = $username;
        $this->roles = $roles;
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
        return $this->roles;
    }

    public function getUsualName(): string
    {
        return $this->username;
    }
}
