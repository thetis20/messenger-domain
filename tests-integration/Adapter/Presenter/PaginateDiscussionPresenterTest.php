<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Presenter;


use Messenger\Domain\Presenter\PaginateDiscussionPresenterInterface;
use Messenger\Domain\Response\PaginateDiscussionResponse;

class PaginateDiscussionPresenterTest implements PaginateDiscussionPresenterInterface
{
    public PaginateDiscussionResponse $response;

    public function present(PaginateDiscussionResponse $response): void
    {
        $this->response = $response;
    }
}