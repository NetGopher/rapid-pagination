<?php

namespace mav3rick177\RapidPagination\Base\Exceptions\Query;

use mav3rick177\RapidPagination\Base\Contracts\Exceptions\Query\BadQueryException;
use mav3rick177\RapidPagination\Base\Exceptions\UnexpectedValueException;

/**
 * Class CursorParameterException
 */
class CursorParameterException extends UnexpectedValueException implements BadQueryException
{
    /**
     * @var string
     */
    protected $column;

    /**
     * CursorParameterException constructor.
     *
     * @param string                     $message
     * @param string                     $column   column which caused an exception.
     * @param int                        $code
     * @param null|\Exception|\Throwable $previous
     */
    public function __construct($message, $column, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->column = $column;
    }

    /**
     * Return column which caused an exception.
     *
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }
}
