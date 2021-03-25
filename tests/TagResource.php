<?php

namespace mav3rick177\RapidPagination\Tests;

use mav3rick177\RapidPagination\RapidPaginationResourceTrait;

/**
 * Class TagResource
 */
class TagResource extends JsonResource
{
    use RapidPaginationResourceTrait;

    /**
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array_replace(parent::toArray($request), [
            'posts' => new PostResourceCollection($this->whenLoaded('posts')),
        ]);
    }
}
