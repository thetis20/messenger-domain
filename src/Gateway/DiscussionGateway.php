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

    /**
     * @param array{"discussionMembers.member.email"?: string} $filters
     * @return int
     */
    public function countBy(array $filters): int;

    /**
     * find one by id
     * @param string $id
     * @return Discussion|null
     */
    public function find(string $id): ?Discussion;

    /**
     * @param array{"discussionMembers.member.email"?: string} $filters
     * @param array{limit?: int, offset?: int} $options
     * @return Discussion[]
     */
    public function findBy(array $filters, array $options): array;
}
