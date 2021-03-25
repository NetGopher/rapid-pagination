<?php

namespace mav3rick177\RapidPagination;

use mav3rick177\RapidPagination\Http\Resources\CollectsPaginationResult;
use mav3rick177\RapidPagination\Http\Resources\Json\MakesAnonymousPaginationResultAwareResourceCollection;
use mav3rick177\RapidPagination\Http\Resources\Json\RespondsWithPaginationResult;

/**
 * Trait RapidPaginationResourceCollectionTrait
 */
trait RapidPaginationResourceCollectionTrait
{
    use MakesAnonymousPaginationResultAwareResourceCollection,
        CollectsPaginationResult,
        RespondsWithPaginationResult;
}
 