<?php

namespace Messenger\Domain\Presenter;

use Messenger\Domain\Response\SendMessageResponse;

interface SendMessagePresenterInterface
{
    public function present(SendMessageResponse $response): void;

}
