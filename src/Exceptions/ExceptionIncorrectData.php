<?php

namespace Demeja1610\CBRFExchangeRate\Exceptions;

use Throwable;
use RuntimeException;

class ExceptionIncorrectData extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
