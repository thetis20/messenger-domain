<?php

namespace Messenger\Domain\Request;

use Assert\Assert;
use Messenger\Domain\Entity\UserInterface;
use Messenger\Domain\Exception\CreateDiscussionForbiddenException;

class CreateDiscussionRequest
{
    /** @var string */
    private string $name;
    /** @var array<string> $emails emails */
    private array $emails;
    /** @var UserInterface */
    private UserInterface $author;

    /**
     * @param string $name
     * @param string[] $emails
     * @param UserInterface $author
     */
    public function __construct(string $name, array $emails, UserInterface $author)
    {
        $this->name = $name;
        $this->emails = $emails;
        $this->author = $author;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getEmails(): array
    {
        return $this->emails;
    }

    public function getAuthor(): UserInterface
    {
        return $this->author;
    }
}
