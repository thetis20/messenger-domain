<?php

namespace Messenger\Domain\Gateway;

use Messenger\Domain\Entity\Discussion;

interface DiscussionGateway
{
    /**
     * Save discussion
     * @param Discussion $discussion
     * @return void
     */
    public function insert(Discussion $discussion): void;
}
