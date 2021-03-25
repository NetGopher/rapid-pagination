<?php

namespace mav3rick177\RapidPagination\Tests;

use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;

if (class_exists(BaseJsonResource::class)) {
    /**
     * To support testing Laravel version 7 and up.
     */
    class JsonResource extends BaseJsonResource
    {
    }
} else {
    /**
     * To support testing until Laravel version 7.
     */
    class JsonResource extends \Illuminate\Http\Resources\Json\Resource
    {
    }
}
