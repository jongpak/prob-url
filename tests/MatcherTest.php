<?php

namespace Prob\Url;

use PHPUnit_Framework_TestCase;
use Prob\Url\Exception\TypePatternNotFound;

class MatcherTest extends PHPUnit_Framework_TestCase
{
    public function testStaticMatch()
    {
        $matcher = new Matcher();
        $matcher->setUrlFormat('/some');

        $this->assertEquals(true, $matcher->isMatch('/some'));
        $this->assertEquals([], $matcher->getMatchedUrlFormat('/some'));
    }

    public function testStaticMatchTwoDeep()
    {
        $matcher = new Matcher();
        $matcher->setUrlFormat('/some/other');

        $this->assertEquals(true, $matcher->isMatch('/some/other'));
        $this->assertEquals([], $matcher->getMatchedUrlFormat('/some/other'));
    }

    public function testStaticNoneMatch()
    {
        $matcher = new Matcher();
        $matcher->setUrlFormat('/some');

        $this->assertEquals(false, $matcher->isMatch('/other'));
        $this->assertEquals(null, $matcher->getMatchedUrlFormat('/other'));
    }

    public function testStaticNoneMatchTwoDeep()
    {
        $matcher = new Matcher();
        $matcher->setUrlFormat('/some/other');

        $this->assertEquals(false, $matcher->isMatch('/some'));
        $this->assertEquals(null, $matcher->getMatchedUrlFormat('/some'));
    }

    public function testDynamicMatchOneDeep()
    {
        $matcher = new Matcher();
        $matcher->setUrlFormat('/{someName}');

        $this->assertEquals(true, $matcher->isMatch('/test'));
        $this->assertEquals([
            'someName' => 'test'
        ], $matcher->getMatchedUrlFormat('/test'));
    }

    public function testDynamicMatchTwoDeep()
    {
        $matcher = new Matcher();
        $matcher->setUrlFormat('/{someName}/{otherName}');

        $this->assertEquals(true, $matcher->isMatch('/test/ok'));
        $this->assertEquals([
            'someName' => 'test',
            'otherName' => 'ok'
        ], $matcher->getMatchedUrlFormat('/test/ok'));

        $this->assertEquals(true, $matcher->isMatch('/test/5'));
        $this->assertEquals([
            'someName' => 'test',
            'otherName' => '5'
        ], $matcher->getMatchedUrlFormat('/test/5'));
    }

    public function testDynamicMatchType()
    {
        $matcher = new Matcher();
        $matcher->setUrlFormat('/{someName:string}/{otherName:int}');

        $this->assertEquals(true, $matcher->isMatch('/test/5'));
        $this->assertEquals([
            'someName' => 'test',
            'otherName' => '5'
        ], $matcher->getMatchedUrlFormat('/test/5'));

        $this->assertEquals(false, $matcher->isMatch('/test/ok'));
        $this->assertEquals(null, $matcher->getMatchedUrlFormat('/test/ok'));
    }

    public function testDynamicNoneMatch()
    {
        $matcher = new Matcher();
        $matcher->setUrlFormat('/{someName:int}');

        $this->assertEquals(false, $matcher->isMatch('/test'));
        $this->assertEquals(null, $matcher->getMatchedUrlFormat('/test'));
    }

    public function testNotExistsType()
    {
        $matcher = new Matcher();
        $matcher->setUrlFormat('/{emailAddress:email}');

        $this->expectException(TypePatternNotFound::class);
        $matcher->isMatch('/test@test.com');
        $matcher->getMatchedUrlFormat('/test@test.com');
    }

    public function testCustomTypePattren()
    {
        $matcher = new Matcher();
        $matcher->setUrlFormat('/{emailAddress:email}/{ipAddress:ip}/{name:string}');

        $matcher->addTypePattern('email', '\S+@\S+\.\S+');
        $matcher->addTypePattern('ip', '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}');

        $this->assertEquals(false, $matcher->isMatch('/test.com/localhost/Park'));
        $this->assertEquals(null, $matcher->getMatchedUrlFormat('/test.com/localhost/Park'));

        $this->assertEquals(true, $matcher->isMatch('/test@test.com/127.0.0.1/Park'));
        $this->assertEquals([
            'emailAddress' => 'test@test.com',
            'ipAddress' => '127.0.0.1',
            'name' => 'Park'
        ], $matcher->getMatchedUrlFormat('/test@test.com/127.0.0.1/Park'));
    }
}
