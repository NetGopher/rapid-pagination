<?php

namespace mav3rick177\RapidPagination\Base\Exceptions\Query;

use mav3rick177\RapidPagination\Base\Contracts\Exceptions\Query\BadQueryException;
use mav3rick177\RapidPagination\Base\Exceptions\DomainException;

/**
 * Class LimitParameterException
 */
class LimitParameterException extends DomainException implements BadQueryException
{
}
