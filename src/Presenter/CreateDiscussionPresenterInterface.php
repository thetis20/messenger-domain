<?php

namespace Messenger\Domain\Presenter;

use Messenger\Domain\Response\CreateDiscussionResponse;

interface CreateDiscussionPresenterInterface
{
    public function present(CreateDiscussionResponse $response): void;

}
