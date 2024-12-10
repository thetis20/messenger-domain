<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\Entity\Member;
use Messenger\Domain\Gateway\MemberGateway;

class MemberRepository implements MemberGateway
{

    public function insert(Member $member): void
    {
        $members = Data::getInstance()->getMembers();
        $members[] = $member;
        Data::getInstance()->setMembers($members);
    }

    public function findOneByEmail(string $email): ?Member
    {
        $index = array_search($email, array_map(function (Member $member) {
            return $member->getEmail();
        }, Data::getInstance()->getMembers()));
        return $index === false ? null : Data::getInstance()->getMembers()[$index];
    }
}
