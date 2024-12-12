<?php

namespace Messenger\Domain\Presenter;

use Messenger\Domain\Response\PaginateDiscussionResponse;

interface PaginateDiscussionPresenterInterface
{
    public function present(PaginateDiscussionResponse $response): void;

}
