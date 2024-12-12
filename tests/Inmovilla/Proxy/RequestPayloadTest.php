<?php

namespace Inmovilla\Tests\Proxy;

use Inmovilla\Proxy\RequestPayload;
use Inmovilla\ApiClient\Request;
use Inmovilla\ApiClient\RequestBatch;
use PHPUnit\Framework\TestCase;

class RequestPayloadTest extends TestCase
{
    public function testRequestPayloadInitialization(): void
    {
        $batch = new RequestBatch();
        $batch->addRequest(new Request('type1', 1, 100, '', 'order1'));
        $batch->addRequest(new Request('type2', 2, 50, 'where2', 'order2'));

        $payload = new RequestPayload('agency', 'password', 1, $batch);

        $this->assertEquals('agency', $payload->agency);
        $this->assertEquals('password', $payload->password);
        $this->assertEquals(1, $payload->language);
        $this->assertCount(2, $payload->requests->getRequests());
    }
}