<?php

namespace Prob\Url;

use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testSegment1()
    {
        $path = new Path('/some/other');
        $this->assertEquals('some', $path->seg(0));
    }

    public function testSegment2()
    {
        $path = new Path('/some/other');
        $this->assertEquals('other', $path->seg(1));
    }

    public function testSegment3()
    {
        $path = new Path('//some//other///');
        $this->assertEquals(2, count($path->segments()));
    }

    public function testSegments()
    {
        $path = new Path('/some/other//etc');
        $this->assertEquals(['some', 'other', 'etc'], $path->segments());
    }
}
