<?php

namespace mav3rick177\RapidPagination\Tests;

use Illuminate\Support\Collection;
use mav3rick177\RapidPagination\Processor;
use mav3rick177\RapidPagination\Base\Query;

class FormatterTest extends TestCase
{
    /**
     * @test
     */
    public function testStaticCustomFormatter()
    {
        try {
            Processor::setDefaultFormatter(function ($rows, $meta, Query $query) {
                $this->assertInstanceOf(Post::class, $query->builder()->getModel());
                $meta['foo'] = 'bar';
                return new Collection([
                    'records' => $rows,
                    'meta' => $meta,
                ]);
            });
            $result = Post::rapid_pagination()->orderBy('id')->paginate();
            $this->assertInstanceOf(Collection::class, $result);
            $this->assertEquals('bar', $result['meta']['foo']);
        } finally {
            Processor::restoreDefaultFormatter();
        }
    }

    /**
     * @test
     */
    public function testInstanceCustomFormatter()
    {
        $pager = Post::rapid_pagination();
        try {
            $result = $pager->orderBy('id')->useFormatter(function ($rows, $meta, Query $query) {
                $this->assertInstanceOf(Post::class, $query->builder()->getModel());
                $meta['foo'] = 'bar';
                return new Collection([
                    'records' => $rows,
                    'meta' => $meta,
                ]);
            })->paginate();
            $this->assertInstanceOf(Collection::class, $result);
            $this->assertEquals('bar', $result['meta']['foo']);
        } finally {
            $pager->restoreFormatter();
        }
    }

    /**
     * @test
     */
    public function testInvalidFormatter()
    {
        $this->expectException(\InvalidArgumentException::class);
        Post::rapid_pagination()->useProcessor(function () {});
    }

    /**
     * @test
     */
    public function testInvalidProcessor()
    {
        $this->expectException(\InvalidArgumentException::class);
        Post::rapid_pagination()->useFormatter(__CLASS__);
    }
}
