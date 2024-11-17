<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Presenter\CreateDiscussionPresenterInterface;
use Messenger\Domain\Request\CreateDiscussionRequest;
use Messenger\Domain\Response\CreateDiscussionResponse;

class CreateDiscussion
{
    private DiscussionGateway $discussionGateway;

    public function __construct(DiscussionGateway $discussionGateway)
    {
        $this->discussionGateway = $discussionGateway;
    }

    public function execute(CreateDiscussionRequest $request, CreateDiscussionPresenterInterface $presenter): void
    {
        $discussion = Discussion::fromCreation($request);
        $this->discussionGateway->insert($discussion);
        $presenter->present(new CreateDiscussionResponse($discussion));
    }
}
