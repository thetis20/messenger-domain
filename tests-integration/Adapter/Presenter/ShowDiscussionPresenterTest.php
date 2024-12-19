<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Presenter;


use Messenger\Domain\Presenter\ShowDiscussionPresenterInterface;
use Messenger\Domain\Response\ShowDiscussionResponse;

class ShowDiscussionPresenterTest implements ShowDiscussionPresenterInterface
{
    public ShowDiscussionResponse $response;

    public function present(ShowDiscussionResponse $response): void
    {
        $this->response = $response;
    }
}