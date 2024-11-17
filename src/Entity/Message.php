<?php

namespace Messenger\Domain\Entity;

use Messenger\Domain\Request\SendMessageRequest;
use Symfony\Component\Uid\Uuid;

class Message
{
    /** @var Uuid */
    private Uuid $id;
    /** @var UserInterface  */
    private UserInterface $author;
    /** @var string  */
    private string $message;

    public static function fromCreation(SendMessageRequest $request): self
    {
        return new self(
            Uuid::v4(),
            $request->getMessage(),
            $request->getAuthor()
        );
    }

    public function __construct(Uuid $id, string $message, UserInterface $author)
    {
        $this->id = $id;
        $this->message = $message;
        $this->author = $author;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAuthor(): UserInterface
    {
        return $this->author;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
