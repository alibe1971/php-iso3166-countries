<?php

namespace Alibe\GeoCodes\Lib\Enums\Exceptions;

class ExceptionsMessagesMap
{
    /** Config */
    public const ERROR_10001 = 'Language "%s" not available in the package';


    /** Queries */
    public const ERROR_11001 = 'Property "%s" not existent or not usable as index';
    public const ERROR_11002 = 'Property "%s" not existent or not usable as selectable';
    public const ERROR_11003 = 'Attribute `limit`.`from` cannot be less than 0';
    public const ERROR_11004 = 'Attribute `limit`.`numberOfItems` cannot be less than 0';
    public const ERROR_11005 = 'Attribute `orderBy`.`property` must be usable as index. "%s" not not valid';
    public const ERROR_11006 = 'Attribute `orderBy`.`orderType` must "ASC" or "DESC" (case insensitive)';
    public const ERROR_11007 = 'Multidimensional arrays are not allowed in the fetching';
    public const ERROR_11008 = 'The `intersect` operation requires at least 2 fetched elements';
    public const ERROR_11009 = 'The `complement` operation requires at least 2 fetched elements';
    public const ERROR_11010 = 'Wrong conditions\' structure';
    public const ERROR_11011 = 'Wrong structure for the conditions\' components';

    public const ERROR_11012 = 'The condition operator "%s" is invalid';
    public const ERROR_11013 = 'With "%s" operator the term of match must be an array';
    public const ERROR_11014 = 'With "%s" operator the term of match cannot be an array';
    public const ERROR_11015 = 'The condition property "%s" doesn\'t exist';
    public const ERROR_11016 = 'The condition property must be a string';
}
