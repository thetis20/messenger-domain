<?php

namespace Messenger\Domain\TestsIntegration\Adapter\Presenter;

use Messenger\Domain\Presenter\DeleteMessagePresenterInterface;
use Messenger\Domain\Response\DeleteMessageResponse;

class DeleteMessagePresenterTest implements DeleteMessagePresenterInterface
{
    public DeleteMessageResponse $response;

    public function present(DeleteMessageResponse $response): void
    {
        $this->response = $response;
    }
}