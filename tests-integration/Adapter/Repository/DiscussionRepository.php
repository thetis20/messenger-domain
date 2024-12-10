<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Repository;

use Messenger\Domain\Entity\Discussion;
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
        foreach ($discussions as $key=>$d) {
            if ($d->getId()->toString() === $discussion->getId()->toString()) {
                $discussions[$key] = $discussion;
            }
        }
        Data::getInstance()->setDiscussions($discussions);
    }
}
