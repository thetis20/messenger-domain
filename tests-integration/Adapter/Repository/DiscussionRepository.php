<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\DiscussionMember;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Gateway\DiscussionGateway;
use Symfony\Component\Uid\Uuid;

class DiscussionRepository implements DiscussionGateway
{
    /** @var array{discussions: Discussion[]} */
    private array $data;

    /**
     * @param array{discussions: Discussion[]} $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function insert(Discussion $discussion): void
    {
        $this->data['discussions'][] = $discussion;
    }

    /**
     * @param Uuid $id
     * @return Discussion|null
     */
    public function find(string|Uuid $id): ?Discussion
    {
        if (!$id instanceof Uuid) {
            $id = new Uuid($id);
        }
        foreach ($this->data['discussions'] as $discussion) {
            if ($discussion->getId()->toString() === $id->toString()) {
                return $discussion;
            }
        }
        return null;
    }

    public function update(Discussion $discussion): void
    {
        foreach ($this->data['discussions'] as $key => $d) {
            if ($d->getId()->toString() === $discussion->getId()->toString()) {
                $this->data['discussions'][$key] = $discussion;
            }
        }
    }

    public function countBy(array $filters): int
    {
        $count = 0;
        foreach ($this->data['discussions'] as $discussion) {

            if (isset($filters['discussionMembers.member.email']) &&
                !in_array($filters['discussionMembers.member.email'],
                    array_map(function (DiscussionMember $discussionMember) {
                        return $discussionMember->getMember()->getEmail();
                    }, $discussion->getDiscussionMembers()))) {
                continue;
            }
            $count++;
        }
        return $count;
    }

    /**
     * @param array{"discussionMembers.member.email"?: string} $filters
     * @param array{offset?: int, limit?: int} $options
     * @return Discussion[]
     */
    public function findBy(array $filters, array $options): array
    {
        $offset = $options['offset'] ?? 0;
        $limit = $options['limit'] ?? 10;
        $discussions = [];
        foreach ($this->data['discussions'] as $discussion) {
            if (isset($filters['discussionMembers.member.email']) &&
                !in_array($filters['discussionMembers.member.email'],
                    array_map(function (DiscussionMember $discussionMember) {
                        return $discussionMember->getMember()->getEmail();
                    }, $discussion->getDiscussionMembers()))) {
                continue;
            }
            if ($offset > 0) {
                $offset--;
                continue;
            }
            $discussions[] = $discussion;
            if (count($discussions) === $limit) {
                break;
            }
        }
        return $discussions;
    }


    public function findOneById(string $id): ?Discussion
    {
        $index = array_search($id, array_map(function (Discussion $discussion) {
            return $discussion->getId()->toString();
        }, $this->data['discussions']));
        return $index === false ? null : $this->data['discussions'][$index];
    }
}
