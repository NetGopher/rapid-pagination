<?php

namespace mav3rick177\RapidPagination\Base;

use mav3rick177\RapidPagination\Base\Contracts\Formatter;
use mav3rick177\RapidPagination\Base\Exceptions\InvalidArgumentException;
use mav3rick177\RapidPagination\Base\Query\UnionAll;

/**
 * Class AbstractProcessor
 */
abstract class AbstractProcessor
{
    /**
     * @var null|callable
     */
    protected static $defaultFormatter;

    /**
     * @var null|callable
     */
    protected $formatter;
    

    /**
     * Override static default formatter.
     *
     * @param callable|Formatter|string $formatter
     */
    public static function setDefaultFormatter($formatter)
    {
        static::$defaultFormatter = static::validateFormatter($formatter);
    }

    /**
     * Restore static default formatter.
     */
    public static function restoreDefaultFormatter()
    {
        static::$defaultFormatter = null;
    }

    /**
     * Use custom formatter.
     *
     * @param  callable|Formatter|string $formatter
     * @return $this
     */
    public function useFormatter($formatter)
    {
        $this->formatter = static::validateFormatter($formatter);
        return $this;
    }

    /**
     * Restore default formatter.
     *
     * @return $this
     */
    public function restoreFormatter()
    {
        $this->formatter = null;
        return $this;
    }

    /**
     * Get result.
     *
     * @param  Query $query
     * @param  mixed $rows
     * @return mixed
     */
    public function process(Query $query, $rows)
    {
        $meta = [
            'hasPrevious' => false,
            'previousCursor' => null,
            'hasNext' => false,
            'nextCursor' => null,
            'previousUrl' => null,
            'nextUrl' => null,
        ];

        if ($this->shouldLtrim($query, $rows)) {
            $type = $query->direction()->forward() ? 'previous' : 'next';
            $meta['has' . ucfirst($type)] = true;
            $meta[$type . 'Cursor'] = $this->makeCursor(
                $query,
                $this->offset($rows, (int)$query->exclusive())
            );
            $rows = $this->slice($rows, 1);
        }
        if ($this->shouldRtrim($query, $rows)) {
            $type = $query->direction()->backward() ? 'previous' : 'next';
            $meta['has' . ucfirst($type)] = true;
            $meta[$type . 'Cursor'] = $this->makeCursor(
                $query,
                $this->offset($rows, $query->limit() - $query->exclusive())
            );
            $rows = $this->slice($rows, 0, $query->limit());
        }

        // If we are not using UNION ALL, boolean values are not defined.
        if (!$query->selectOrUnionAll() instanceof UnionAll) {
            $meta[$query->direction()->forward() ? 'hasPrevious' : 'hasNext'] = null;
        }

        return $this->invokeFormatter($this->shouldReverse($query) ? $this->reverse($rows) : $rows, $meta, $query);
    }

    /**
     * Invoke formatter.
     *
     * @param  mixed $rows
     * @param  array $meta
     * @param  Query $query
     * @return mixed
     */
    protected function invokeFormatter($rows, array $meta, Query $query)
    {
        $formatter = $this->formatter ?: static::$defaultFormatter ?: [$this, 'defaultFormat'];
        return $formatter($rows, $meta, $query);
    }

    /**
     * Validate formatter and return in normalized form.
     *
     * @param  mixed    $formatter
     * @return callable
     */
    protected static function validateFormatter($formatter)
    {
        if (is_subclass_of($formatter, Formatter::class)) {
            return [is_string($formatter) ? new $formatter() : $formatter, 'format'];
        }
        if (is_callable($formatter)) {
            return $formatter;
        }
        throw new InvalidArgumentException('Formatter must be an instanceof ' . Formatter::class . ' or callable.');
    }

    /**
     * Format result with default format.
     *
     * @param  mixed            $rows
     * @param  array            $meta
     * @param  Query            $query
     * @return PaginationResult
     */
    protected function defaultFormat($rows, array $meta, Query $query)
    {
        return new PaginationResult($rows, $meta);
    }

