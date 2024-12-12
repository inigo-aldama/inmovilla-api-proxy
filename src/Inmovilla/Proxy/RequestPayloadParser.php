<?php
namespace Inmovilla\Proxy;

use Inmovilla\ApiClient\Request;
use Inmovilla\ApiClient\RequestBatch;
use InvalidArgumentException;

class RequestPayloadParser
{
    public static function parse(string $proxyData): RequestPayload
    {
        parse_str($proxyData, $parsedParams);

        if (!isset($parsedParams['param'])) {
            throw new InvalidArgumentException("The input data does not contain the 'param' key.");
        }

        $decodedString = rawurldecode($parsedParams['param']);
        $parts = explode(';', $decodedString);

        if (count($parts) < 4) {
            throw new InvalidArgumentException("The 'param' key does not contain enough parts to process.");
        }

        $agency = $parts[0];
        $password = $parts[1];
        $language = (int)$parts[2];

        $requests = new RequestBatch();
        for ($i = 3; $i < count($parts); $i += 5) {
            $type = $parts[$i];
            $startPosition = (int) $parts[$i + 1] ?? 1;
            $numElements = (int) $parts[$i + 2] ?? 100;
            $where = $parts[$i + 3] ?? '';
            $order = $parts[$i + 4] ?? '';
            $request = new Request ($type,$startPosition, $numElements,$where,$order);
            $requests->addRequest($request);
        }
        return new RequestPayload($agency, $password, $language, $requests);
    }
}