<?php

namespace Messenger\Domain\Presenter;

use Messenger\Domain\Response\ShowDiscussionResponse;

interface ShowDiscussionPresenterInterface
{
    public function present(ShowDiscussionResponse $response): void;

}
