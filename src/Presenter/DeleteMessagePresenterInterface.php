<?php

namespace Messenger\Domain\Presenter;

use Messenger\Domain\Response\DeleteMessageResponse;

interface DeleteMessagePresenterInterface
{
    public function present(DeleteMessageResponse $response): void;

}
