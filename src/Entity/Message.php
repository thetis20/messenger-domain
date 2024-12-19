<?php

namespace Messenger\Domain\Entity;

use Messenger\Domain\Request\SendMessageRequest;
use Symfony\Component\Uid\Uuid;

class Message
{
    private Uuid $id;
    private ?Discussion $discussion;
    private Member $author;
    private string $message;
    private \DateTime $createdAt;

    public static function fromRequest(SendMessageRequest $request): self
    {
        return new self(
            Uuid::v4(),
            $request->getMessage(),
            $request->getAuthor(),
            $request->getDiscussion()
        );
    }

    public function __construct(Uuid $id, string $message, Member $author, ?Discussion $discussion, \DateTime $createdAt = new \DateTime())
    {
        $this->id = $id;
        $this->message = $message;
        $this->author = $author;
        $this->discussion = $discussion;
        $this->createdAt = $createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAuthor(): Member
    {
        return $this->author;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getDiscussion(): Discussion
    {
        if (!$this->discussion) {
            throw new \RuntimeException('Discussion not set');
        }
        return $this->discussion;
    }
}
