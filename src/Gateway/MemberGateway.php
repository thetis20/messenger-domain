<?php

namespace Messenger\Domain\Gateway;

use Messenger\Domain\Entity\Member;

interface MemberGateway
{
    /**
     * @param Member $member
     * @return void
     */
    public function insert(Member $member): void;

    /**
     * find one member by email
     * @param string $email
     * @return Member|null
     */
    public function findOneByEmail(string $email): ?Member;
}
