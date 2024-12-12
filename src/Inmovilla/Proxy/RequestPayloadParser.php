<?php
namespace Inmovilla\Proxy;

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

        $requests = [];
        for ($i = 4; $i < count($parts); $i += 5) {
            $requests[] = [
                'type' => $parts[$i],
                'startPosition' => $parts[$i + 1] ?? null,
                'numElements' => $parts[$i + 2] ?? null,
                'where' => $parts[$i + 3] ?? null,
                'order' => $parts[$i + 4] ?? null,
            ];
        }

        return new RequestPayload($agency, $password, $language, $requests);
    }
}