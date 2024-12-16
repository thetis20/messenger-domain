<?php

namespace Messenger\Domain\Presenter;

use Messenger\Domain\Response\PaginateMessageResponse;

interface PaginateMessagePresenterInterface
{
    public function present(PaginateMessageResponse $response): void;

}
