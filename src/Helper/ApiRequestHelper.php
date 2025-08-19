<?php

namespace PayGlocal\PgClientSdk\Helper;

use PayGlocal\PgClientSdk\Core\HttpClient;
use PayGlocal\PgClientSdk\Utils\Logger;
use PayGlocal\PgClientSdk\Constants\Endpoints;

/**
 * Make API request with simple error handling
 * Matches JavaScript apiRequestHelper behavior exactly
 */
class ApiRequestHelper
{
    /**
     * Make API request with simple error handling
     * @param array $options Request options
     * @param string $options['method'] HTTP method
     * @param string $options['baseUrl'] Base URL
     * @param string $options['endpoint'] Endpoint path
     * @param array $options['endpointParams'] Parameters for endpoint building
     * @param mixed $options['data'] Request data/payload
     * @param array $options['headers'] Request headers
     * @return array API response
     * @throws \Exception
     */
    public static function makeApiRequest(array $options = []): array
    {
        $method = $options['method'] ?? 'POST';
        $baseUrl = $options['baseUrl'] ?? '';
        $endpoint = $options['endpoint'] ?? '';
        $endpointParams = $options['endpointParams'] ?? [];
        $data = $options['data'] ?? null;
        $headers = $options['headers'] ?? [];

        try {
            // Build full URL
            $fullEndpoint = Endpoints::buildEndpoint($endpoint, $endpointParams);
            $fullUrl = $baseUrl . $fullEndpoint;

            // Make the API call
            if (strtoupper($method) === 'GET') {
                $response = HttpClient::get($fullUrl, $headers);
            } elseif (strtoupper($method) === 'PUT') {
                $response = HttpClient::put($fullUrl, $data, $headers);
            } else {
                $response = HttpClient::post($fullUrl, $data, $headers);
            }

            // Validate response exists
            if (empty($response)) {
                throw new \Exception('Empty response from API');
            }

            return $response;

        } catch (\Exception $error) {
            // Simply re-throw the error as-is
            throw $error;
        }
    }

    /**
     * Make payment request
     * @param array $options Request options
     * @return array API response
     * @throws \Exception
     */
    public static function makePaymentRequest(array $options): array
    {
        $method = $options['method'] ?? 'POST';
        $baseUrl = $options['baseUrl'] ?? '';
        $endpoint = $options['endpoint'] ?? '';
        $requestData = $options['requestData'] ?? null;
        $headers = $options['headers'] ?? [];
        
        return self::makeApiRequest([
            'method' => $method,
            'baseUrl' => $baseUrl,
            'endpoint' => $endpoint,
            'data' => $requestData,
            'headers' => $headers
        ]);
    }

    /**
     * Make transaction service request
     * @param array $options Request options
     * @return array API response
     * @throws \Exception
     */
    public static function makeTransactionServiceRequest(array $options): array
    {
        $method = $options['method'] ?? 'POST';
        $baseUrl = $options['baseUrl'] ?? '';
        $endpoint = $options['endpoint'] ?? '';
        $gid = $options['gid'] ?? '';
        $requestData = $options['requestData'] ?? null;
        $headers = $options['headers'] ?? [];
        $operation = $options['operation'] ?? '';
        
        return self::makeApiRequest([
            'method' => $method,
            'baseUrl' => $baseUrl,
            'endpoint' => $endpoint,
            'endpointParams' => ['gid' => $gid],
            'data' => $requestData,
            'headers' => $headers
        ]);
    }

    /**
     * Make SI service request
     * @param array $options Request options
     * @return array API response
     * @throws \Exception
     */
    public static function makeSiServiceRequest(array $options): array
    {
        $method = $options['method'] ?? 'POST';
        $baseUrl = $options['baseUrl'] ?? '';
        $endpoint = $options['endpoint'] ?? '';
        $requestData = $options['requestData'] ?? null;
        $headers = $options['headers'] ?? [];
        $operation = $options['operation'] ?? '';
        
        return self::makeApiRequest([
            'method' => $method,
            'baseUrl' => $baseUrl,
            'endpoint' => $endpoint,
            'data' => $requestData,
            'headers' => $headers
        ]);
    }
} 