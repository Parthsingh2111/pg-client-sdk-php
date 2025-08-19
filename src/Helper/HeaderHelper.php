<?php

namespace PayGlocal\PgClientSdk\Helper;

/**
 * Build JWT headers with token
 * Matches JavaScript headerHelper behavior exactly
 */
class HeaderHelper
{
    /**
     * Build JWT headers with token (matches JavaScript exactly)
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
     * Build API key headers (matches JavaScript exactly)
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
     * Build headers for SI operations (matches JavaScript exactly)
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