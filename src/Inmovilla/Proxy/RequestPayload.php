<?php
namespace Inmovilla\Proxy;

class RequestPayload
{
    public string $agency;
    public string $password;
    public int $language;
    public array $requests;

    public function __construct(string $agency, string $password, int $language, array $requests)
    {
        $this->agency = $agency;
        $this->password = $password;
        $this->language = $language;
        $this->requests = $requests;
    }
}