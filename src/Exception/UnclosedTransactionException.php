<?php

namespace Messenger\Domain\Exception;

final class UnclosedTransactionException extends \Exception
{
    public const int CODE = 6003;

    public function __construct()
    {
        parent::__construct('Transaction not closed.', self::CODE);
    }
}
