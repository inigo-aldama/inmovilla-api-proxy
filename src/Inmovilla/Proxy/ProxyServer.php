<?php

namespace Inmovilla\Proxy;

use Inmovilla\ApiClient\ApiClientConfig;
use Inmovilla\ApiClient\ApiClientFactory;
use Inmovilla\ApiClient\ApiClientInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class ProxyServer
{
    private ApiClientConfig $serverConfig;
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;

    public function __construct(
        ApiClientConfig $serverConfig,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory
    ) {
        $this->serverConfig = $serverConfig;
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    public function handleRequest(string $proxyInput): array
    {
        $requestPayload = RequestPayloadParser::parse($proxyInput);

        $config = $this->createProxyConfig($requestPayload);

        $client = $this->createApiClient($config);

        foreach ($requestPayload->requests as $request) {
            $client->addRequest(...array_values($request));
        }

        return $client->sendRequest();
    }

    private function createProxyConfig(RequestPayload $requestPayload): ApiClientConfig
    {
        return new ApiClientConfig(
            $requestPayload->agency,
            $requestPayload->password,
            $requestPayload->language,
            $this->serverConfig->getApiUrl(),
            $this->serverConfig->getDomain()
        );
    }

    private function createApiClient(ApiClientConfig $config): ApiClientInterface
    {
        return ApiClientFactory::createFromConfig(
            $config,
            $this->httpClient,
            $this->requestFactory
        );
    }
}