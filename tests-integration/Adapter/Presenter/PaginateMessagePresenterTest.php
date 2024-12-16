<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Presenter;


use Messenger\Domain\Presenter\PaginateMessagePresenterInterface;
use Messenger\Domain\Response\PaginateMessageResponse;

class PaginateMessagePresenterTest implements PaginateMessagePresenterInterface
{
    public PaginateMessageResponse $response;

    public function present(PaginateMessageResponse $response): void
    {
        $this->response = $response;
    }
}