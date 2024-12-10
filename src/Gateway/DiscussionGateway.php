<?php

namespace Messenger\Domain\Gateway;

use Messenger\Domain\Entity\Discussion;

interface DiscussionGateway
{
    /**
     * Save discussion & discussionMembers related to its
     * @param Discussion $discussion
     * @return void
     */
    public function insert(Discussion $discussion): void;
    /**
     * Update discussion & discussionMembers related to its
     * @param Discussion $discussion
     * @return void
     */
    public function update(Discussion $discussion): void;
}
