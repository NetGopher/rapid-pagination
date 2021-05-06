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
     * Set URL for the previous page.
     *
     * @return string|null
     */
    public function makePreviousUrl($state)
    {
        $this->previousUrl =  $this->url('prev', $state);
    }

    /**
     * Set URL for the next page.
     *
     * @return string|null
     */
    public function makeNextUrl($state)
    {
        $this->nextUrl = $this->url('next', $state);
    }


    /**
     * Set URL for the next page.
     *
     * @return string|null
     */
    public function setTabID($tab)
    {
        $this->tab = $tab;
    }

    /**
     * Add a set of query string values to the paginator.
     *
     * @param  array|string|null  $key
     * @param  string|null  $value
     * @return $this
     */
    public function appends($key, $value = null)
    {
        if (is_null($key)) {
            return $this;
        }

        if (is_array($key)) {
            return $this->appendArray($key);
        }
        
        return $this->addQuery($key, $value);
    }

    /**
     * Add an array of query string values.
     *
     * @param  array  $keys
     * @return $this
     */
    protected function appendArray(array $keys)
    {
        foreach ($keys as $key => $value) {
            $this->addQuery($key, $value);
        }

        return $this;
    }


    /**
     * Add a query string value to the paginator.
     *
     * @param  string  $key
     * @param  string  $value
     * @return $this
     */
    protected function addQuery($key, $value)
    {
        //var_dump($key);
        if ($key !== "direction" && $key !== "state" && $key !== "tab") {
            $this->query[$key] = $value;
        }
        var_dump($this->query);
        return $this;
    }

    /**
     * Get the URL.
     *
     * @return string
     */
    public function url($direction, $state)
    {
        $uri = url()->current() . '?'. 'direction=' . $direction . '&state=' . $state . '&tab=' . $this->tab;
        // append query to the URI
        if (is_array($this->query) || is_object($this->query))
        {
            foreach ($this->query as $key => $value){
                $uri = $uri . '&' . $key . '=' . $value;
            }
        }
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
