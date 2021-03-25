<?php

namespace mav3rick177\RapidPagination\Tests;

class MacroTest extends TestCase
{
    /**
     * @test
     */
    public function registerAllIlluminateMacros()
    {
        (new Post())->belongsTo(Post::class)->rapid_pagination()->orderBy('id')->build()->toSql();
        $x = (new Post())->rapid_pagination()->orderBy('id')->build()->toSql();
        $y = (new Post())->newQuery()->getQuery()->rapid_pagination()->orderBy('id')->build()->toSql();
        $this->assertEquals($x, $y);
    }
}
