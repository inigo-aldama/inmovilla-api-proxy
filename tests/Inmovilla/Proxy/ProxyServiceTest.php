<?php

namespace Inmovilla\Tests\Proxy;

use Inmovilla\Proxy\ProxyService;
use Inmovilla\ApiClient\ApiClient;
use Inmovilla\ApiClient\ApiClientConfig;
use Inmovilla\ApiClient\RequestBatch;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class ProxyServiceTest extends TestCase
{
    public function testHandleRequest(): void
    {
        $serverConfig = new ApiClientConfig('server-agency', 'server-password', 1, 'https://server.api.url', 'server-domain');

        $mockHttpClient = $this->createMock(ClientInterface::class);
        $mockRequestFactory = $this->createMock(RequestFactoryInterface::class);
        $mockApiClient = $this->createMock(ApiClient::class);

        $mockApiClient->expects($this->once())
            ->method('sendRequest')
            ->with($this->isInstanceOf(RequestBatch::class))
            ->willReturn(['response' => 'data']);

        $service = new ProxyService($mockHttpClient, $mockRequestFactory, $serverConfig);

        // Simulate valid input
        $input = 'param=agency;password;1;type1;1;100;;order1';
        $response = $service->handleRequest($input);

        $this->assertEquals(['response' => 'data'], $response);
    }

    public function testHandleRequestWithInvalidInput(): void
    {
        $serverConfig = new ApiClientConfig('server-agency', 'server-password', 1, 'https://server.api.url', 'server-domain');
        $mockHttpClient = $this->createMock(ClientInterface::class);
        $mockRequestFactory = $this->createMock(RequestFactoryInterface::class);

        $service = new ProxyService($mockHttpClient, $mockRequestFactory, $serverConfig);

        $this->expectException(\InvalidArgumentException::class);
        $service->handleRequest('invalid_input');
    }
}