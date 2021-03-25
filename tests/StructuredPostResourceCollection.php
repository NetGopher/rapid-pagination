<?php

namespace mav3rick177\RapidPagination\Tests;

use Illuminate\Http\Resources\Json\ResourceCollection;
use mav3rick177\RapidPagination\RapidPaginationResourceCollectionTrait;

/**
 * Class PostResourceCollection
 */
class StructuredPostResourceCollection extends ResourceCollection
{
    use RapidPaginationResourceCollectionTrait;

    public $collects = PostResource::class;

    /**
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            static::$wrap => parent::toArray($request),
            'post_resource_collection' => true,
        ];
    }
}
