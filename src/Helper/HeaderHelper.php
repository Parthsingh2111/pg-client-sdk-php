<?php

namespace PayGlocal\PgClientSdk\Helper;

/**
 * Build JWT headers with token
 */
class HeaderHelper
{
    /**
     * Build JWT headers with token 
     * @param string $jws JWS token
     * @param array $customHeaders Additional custom headers
     * @return array Headers object
     */
    public static function buildJwtHeaders(string $jws, array $customHeaders = []): array
    {
        return array_merge([
            'Content-Type' => 'text/plain',
            'x-gl-token-external' => $jws,
        ], $customHeaders);
    }

    /**
     * Build API key headers
     * @param string $apiKey API key
     * @param array $customHeaders Additional custom headers
     * @return array Headers object
     */
    public static function buildApiKeyHeaders(string $apiKey, array $customHeaders = []): array
    {
        return array_merge([
            'Content-Type' => 'application/json',
            'x-gl-auth' => $apiKey,
        ], $customHeaders);
    }

    /**
     * Build headers for SI operations 
     * @param string $jws JWS token
     * @param array $customHeaders Additional custom headers
     * @return array Headers object
     */
    public static function buildSiHeaders(string $jws, array $customHeaders = []): array
    {
        return array_merge([
            'Content-Type' => 'text/plain',
            'x-gl-token-external' => $jws,
        ], $customHeaders);
    }
} 