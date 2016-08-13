<?php

namespace Prob\Url;

use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    public function testStaticMatch()
    {
        $matcher = new Matcher('/some');
        $this->assertEquals([], $matcher->match('/some'));
    }

    public function testStaticMatchTwoDeep()
    {
        $matcher = new Matcher('/some/other');
        $this->assertEquals([], $matcher->match('/some/other'));
    }

    public function testStaticNoneMatch()
    {
        $matcher = new Matcher('/some');
        $this->assertEquals(false, $matcher->match('/nonSome'));
    }

    public function testStaticNoneMatchTwoDeep()
    {
        $matcher = new Matcher('/some/other');
        $this->assertEquals(false, $matcher->match('/some'));
    }

    public function testDynamicMatchOneDeep()
    {
        $matcher = new Matcher('/{someName}');
        $this->assertEquals(['someName' => 'test'], $matcher->match('/test'));
    }

    public function testDynamicMatchTwoDeep()
    {
        $matcher = new Matcher('/{someName}/{otherName}');
        $this->assertEquals([
            'someName' => 'test',
            'otherName' => 'ok'
        ], $matcher->match('/test/ok'));
    }

    public function testDynamicMatchType()
    {
        $matcher = new Matcher('/{someName:string}/{otherName:int}');
        $this->assertEquals([
            'someName' => 'test',
            'otherName' => '5'
        ], $matcher->match('/test/5'));
    }

    public function testDynamicNoneMatch()
    {
        $matcher = new Matcher('/{someName:int}');
        $this->assertEquals(false, $matcher->match('/test'));
    }

}
