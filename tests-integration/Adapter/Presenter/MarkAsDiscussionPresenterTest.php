<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Presenter;

use Messenger\Domain\Presenter\CreateDiscussionPresenterInterface;
use Messenger\Domain\Presenter\MarkAsPresenterInterface;
use Messenger\Domain\Presenter\SendMessagePresenterInterface;
use Messenger\Domain\Response\CreateDiscussionResponse;
use Messenger\Domain\Response\MarkAsDiscussionResponse;
use Messenger\Domain\Response\SendMessageResponse;

class MarkAsDiscussionPresenterTest implements MarkAsPresenterInterface
{
    public MarkAsDiscussionResponse $response;

    public function present(MarkAsDiscussionResponse $response): void
    {
        $this->response = $response;
    }
}