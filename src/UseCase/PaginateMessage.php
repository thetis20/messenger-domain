<?php

namespace Messenger\Domain\UseCase;

use Messenger\Domain\Gateway\MessageGateway;
use Messenger\Domain\Presenter\PaginateMessagePresenterInterface;
use Messenger\Domain\Request\PaginateMessageRequest;
use Messenger\Domain\Response\PaginateMessageResponse;

class PaginateMessage
{
    private MessageGateway $messageGateway;

    public function __construct(
        MessageGateway    $messageGateway)
    {
        $this->messageGateway = $messageGateway;
    }

    public function execute(PaginateMessageRequest $request, PaginateMessagePresenterInterface $presenter): void
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
        $presenter->present(new PaginateMessageResponse(
            $request->getDiscussion(),
            $messages,
            $total,
            $request->getLimit(),
            $request->getPage()));
    }
}
