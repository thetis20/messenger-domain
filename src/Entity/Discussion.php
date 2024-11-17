<?php

namespace Messenger\Domain\Entity;

use Messenger\Domain\Request\CreateDiscussionRequest;
use Symfony\Component\Uid\Uuid;

class Discussion
{
    /** @var Uuid */
    private Uuid $id;
    /** @var string */
    private string $name;
    /** @var array<UserInterface> */
    private array $members;

    public static function fromCreation(CreateDiscussionRequest $request): self
    {
        return new self(
            Uuid::v4(),
            $request->getName(),
            $request->getUsers()
        );
    }

    public function __construct(Uuid $id, string $name, array $members)
    {
        $this->id = $id;
        $this->name = $name;
        $this->members = $members;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMembers(): array
    {
        return $this->members;
    }

    public function isMember(UserInterface $author): bool
    {
        foreach ($this->members as $member) {
            if ($member->getId() === $author->getId()) {
                return true;
            }
        }
        return false;
    }
}
