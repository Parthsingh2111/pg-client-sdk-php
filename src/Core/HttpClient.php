<?php

namespace PayGlocal\PgClientSdk\Core;

use PayGlocal\PgClientSdk\Utils\Logger;

/**
 * HTTP client using cURL with timeout protection
 * Matches JavaScript http behavior exactly
 */
class HttpClient
{
    private const TIMEOUT = 90000; // 90 second timeout
    private const SDK_VERSION = '2.0.0'; // Should match package.json version

    /**
     * Make HTTP request with timeout
     * @param string $url Request URL
     * @param array $options Request options
     * @return array Response data
     * @throws \Exception
     */
    public static function makeRequest(string $url, array $options = []): array
    {
        $ch = curl_init();
        
        // Set timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT / 1000); // Convert to seconds
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        // Set headers
        $headers = $options['headers'] ?? [];
        $headers['pg-sdk-version'] = 'PayGlocal-SDK/' . self::SDK_VERSION;
        
        if (!empty($headers)) {
            $headerLines = [];
            foreach ($headers as $key => $value) {
                $headerLines[] = "$key: $value";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerLines);
        }
        
        // Set method and data
        $method = strtoupper($options['method'] ?? 'GET');
        if ($method === 'POST' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (isset($options['body'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception("cURL error: $error");
        }
        
        if ($response === false) {
            throw new \Exception("cURL failed to get response");
        }
        
        if ($httpCode >= 400) {
            throw new \Exception("HTTP $httpCode: " . ($response ?: 'No response body'));
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON response: " . json_last_error_msg());
        }
        
        return $data;
    }

    /**
     * Make POST request
     * @param string $url Request URL
     * @param array|string $data Request payload
     * @param array $headers Request headers
     * @return array Response data
     * @throws \Exception
     */
    public static function post(string $url, $data = null, array $headers = []): array
    {
        try {
            Logger::logRequest('POST', $url, $headers, $data);
            
            $options = [
                'method' => 'POST',
                'headers' => array_merge(['Content-Type' => 'application/json'], $headers)
            ];
            
            // Handle different data types
            if (is_string($data)) {
                $options['body'] = $data;
                $options['headers']['Content-Type'] = 'text/plain';
            } elseif ($data !== null) {
                $options['body'] = json_encode($data);
            }
            
            $response = self::makeRequest($url, $options);
            Logger::logResponse('POST', $url, 200, $response);
            
            return $response ?: [];
        } catch (\Exception $error) {
            Logger::error("POST request failed: $url", $error);
            throw $error;
        }
    }

    /**
     * Make GET request
     * @param string $url Request URL
     * @param array $headers Request headers
     * @return array Response data
     * @throws \Exception
     */
    public static function get(string $url, array $headers = []): array
    {
        try {
            Logger::logRequest('GET', $url, $headers);
            
            $options = [
                'method' => 'GET',
                'headers' => $headers
            ];
            
            $response = self::makeRequest($url, $options);
            Logger::logResponse('GET', $url, 200, $response);
            
            return $response ?: [];
        } catch (\Exception $error) {
            Logger::error("GET request failed: $url", $error);
            throw $error;
        }
    }

    /**
     * Make PUT request
     * @param string $url Request URL
     * @param array|string $data Request payload
     * @param array $headers Request headers
     * @return array Response data
     * @throws \Exception
     */
    public static function put(string $url, $data = null, array $headers = []): array
    {
        try {
            Logger::logRequest('PUT', $url, $headers, $data);
            
            $options = [
                'method' => 'PUT',
                'headers' => array_merge(['Content-Type' => 'application/json'], $headers)
            ];
            
            // Handle different data types
            if (is_string($data)) {
                $options['body'] = $data;
                $options['headers']['Content-Type'] = 'text/plain';
            } elseif ($data !== null) {
                $options['body'] = json_encode($data);
            }
            
            $response = self::makeRequest($url, $options);
            Logger::logResponse('PUT', $url, 200, $response);
            
            return $response ?: [];
        } catch (\Exception $error) {
            Logger::error("PUT request failed: $url", $error);
            throw $error;
        }
    }
} 