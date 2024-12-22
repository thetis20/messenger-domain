<?php

namespace Messenger\Domain\Presenter;

use Messenger\Domain\Response\MarkAsDiscussionResponse;

interface MarkAsDiscussionPresenterInterface
{
    public function present(MarkAsDiscussionResponse $response): void;

}
