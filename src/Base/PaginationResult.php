<?php

namespace mav3rick177\RapidPagination\Base;

/**
 * Class PaginationResult
 */
class PaginationResult implements \IteratorAggregate, \Countable
{
    /**
     * @var mixed
     */
    public $records;

    /**
     * @var null|bool
     */
    public $hasPrevious;

    /**
     * @var null|mixed
     */
    public $previousCursor;

    /**
     * @var null|bool
     */
    public $hasNext;

    /**
     * @var null|mixed
     */
    public $nextCursor;

    /**
     * @var null|mixed
     */
    public $previousUrl;

    /**
     * @var null|mixed
     */
    public $nextUrl;

    /**
     * @var null|mixed
     */
    public $tab;

    /**
     * @var null|mixed
     */
    public $query;

    /**
     * PaginationResult constructor.
     * Merge $meta entries into $this.
     *
     * @param mixed $rows
     * @param array $meta
     */
    public function __construct($rows, array $meta)
    {
        $this->records = $rows;
        foreach ($meta as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get iterator of records.
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return $this->records instanceof \Traversable ? $this->records : new \ArrayIterator($this->records);
    }

    /**
     * Count records.
     *
     * @return int
     * @see https://wiki.php.net/rfc/counting_non_countables
     */
    public function count()
    {
        return count($this->records);
    }

    /**
     * Render the paginator using the given view.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function render($view = 'rapid-pagination::default', $data = [])
    {
        return view($view, array_merge($data, [
            'paginator' => $this,
        ]));
    }
}
