<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Gateway\LoggerInterface;
use Messenger\Domain\Gateway\MemberGateway;
use Messenger\Domain\Gateway\NotificationGateway;
use Messenger\Domain\Presenter\CreateDiscussionPresenterInterface;
use Messenger\Domain\Request\CreateDiscussionRequest;
use Messenger\Domain\Response\CreateDiscussionResponse;
use Symfony\Component\Uid\Uuid;

final readonly class CreateDiscussion
{

    public function __construct(
        private DiscussionGateway   $discussionGateway,
        private MemberGateway       $memberGateway,
        private NotificationGateway $notificationGateway,
        private LoggerInterface     $logger)
    {
    }

    public function execute(CreateDiscussionRequest $request, CreateDiscussionPresenterInterface $presenter): void
    {
        $this->notificationGateway->beginTransaction();

        $authorMember = $this->memberGateway->findOneByEmail($request->getAuthor()->getEmail());
        if (!$authorMember) {
            $authorMember = new Member(
                $request->getAuthor()->getEmail(),
                $request->getAuthor()->getUserIdentifier(),
                $request->getAuthor()->getUsualName());
            $this->memberGateway->insert($authorMember);
        }

        $discussion = new Discussion($authorMember, Uuid::v4(), $request->getName());

        foreach ($request->getEmails() as $ref) {
            $member = $this->memberGateway->findOneByEmail($ref);
            if (!$member) {
                $member = new Member($ref);
                $this->memberGateway->insert($member);
                $this->notificationGateway->send('invitesDiscussion', $member->getEmail(), [
                    'member' => $member,
                    'discussion' => $discussion]);
                $discussion->addMember($member);
                continue;
            }
            $discussion->addMember($member);
            $this->notificationGateway->send('invitesMemberDiscussion', $member->getEmail(), [
                'member' => $member,
                'discussion' => $discussion]);
        }

        $this->discussionGateway->insert($discussion);
        $presenter->present(new CreateDiscussionResponse($discussion));
        $this->logger->notice('Discussion created.', [
            'discussion' => $discussion,
            'user' => $request->getAuthor()->getEmail()]);
        $this->notificationGateway->closeTransaction();
    }
}
