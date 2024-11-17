<?php

namespace Messenger\Domain\TestsIntegration\Entity;

use Messenger\Domain\Entity\UserInterface;
use Symfony\Component\Uid\Uuid;

class User implements UserInterface
{
    /** @var Uuid */
    private Uuid $id;
    /** @var string */
    private string $email;
    /** @var string */
    private string $username;
    /** @var string */
    private string $password;

    public function __construct(Uuid $id, string $email, string $username, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
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

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
