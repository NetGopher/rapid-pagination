<?php

namespace mav3rick177\RapidPagination\Base\Exceptions\Query;

use mav3rick177\RapidPagination\Base\Contracts\Exceptions\Query\BadQueryException;
use mav3rick177\RapidPagination\Base\Exceptions\OutOfRangeException;

/**
 * Class InsufficientConstraintsException
 */
class InsufficientConstraintsException extends OutOfRangeException implements BadQueryException
{
}
