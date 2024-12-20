<?php

namespace Messenger\Domain\Presenter;

use Messenger\Domain\Response\MarkAsDiscussionResponse;

interface MarkAsPresenterInterface
{
    public function present(MarkAsDiscussionResponse $response): void;

}
