<?php

namespace Prob\Url;

use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testSegment1()
    {
        $path = new Path('/some/other');
        $this->assertEquals($path->seg(0), 'some');
    }

    public function testSegment2()
    {
        $path = new Path('/some/other');
        $this->assertEquals($path->seg(1), 'other');
    }

    public function testSegment3()
    {
        $path = new Path('//some//other///');
        $this->assertEquals(count($path->segments()), 2);
    }

    public function testSegments()
    {
        $path = new Path('/some/other//etc');
        $this->assertEquals($path->segments(), [
            'some', 'other', 'etc'
        ]);
    }
}
