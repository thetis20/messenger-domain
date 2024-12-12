<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Gateway\DiscussionGateway;
use Messenger\Domain\Presenter\PaginateDiscussionPresenterInterface;
use Messenger\Domain\Request\PaginateDiscussionRequest;
use Messenger\Domain\Response\PaginateDiscussionResponse;

class PaginateDiscussion
{
    /** @var DiscussionGateway */
    private DiscussionGateway $discussionGateway;

    public function __construct(DiscussionGateway $discussionGateway)
    {
        $this->discussionGateway = $discussionGateway;
    }

    public function execute(PaginateDiscussionRequest $request, PaginateDiscussionPresenterInterface $presenter): void
    {
        $options = [
            'offset' => ($request->getPage() - 1) * $request->getLimit(),
            'limit' => $request->getLimit(),
        ];
        $filters = [
            'discussionMembers.member.email' => $request->getUser()->getEmail(),
        ];
        $total = $this->discussionGateway->countBy($filters);
        $discussions = $this->discussionGateway->findBy($filters, $options);
        $presenter->present(new PaginateDiscussionResponse($discussions, $total, $request->getLimit(), $request->getPage()));
    }
}
