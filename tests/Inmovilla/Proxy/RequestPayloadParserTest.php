<?php

namespace Inmovilla\Tests\Proxy;

use Inmovilla\Proxy\RequestPayloadParser;
use Inmovilla\Proxy\RequestPayload;
use Inmovilla\ApiClient\Request;
use PHPUnit\Framework\TestCase;

class RequestPayloadParserTest extends TestCase
{
    public function testParseValidInput(): void
    {
        $input = 'param=agency;password;1;type1;1;100;;order1;type2;2;50;where2;order2';

        $result = RequestPayloadParser::parse($input);

        $this->assertInstanceOf(RequestPayload::class, $result);
        $this->assertEquals('agency', $result->agency);
        $this->assertEquals('password', $result->password);
        $this->assertEquals(1, $result->language);

        $requests = $result->requests->getRequests();
        $this->assertCount(2, $requests);

        $this->assertInstanceOf(Request::class, $requests[0]);
        $this->assertEquals('type1', $requests[0]->type);
        $this->assertEquals(1, $requests[0]->startPosition);
        $this->assertEquals(100, $requests[0]->numElements);
        $this->assertEquals('', $requests[0]->where);
        $this->assertEquals('order1', $requests[0]->order);

        $this->assertInstanceOf(Request::class, $requests[1]);
        $this->assertEquals('type2', $requests[1]->type);
        $this->assertEquals(2, $requests[1]->startPosition);
        $this->assertEquals(50, $requests[1]->numElements);
        $this->assertEquals('where2', $requests[1]->where);
        $this->assertEquals('order2', $requests[1]->order);
    }

    public function testParseInvalidInput(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        RequestPayloadParser::parse('invalid_input');
    }
}