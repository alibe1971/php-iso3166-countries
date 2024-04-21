<?php

namespace Alibe\GeoCodes\Lib\Exceptions;

use InvalidArgumentException;
use Exception;
use Alibe\GeoCodes\Lib\Enums\Exceptions\ExceptionsMessagesMap;

class BaseException extends Exception
{
    /**
     * @param int $code
     * @param array<string> $mexParams
     * @param \Throwable|null $previous
     */
    public function __construct(int $code, array $mexParams = [], \Throwable $previous = null)
    {
        $message = ExceptionsMessagesMap::class . '::ERROR_' . $code;
        if (!defined($message)) {
            throw new InvalidArgumentException('Found Error with code ' . $code . ' and no corresponding message');
        }
        $message = constant($message);
        if (!empty($mexParams)) {
            $message = vsprintf($message, $mexParams);
        }
        parent::__construct($message, $code, $previous);
    }
}
