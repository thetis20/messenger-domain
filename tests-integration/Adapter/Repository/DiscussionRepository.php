<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\DiscussionMember;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Gateway\DiscussionGateway;
use Symfony\Component\Uid\Uuid;

class DiscussionRepository implements DiscussionGateway
{

    public function insert(Discussion $discussion): void
    {
        $discussions = Data::getInstance()->getDiscussions();
        $discussions[] = $discussion;
        Data::getInstance()->setDiscussions($discussions);
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
        foreach (Data::getInstance()->getDiscussions() as $discussion) {
            if ($discussion->getId()->toString() === $id->toString()) {
                return $discussion;
            }
        }
        return null;
    }

    public function update(Discussion $discussion): void
    {
        $discussions = Data::getInstance()->getDiscussions();
        foreach ($discussions as $key => $d) {
            if ($d->getId()->toString() === $discussion->getId()->toString()) {
                $discussions[$key] = $discussion;
            }
        }
        Data::getInstance()->setDiscussions($discussions);
    }

    public function countBy(array $filters): int
    {
        $count = 0;
        foreach (Data::getInstance()->getDiscussions() as $discussion) {

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

    public function findBy(array $filters, array $options): array
    {
        $offset = $options['offset'] ?? 0;
        $limit = $options['limit'] ?? 10;
        $discussions = [];
        foreach (Data::getInstance()->getDiscussions() as $discussion) {
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
        }, Data::getInstance()->getDiscussions()));
        return $index === false ? null : Data::getInstance()->getDiscussions()[$index];
    }
}
