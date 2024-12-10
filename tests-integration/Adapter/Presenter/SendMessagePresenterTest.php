<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Presenter;

use Messenger\Domain\Presenter\CreateDiscussionPresenterInterface;
use Messenger\Domain\Presenter\SendMessagePresenterInterface;
use Messenger\Domain\Response\CreateDiscussionResponse;
use Messenger\Domain\Response\SendMessageResponse;

class SendMessagePresenterTest implements SendMessagePresenterInterface
{
    public SendMessageResponse $response;

    public function present(SendMessageResponse $response): void
    {
        $this->response = $response;
    }
}