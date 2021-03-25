<?php

namespace mav3rick177\RapidPagination\Tests;

use mav3rick177\RapidPagination\RapidPaginationResourceTrait;

/**
 * Class PostResource
 */
class PostResource extends JsonResource
{
    use RapidPaginationResourceTrait;

    public $preserveKeys = true;

    /**
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request) + [
            'post_resource' => true,
        ];
    }
}
