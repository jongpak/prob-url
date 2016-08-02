<?php

namespace Prob\Url;

use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    public function testStaticMatch()
    {
        $matcher = new Matcher('/some');
        $this->assertEquals($matcher->match('/some'), []);
    }

    public function testStaticMatchTwoDeep()
    {
        $matcher = new Matcher('/some/other');
        $this->assertEquals($matcher->match('/some/other'), []);
    }

    public function testStaticNoneMatch()
    {
        $matcher = new Matcher('/some');
        $this->assertEquals($matcher->match('/nonSome'), false);
    }

    public function testStaticNoneMatchTwoDeep()
    {
        $matcher = new Matcher('/some/other');
        $this->assertEquals($matcher->match('/some'), false);
    }

    public function testDynamicMatchOneDeep()
    {
        $matcher = new Matcher('/{someName}');
        $this->assertEquals($matcher->match('/test'), [
            'someName' => 'test'
        ]);
    }

    public function testDynamicMatchTwoDeep()
    {
        $matcher = new Matcher('/{someName}/{otherName}');
        $this->assertEquals($matcher->match('/test/ok'), [
            'someName' => 'test',
            'otherName' => 'ok'
        ]);
    }

    public function testDynamicMatchType()
    {
        $matcher = new Matcher('/{someName:string}/{otherName:int}');
        $this->assertEquals($matcher->match('/test/5'), [
            'someName' => 'test',
            'otherName' => '5'
        ]);
    }

    public function testDynamicNoneMatch()
    {
        $matcher = new Matcher('/{someName:int}');
        $this->assertEquals($matcher->match('/test'), false);
    }

}
