<?php

namespace mav3rick177\RapidPagination\Tests;

use Illuminate\Database\Eloquent\Model;
use mav3rick177\RapidPagination\Paginator;

/**
 * Class Post
 *
 * @method static Paginator rapid_pagination()
 * @method static Post create(array $attributes = [])
 * @method static Post whereUserId(int $userId)
 */
class Post extends Model
{
    protected $fillable = ['id', 'updated_at'];

    public $timestamps = false;

    protected $hidden = ['pivot'];

    protected $casts = [
        'id' => 'int',
        'updated_at' => 'datetime',
    ];
}
