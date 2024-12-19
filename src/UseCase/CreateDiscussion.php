<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\DiscussionMember;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Gateway\MemberGateway;
use Messenger\Domain\Presenter\CreateDiscussionPresenterInterface;
use Messenger\Domain\Request\CreateDiscussionRequest;
use Messenger\Domain\Response\CreateDiscussionResponse;
use Symfony\Component\Uid\Uuid;

final readonly class CreateDiscussion
{

    public function __construct(
        private DiscussionGateway $discussionGateway,
        private MemberGateway     $memberGateway)
    {
    }

    public function execute(CreateDiscussionRequest $request, CreateDiscussionPresenterInterface $presenter): void
    {
        $discussion = new Discussion(Uuid::v4(), $request->getName());

        $authorMember = $this->memberGateway->findOneByEmail($request->getAuthor()->getEmail());
        if (!$authorMember) {
            $authorMember = new Member(
                $request->getAuthor()->getEmail(),
                $request->getAuthor()->getUserIdentifier(),
                $request->getAuthor()->getUsualName());
            $this->memberGateway->insert($authorMember);
        }
        $discussion->addMember($authorMember, true);

        foreach ($request->getEmails() as $ref) {
            $member = $this->memberGateway->findOneByEmail($ref);
            if (!$member) {
                $member = new Member($ref);
                $this->memberGateway->insert($member);
            }
            $discussion->addMember($member);
        }

        $this->discussionGateway->insert($discussion);
        $presenter->present(new CreateDiscussionResponse($discussion));
    }
}
