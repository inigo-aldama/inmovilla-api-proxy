<?php

namespace Inmovilla\Tests\Proxy;

use Inmovilla\ApiClient\ApiClient;
use Inmovilla\ApiClient\ApiClientConfig;
use Inmovilla\ApiClient\ApiClientFactory;
use Inmovilla\Proxy\ProxyServer;
use Inmovilla\Proxy\RequestPayload;
use Inmovilla\Proxy\RequestPayloadParser;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class ProxyServerTest extends TestCase
{
    private ApiClientConfig $config;
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;

    protected function setUp(): void
    {
        // Crear una configuración simulada para el proxy
        $this->config = $this->createMock(ApiClientConfig::class);
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);
    }

    public function testHandleRequestValidInput(): void
    {
        // Crear un objeto `RequestPayload` simulado
        $requestPayload = new RequestPayload(
            'agencyCode',
            'password123',
            1,
            [
                ['type' => 'requestType1', 'startPosition' => 10, 'numElements' => 5, 'where' => 'where1', 'order' => 'order1']
            ]
        );

        // Simular el comportamiento de `RequestPayloadParser`
        $parserMock = $this->getMockBuilder(RequestPayloadParser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['parse'])
            ->getMock();
        $parserMock::staticExpects($this->once())
            ->method('parse')
            ->willReturn($requestPayload);

        // Simular el comportamiento del cliente API
        $apiClientMock = $this->createMock(ApiClient::class);
        $apiClientMock->expects($this->once())
            ->method('sendRequest')
            ->willReturn(['status' => 'success']);

        // Simular el comportamiento de la fábrica
        $factoryMock = $this->getMockBuilder(ApiClientFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createFromConfig'])
            ->getMock();
        $factoryMock::staticExpects($this->once())
            ->method('createFromConfig')
            ->willReturn($apiClientMock);

        // Crear una instancia de ProxyServer
        $proxyServer = new ProxyServer($this->config, $this->httpClient, $this->requestFactory);

        // Ejecutar la solicitud
        $proxyInput = 'param=agencyCode%3Bpassword123%3B1%3B%3BrequestType1%3B10%3B5%3Bwhere1%3Border1';
        $response = $proxyServer->handleRequest($proxyInput);

        // Verificar la respuesta
        $this->assertEquals(['status' => 'success'], $response);
    }

    public function testHandleRequestInvalidInputThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The input data does not contain the 'param' key.");

        // Crear una instancia de ProxyServer
        $proxyServer = new ProxyServer($this->config, $this->httpClient, $this->requestFactory);

        // Probar con un input inválido
        $proxyInput = 'invalidInput';
        $proxyServer->handleRequest($proxyInput);
    }
}