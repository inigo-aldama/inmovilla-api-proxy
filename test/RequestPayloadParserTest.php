<?php

namespace Inmovilla\Tests\Proxy;

use Inmovilla\Proxy\RequestPayload;
use Inmovilla\Proxy\RequestPayloadParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RequestPayloadParserTest extends TestCase
{
    public function testParseValidInput(): void
    {
        $input = 'param=agencyCode%3Bpassword123%3B1%3B%3BrequestType1%3B10%3B5%3Bwhere1%3Border1';
        $expectedRequests = [
            [
                'type' => 'requestType1',
                'startPosition' => '10',
                'numElements' => '5',
                'where' => 'where1',
                'order' => 'order1',
            ]
        ];

        $payload = RequestPayloadParser::parse($input);

        $this->assertInstanceOf(RequestPayload::class, $payload);
        $this->assertEquals('agencyCode', $payload->agency);
        $this->assertEquals('password123', $payload->password);
        $this->assertEquals(1, $payload->language);
        $this->assertEquals($expectedRequests, $payload->requests);
    }

    public function testParseInvalidInputThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The input data does not contain the 'param' key.");

        $input = 'invalidInput';
        RequestPayloadParser::parse($input);
    }

    public function testParseInputWithInsufficientPartsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The 'param' key does not contain enough parts to process.");

        $input = 'param=agencyCode%3Bpassword123%3B1';
        RequestPayloadParser::parse($input);
    }

    public function testParseInputWithMultipleRequests(): void
    {
        $input = 'param=agencyCode%3Bpassword123%3B1%3B%3BrequestType1%3B10%3B5%3Bwhere1%3Border1%3BrequestType2%3B15%3B3%3Bwhere2%3Border2';
        $expectedRequests = [
            [
                'type' => 'requestType1',
                'startPosition' => '10',
                'numElements' => '5',
                'where' => 'where1',
                'order' => 'order1',
            ],
            [
                'type' => 'requestType2',
                'startPosition' => '15',
                'numElements' => '3',
                'where' => 'where2',
                'order' => 'order2',
            ],
        ];

        $payload = RequestPayloadParser::parse($input);

        $this->assertInstanceOf(RequestPayload::class, $payload);
        $this->assertEquals('agencyCode', $payload->agency);
        $this->assertEquals('password123', $payload->password);
        $this->assertEquals(1, $payload->language);
        $this->assertEquals($expectedRequests, $payload->requests);
    }
}