<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Presenter\MarkAsPresenterInterface;
use Messenger\Domain\Request\MarkAsRequest;
use Messenger\Domain\Response\MarkAsDiscussionResponse;

final readonly class MarkAsDiscussion
{

    public function __construct(private DiscussionGateway $discussionGateway)
    {
    }

    public function execute(MarkAsRequest $request, MarkAsPresenterInterface $presenter): void
    {
        $discussion = $request->getDiscussion();
        $discussion->markAs($request->isSeen(), [$request->getAuthor()->getEmail()]);
        $this->discussionGateway->update($discussion);

        $presenter->present(new MarkAsDiscussionResponse($request->getDiscussion()));
    }
}
