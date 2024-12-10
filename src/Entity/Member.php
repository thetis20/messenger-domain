<?php

namespace Messenger\Domain\Entity;

use Messenger\Domain\Request\CreateDiscussionRequest;
use Symfony\Component\Uid\Uuid;

class Member
{
    /** @var string */
    private string $email;
    /** @var string|null */
    private ?string $userIdentifier;
    /** @var string|null */
    private ?string $username;

    public function __construct(string $email, ?string $userIdentifier = null, ?string $username = null)
    {
        $this->email = $email;
        $this->userIdentifier = $userIdentifier;
        $this->username = $username;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}
