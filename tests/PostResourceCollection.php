<?php

namespace mav3rick177\RapidPagination\Tests;

use Illuminate\Http\Resources\Json\ResourceCollection;
use mav3rick177\RapidPagination\RapidPaginationResourceCollectionTrait;

/**
 * Class PostResourceCollection
 */
class PostResourceCollection extends ResourceCollection
{
    use RapidPaginationResourceCollectionTrait;
}
