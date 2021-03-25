<?php

namespace mav3rick177\RapidPagination\Base\Contracts;

use mav3rick177\RapidPagination\Base\Query;

/**
 * Interface Formatter
 */
interface Formatter
{
    /**
     * Format rows.
     *
     * @param  mixed $rows
     * @param  array $meta
     * @param  Query $query
     * @return mixed
     */
    public function format($rows, array $meta, Query $query);
}
