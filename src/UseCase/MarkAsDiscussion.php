<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Gateway\LoggerInterface;
use Messenger\Domain\Presenter\MarkAsDiscussionPresenterInterface;
use Messenger\Domain\Request\MarkAsDiscussionRequest;
use Messenger\Domain\Response\MarkAsDiscussionResponse;

final readonly class MarkAsDiscussion
{

    public function __construct(
        private DiscussionGateway $discussionGateway,
        private LoggerInterface   $logger)
    {
    }

    public function execute(MarkAsDiscussionRequest $request, MarkAsDiscussionPresenterInterface $presenter): void
    {
        $discussion = $request->getDiscussion();
        $discussion->markAs($request->isSeen(), [$request->getAuthor()->getEmail()]);
        $this->discussionGateway->update($discussion);

        $presenter->present(new MarkAsDiscussionResponse($request->getDiscussion()));
        $this->logger->notice('Mark discussion as seen', [
            'discussion' => $discussion,
            'user' => $request->getAuthor()->getEmail()]);
    }
}
