<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Gateway\MessageGateway;
use Messenger\Domain\Presenter\ShowDiscussionPresenterInterface;
use Messenger\Domain\Request\ShowDiscussionRequest;
use Messenger\Domain\Response\ShowDiscussionResponse;

final readonly class ShowDiscussion
{
    public function __construct(
        private MessageGateway $messageGateway)
    {
    }

    public function execute(ShowDiscussionRequest $request, ShowDiscussionPresenterInterface $presenter): void
    {
        $options = [
            'offset' => ($request->getPage() - 1) * $request->getLimit(),
            'limit' => $request->getLimit(),
        ];
        $filters = [
            'discussion.id' => $request->getDiscussion()->getId()->toString(),
        ];
        $total = $this->messageGateway->countBy($filters);
        $messages = $this->messageGateway->findBy($filters, $options);
        $presenter->present(new ShowDiscussionResponse(
            $request->getDiscussion(),
            $messages,
            $total,
            $request->getLimit(),
            $request->getPage()));
    }
}
