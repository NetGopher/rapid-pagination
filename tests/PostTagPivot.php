<?php

namespace mav3rick177\RapidPagination\Tests;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class PostTagPivot
 */
class PostTagPivot extends Pivot
{
    protected $casts = [
        'id' => 'int',
    ];
}