    /**
     * Determine if the rows should be replaced in reverse order.
     *
     * @param  Query $query
     * @return bool
     */
    protected function shouldReverse(Query $query)
    {
        return $query->direction()->backward();
    }

    /**
     * Determine if the first row should be dropped.
     *
     * @param  Query $query
     * @param  mixed $rows
     * @return bool
     */
    protected function shouldLtrim(Query $query, $rows)
    {
        $first = $this->offset($rows, 0);

        $selectOrUnionAll = $query->selectOrUnionAll();

        // If we are not using UNION ALL or the elements are empty...
        if (!$selectOrUnionAll instanceof UnionAll || !$first) {
            return false;
        }

        foreach ($selectOrUnionAll->supportQuery()->orders() as $order) {

            // Retrieve values
            $field = $this->field($first, $order->column());
            $cursor = $query->cursor()->get($order->column());

            // Compare the first row and the cursor
            if (!$diff = $this->compareField($field, $cursor)) {
                continue;
            }

            //
            // Drop the first row if ...
            //
            //
            //  - the support query is descending  &&  $field < $cursor
            //
            //               -------------------->  Main query, ascending
            //           [4, <5>, 6, 7, 8, 9, 10]
            //         <-----                       Support query, descending
            //
            //                               ---->  Support query, descending
            //           [10, 9, 8, 7, 6, <5>, 4]
            //         <---------------------       Main query, ascending
            //
            //
            //  - the support query is ascending   &&  $field > $cursor
            //
            //                              ----->  Support query, ascending
            //           [4, 5, 6, 7, 8, <9>, 10]
            //         <--------------------        Main query, descending
            //
            //               -------------------->  Main query, descending
            //           [10, <9>, 8, 7, 6, 5, 4]
            //         <----                        Support query, ascending
            //
            return $diff === ($order->descending() ? -1 : 1);
        }

        // If the first row and the cursor are identical, we should drop the first row only if exclusive.
        return $query->exclusive();
    }

    /**
     * Determine if the last row should be dropped.
     *
     * @param Query $query
     * @param $rows
     * @return bool
     */
    protected function shouldRtrim(Query $query, $rows)
    {
        return $query->limit() < $this->count($rows);
    }

    /**
     * Make a cursor from the specific row.
     *
     * @param  Query          $query
     * @param  mixed          $row
     * @return int[]|string[]
     */
    protected function makeCursor(Query $query, $row)
    {
        $fields = [];
        foreach ($query->orders() as $order) {
            $fields[$order->column()] = $this->field($row, $order->column());
        }
        return $fields;
    }

    /**
     * Return comparable value from a row.
     *
     * @param  mixed      $row
     * @param  string     $column
     * @return int|string
     */
    abstract protected function field($row, $column);

    /**
     * Compare the values.
     *
     *  "$field < $cursor" should return -1.
     *  "$field > $cursor" should return 1.
     *  "$field == $cursor" should return 0.
     *
     * @param  int|string $field
     * @param  int|string $cursor
     * @return int
     */
    protected function compareField($field, $cursor)
    {
        return ($field > $cursor) - ($field < $cursor);
    }

    /**
     * Return the n-th element of collection.
     * Must return null if not exists.
     *
     * @param  mixed $rows
     * @param  int   $offset
     * @return mixed
     */
    abstract protected function offset($rows, $offset);

    /**
     * Slice rows, like PHP function array_slice().
     *
     * @param  mixed    $rows
     * @param  int      $offset
     * @param  null|int $length
     * @return mixed
     */
    abstract protected function slice($rows, $offset, $length = null);

    /**
     * Count rows, like PHP function count().
     *
     * @param  mixed $rows
     * @return int
     */
    abstract protected function count($rows);

    /**
     * Reverse rows, like PHP function array_reverse().
     *
     * @param $rows
     * @return mixed
     */
    abstract protected function reverse($rows);
}
