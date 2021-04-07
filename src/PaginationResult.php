<?php

namespace mav3rick177\RapidPagination;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use mav3rick177\RapidPagination\Base\PaginationResult as BasePaginationResult;

/**
 * PaginationResult
 *
 * @see BasePaginationResult
 * @mixin Collection
 */
class PaginationResult extends BasePaginationResult implements \JsonSerializable, Arrayable, Jsonable
{

    /**
     * The view factory resolver callback.
     *
     * @var \Closure
     */
    protected static $viewFactoryResolver;

    use Macroable {
        __call as macroCall;
    }

    /**
     * Make dynamic calls into the collection.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->records->$method(...$parameters);
    }

    /**
     * Get the URL for the next page.
     *
     * @return string|null
     */
    public function makePreviousUrl($state)
    {
        $this->previousUrl =  $this->url('prev', $state);
    }

    /**
     * Get the URL for the next page.
     *
     * @return string|null
     */
    public function makeNextUrl($state)
    {
        $this->nextUrl = $this->url('next', $state);
    }

    /**
     * Get the URL.
     *
     * @return string
     */
    public function url($direction, $state)
    {
        $uri = \URL::route(\Route::currentRouteName(), [], false) . '?'. 'direction=' . $direction . '&state=' . $state;
        return $uri;             
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach (get_object_vars($this) as $key => $value) {
            $array[Str::snake($key)] = $value;
        }
        return $array;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int    $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
