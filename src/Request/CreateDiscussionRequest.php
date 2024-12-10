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
     * @param string[] $emails members' email
     * @param UserInterface $author
     * @return CreateDiscussionRequest
     * @throws CreateDiscussionForbiddenException
     */
    public static function create(string $name, array $emails, UserInterface $author): CreateDiscussionRequest
    {
        if (!in_array('ROLE_USER', $author->getRoles())) {
            throw new CreateDiscussionForbiddenException($author);
        }

        Assert::lazy()
            ->that($name, 'name')->tryAll()->notEmpty()->string()
            ->that($author->getEmail(), 'author.email')->tryAll()->notInArray($emails)
            ->that($emails, 'emails')->tryAll()->minCount(1)->verifyNow();
        $assertion = Assert::lazy();
        foreach ($emails as $key => $email) {
            $assertion->that($email, 'emails.' . $key)->tryAll()
                ->string()->notEmpty()
                ->email();
        }
        $assertion->verifyNow();

        return new CreateDiscussionRequest($name, $emails, $author);
    }

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
