<?php

namespace Messenger\Domain\Entity;

use Messenger\Domain\Request\SendMessageRequest;
use Symfony\Component\Uid\Uuid;

class Message
{
    private Uuid $id;
    private Uuid $discussionId;
    private Member $author;
    private string $message;
    private \DateTime $createdAt;

    public static function fromRequest(SendMessageRequest $request): self
    {
        return new self(
            Uuid::v4(),
            $request->getMessage(),
            $request->getAuthor(),
            $request->getDiscussion()->getId(),
        );
    }

    public function __construct(Uuid $id, string $message, Member $author, Uuid $discussionId, \DateTime $createdAt = new \DateTime())
    {
        $this->id = $id;
        $this->message = $message;
        $this->author = $author;
        $this->discussionId = $discussionId;
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

    public function getDiscussionId(): Uuid
    {
        return $this->discussionId;
    }
}
