<?php
namespace Inmovilla\Proxy;

use Inmovilla\ApiClient\RequestBatch;

class RequestPayload
{
    public string $agency;
    public string $password;
    public int $language;
    public RequestBatch $requests;

    public function __construct(string $agency, string $password, int $language, RequestBatch $requests)
    {
        $this->agency = $agency;
        $this->password = $password;
        $this->language = $language;
        $this->requests = $requests;
    }
}