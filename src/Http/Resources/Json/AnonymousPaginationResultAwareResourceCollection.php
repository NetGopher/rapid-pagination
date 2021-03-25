<?php

namespace mav3rick177\RapidPagination\Http\Resources\Json;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use mav3rick177\RapidPagination\Http\Resources\CollectsPaginationResult;

/**
 * Class AnonymousPaginationResultAwareResourceCollection
 *
 * @mixin \Illuminate\Http\Resources\Json\JsonResource
 */
class AnonymousPaginationResultAwareResourceCollection extends AnonymousResourceCollection
{
    use MakesAnonymousPaginationResultAwareResourceCollection,
        CollectsPaginationResult,
        RespondsWithPaginationResult;
}
