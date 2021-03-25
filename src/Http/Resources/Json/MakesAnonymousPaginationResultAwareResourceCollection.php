<?php

namespace mav3rick177\RapidPagination\Http\Resources\Json;

/**
 * Trait MakesAnonymousPaginationResultAwareResourceCollection
 *
 * @mixin \Illuminate\Http\Resources\Json\JsonResource
 */
trait MakesAnonymousPaginationResultAwareResourceCollection
{
    /**
     * Create a new anonymous resource collection.
     *
     * @param  mixed                                                       $resource
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource)
    {
        return tap(new AnonymousPaginationResultAwareResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }
}
