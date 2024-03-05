<?php

namespace Alibe\GeoCodes\Lib\Exceptions;

use \InvalidArgumentException;
use \Exception;
use Alibe\GeoCodes\Lib\Enums\Exceptions\ExceptionsMessagesMap;

class BaseException extends Exception
{
    public function __construct(int $code, \Throwable $previous = null)
    {
        $message = ExceptionsMessagesMap::class . '::ERROR_' . $code;
        if (!defined($message)) {
            throw new InvalidArgumentException('Error code ' . $code . ' has no corresponding message');
        }
        parent::__construct(constant($message), $code, $previous);
    }

}
