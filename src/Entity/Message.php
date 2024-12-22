<?php

namespace Messenger\Domain\Entity;

use Messenger\Domain\Request\SendMessageRequest;
use Symfony\Component\Uid\Uuid;

class Message implements \JsonSerializable
{
    private Uuid $id;
    private Uuid $discussionId;
    private Member $author;
    private ?string $message;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private bool $deleted;

    public static function fromRequest(SendMessageRequest $request): self
    {
        return new self(
            Uuid::v4(),
            $request->getMessage(),
            $request->getAuthor(),
            $request->getDiscussion()->getId(),
        );
    }

    public function __construct(
        Uuid      $id,
        ?string   $message,
        Member    $author,
        Uuid      $discussionId,
        \DateTime $createdAt = new \DateTime(),
        \DateTime $updatedAt = new \DateTime(),
        bool      $deleted = false)
    {
        $this->id = $id;
        $this->message = $message;
        $this->author = $author;
        $this->discussionId = $discussionId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deleted = $deleted;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAuthor(): Member
    {
        return $this->author;
    }

    public function getMessage(): ?string
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

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @return array{id: string, message: string, authorEmail: string, discussionId: string, createdAt: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'authorEmail' => $this->author->getEmail(),
            'discussionId' => $this->discussionId,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    public function delete(): static
    {
        $this->deleted = true;
        $this->message = null;
        $this->updatedAt = new \DateTime();
        return $this;
    }
}
