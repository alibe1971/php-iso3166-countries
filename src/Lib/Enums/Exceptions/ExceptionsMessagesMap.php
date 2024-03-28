<?php

namespace Alibe\GeoCodes\Lib\Enums\Exceptions;

class ExceptionsMessagesMap
{
    /** Config */
    public const ERROR_10001 = 'Language not available in the package';


    /** Queries */
    public const ERROR_11001 = 'Property not usable as index';
    public const ERROR_11002 = 'Property not existent or not usable as selectable';
    public const ERROR_11003 = 'Attribute `limit`.`from` cannot be less than 0';
    public const ERROR_11004 = 'Attribute `limit`.`numberOfItems` cannot be less than 0';
}
