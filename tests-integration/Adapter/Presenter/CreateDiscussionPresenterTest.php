<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Presenter;

use Messenger\Domain\Presenter\CreateDiscussionPresenterInterface;
use Messenger\Domain\Response\CreateDiscussionResponse;

class CreateDiscussionPresenterTest implements CreateDiscussionPresenterInterface
{
    public CreateDiscussionResponse $response;

    public function present(CreateDiscussionResponse $response): void
    {
        $this->response = $response;
    }
}