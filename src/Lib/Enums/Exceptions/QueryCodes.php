<?php

namespace Alibe\GeoCodes\Lib\Enums\Exceptions;

class QueryCodes
{
    public const FIELD_NOT_INDEXABLE                    = 11001;
    public const FIELD_NOT_SELECTABLE                   = 11002;
    public const LIMIT_ROWS_NUMBER_LESS_THAN_ZERO       = 11003;
    public const OFFSET_ROWS_NUMBER_LESS_THAN_ZERO      = 11004;
    public const PROPERTY_TO_ORDER_NOT_VALID            = 11005;
    public const ORDER_TYPE_NOT_VALID                   = 11006;
    public const FETCH_MULTIDIM_ARRAY_NOT_ALLOWED       = 11007;
    public const INTERSECT_OPERATION_NOT_ALLOWED        = 11008;
    public const COMPLEMENT_OPERATION_NOT_ALLOWED       = 11009;
    public const CONDITIONS_STRUCTURE_WRONG             = 11010;
    public const CONDITIONS_STRUCTURE_COMPONENTS_WRONG  = 11011;
    public const CONDITIONS_WRONG_OPERATOR              = 11012;
    public const CONDITIONS_TERM_MUST_BE_ARRAY          = 11013;
    public const CONDITIONS_TERM_MUST_BE_NOT_ARRAY      = 11014;
    public const CONDITIONS_WRONG_FIELD                 = 11015;
    public const CONDITIONS_WRONG_FIELD_TYPE            = 11016;
}
