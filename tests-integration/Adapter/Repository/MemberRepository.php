<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\Entity\Member;
use Messenger\Domain\Entity\Message;
use Messenger\Domain\Gateway\MemberGateway;

class MemberRepository implements MemberGateway
{
    /** @var array{members: Member[]}  */
    private array $data;

    /**
     * @param array{members: Member[]} $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function insert(Member $member): void
    {
        $this->data['members'][] = $member;
    }

    public function findOneByEmail(string $email): ?Member
    {
        $index = array_search($email, array_map(function (Member $member) {
            return $member->getEmail();
        }, $this->data['members']));
        return $index === false ? null : $this->data['members'][$index];
    }
}
