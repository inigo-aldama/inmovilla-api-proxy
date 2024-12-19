<?php

namespace Inmovilla\Proxy;

use Inmovilla\ApiClient\ApiClient;
use Inmovilla\ApiClient\ApiClientConfig;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class ProxyService
{
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private ApiClientConfig $serverConfig;

    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        ApiClientConfig $serverConfig
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->serverConfig = $serverConfig;
    }

    public function handleRequest(string $input): array
    {
        $requestPayload = RequestPayloadParser::parse($input);

        $apiClient = new ApiClient(
            $requestPayload->agency,
            $requestPayload->password,
            $requestPayload->language,
            $this->serverConfig->getApiUrl(),
            $this->serverConfig->getDomain(),
            $this->httpClient,
            $this->requestFactory
        );

        return $apiClient->sendRequest($requestPayload->requests);
    }
}