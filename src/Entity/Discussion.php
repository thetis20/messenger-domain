<?php

namespace Messenger\Domain\Entity;

use Symfony\Component\Uid\Uuid;

class Discussion implements \JsonSerializable
{
    private Uuid $id;
    private string $name;
    /** @var DiscussionMember[] */
    private array $discussionMembers;
    private Member $author;

    public function __construct(Member $author, Uuid $id, string $name)
    {
        $this->author = $author;
        $this->id = $id;
        $this->name = $name;
        $this->discussionMembers = [new DiscussionMember($this, $author, true)];
    }

    public function getAuthor(): Member
    {
        return $this->author;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return DiscussionMember[]
     */
    public function getDiscussionMembers(): array
    {
        return $this->discussionMembers;
    }

    public function isMember(string $email): bool
    {
        return (bool)$this->findDiscussionMemberByEmail($email);
    }

    public function findDiscussionMemberByEmail(string $email): ?DiscussionMember
    {
        foreach ($this->discussionMembers as $discussionMembers) {
            if ($discussionMembers->getMember()->getEmail() === $email) {
                return $discussionMembers;
            }
        }
        return null;
    }

    public function addMember(Member $member, bool $seen = false): void
    {
        if ($this->isMember($member->getEmail())) {
            return;
        }
        $discussionMember = new DiscussionMember($this, $member, $seen);
        $this->discussionMembers[] = $discussionMember;
    }

    /**
     * @param string[]|null $emails if null it marks all member as unseen
     * @return void
     */
    public function markAsUnseen(?array $emails = null): void
    {
        foreach ($this->getDiscussionMembers() as $discussionMember) {
            if ($emails === null || in_array($discussionMember->getMember()->getEmail(), $emails)) {
                $discussionMember->markAsUnseen();
            }
        }
    }

    /**
     * @param string[]|null $emails if null it marks all member as seen
     * @return void
     */
    public function markAsSeen(?array $emails = null): void
    {
        foreach ($this->getDiscussionMembers() as $discussionMember) {
            if ($emails === null || in_array($discussionMember->getMember()->getEmail(), $emails)) {
                $discussionMember->markAsSeen();
            }
        }
    }

    /**
     * @param bool $seen
     * @param string[]|null $emails if null it marks all member as seen
     * @return void
     */
    public function markAs(bool $seen, ?array $emails = null): void
    {
        if ($seen) {
            $this->markAsSeen($emails);
        } else {
            $this->markAsUnseen($emails);
        }
    }

    /**
     * @return array{id: string, name: string, discussionMembers: array{memberEmail: string, seen: bool}[]}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->__toString(),
            'name' => $this->name,
            'discussionMembers' => array_map(function (DiscussionMember $discussionMember) {
                return [
                    'memberEmail' => $discussionMember->getMember()->getEmail(),
                    'seen' => $discussionMember->isSeen()
                ];
            }, $this->discussionMembers),
        ];
    }
}
