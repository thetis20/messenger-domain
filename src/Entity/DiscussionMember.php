<?php

namespace Messenger\Domain\Entity;

use Messenger\Domain\Request\CreateDiscussionRequest;
use Symfony\Component\Uid\Uuid;

class DiscussionMember
{
    /** @var Discussion */
    private Discussion $discussion;
    /** @var Member */
    private Member $member;
    /** @var bool */
    private bool $seen;

    public function __construct(Discussion $discussion, Member $member, bool $seen = false)
    {
        $this->discussion = $discussion;
        $this->member = $member;
        $this->seen = $seen;
    }

    public function getDiscussion(): Discussion
    {
        return $this->discussion;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function isSeen(): bool
    {
        return $this->seen;
    }

    public function markAsUnseen(): void
    {
        $this->seen = false;
    }

    public function markAsSeen(): void
    {
        $this->seen = false;
    }
}
