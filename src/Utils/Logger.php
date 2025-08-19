<?php

namespace PayGlocal\PgClientSdk\Utils;

/**
 * Enhanced Logger for PayGlocal SDK
 * Provides structured logging with different levels and formatting
 * Matches JavaScript logger behavior exactly
 */
class Logger
{
    private static array $levels = [
        'error' => 0,
        'warn' => 1,
        'info' => 2,
        'debug' => 3
    ];

    private static string $level = 'info';

    /**
     * Set log level
     * @param string $level Log level
     */
    public static function setLevel(string $level): void
    {
        self::$level = self::normalizeLevel($level);
    }

    /**
     * Normalize log level to handle case variations
     * @param string $level Log level
     * @return string Normalized level
     */
    private static function normalizeLevel(string $level): string
    {
        $normalized = strtolower($level);
        if (array_key_exists($normalized, self::$levels)) {
            return $normalized;
        }
        error_log("[LOGGER] Invalid log level \"$level\", defaulting to \"info\"");
        return 'info';
    }

    /**
     * Check if a log level should be output
     * @param string $level Log level to check
     * @return bool Whether to log
     */
    private static function shouldLog(string $level): bool
    {
        return self::$levels[$level] <= self::$levels[self::$level];
    }

    /**
     * Get current timestamp
     * @return string ISO timestamp
     */
    private static function getTimestamp(): string
    {
        return date('c');
    }

    /**
     * Format log message with timestamp and level
     * @param string $level Log level
     * @param string $message Log message
     * @param mixed $data Additional data to log
     * @return string Formatted log message
     */
    private static function formatMessage(string $level, string $message, $data = null): string
    {
        $timestamp = self::getTimestamp();
        $prefix = "[$timestamp] [" . strtoupper($level) . "] [PAYGLOCAL-SDK]";
        
        if ($data !== null) {
            return "$prefix $message " . json_encode($data, JSON_PRETTY_PRINT);
        }
        return "$prefix $message";
    }

    /**
     * Log error messages (always shown)
     * @param string $message Error message
     * @param \Exception|mixed $error Error object or additional data
     */
    public static function error(string $message, $error = null): void
    {
        if (!self::shouldLog('error')) return;
        
        if ($error instanceof \Exception) {
            error_log(self::formatMessage('error', $message, [
                'error' => $error->getMessage(),
                'stack' => $error->getTraceAsString(),
                'name' => get_class($error)
            ]));
        } else {
            error_log(self::formatMessage('error', $message, $error));
        }
    }

    /**
     * Log warning messages
     * @param string $message Warning message
     * @param mixed $data Additional data
     */
    public static function warn(string $message, $data = null): void
    {
        if (!self::shouldLog('warn')) return;
        error_log(self::formatMessage('warn', $message, $data));
    }

    /**
     * Log info messages
     * @param string $message Info message
     * @param mixed $data Additional data
     */
    public static function info(string $message, $data = null): void
    {
        if (!self::shouldLog('info')) return;
        error_log(self::formatMessage('info', $message, $data));
    }

    /**
     * Log debug messages
     * @param string $message Debug message
     * @param mixed $data Additional data
     */
    public static function debug(string $message, $data = null): void
    {
        if (!self::shouldLog('debug')) return;
        error_log(self::formatMessage('debug', $message, $data));
    }

    /**
     * Log HTTP request
     * @param string $method HTTP method
     * @param string $url Request URL
     * @param array $headers Request headers
     * @param mixed $data Request data
     */
    public static function logRequest(string $method, string $url, array $headers = [], $data = null): void
    {
        if (!self::shouldLog('debug')) return;

        // Mask sensitive headers
        $maskedHeaders = $headers;
        $sensitiveKeys = ['x-gl-auth', 'x-gl-token-external', 'authorization'];
        foreach ($sensitiveKeys as $key) {
            if (isset($maskedHeaders[$key])) {
                $maskedHeaders[$key] = '***MASKED***';
            }
        }

        self::debug("HTTP Request", [
            'method' => $method,
            'url' => $url,
            'headers' => $maskedHeaders,
            'data' => $data
        ]);
    }

    /**
     * Log HTTP response
     * @param string $method HTTP method
     * @param string $url Request URL
     * @param int $status Response status
     * @param mixed $data Response data
     */
    public static function logResponse(string $method, string $url, int $status, $data = null): void
    {
        if (!self::shouldLog('debug')) return;

        self::debug("HTTP Response", [
            'method' => $method,
            'url' => $url,
            'status' => $status,
            'data' => $data
        ]);
    }

    /**
     * Log configuration
     * @param object $config Configuration object
     */
    public static function logConfig(object $config): void
    {
        if (!self::shouldLog('debug')) return;

        // Mask sensitive config values
        $maskedConfig = [
            'apiKey' => isset($config->apiKey) ? '***MASKED***' : null,
            'merchantId' => $config->merchantId ?? null,
            'publicKeyId' => $config->publicKeyId ?? null,
            'privateKeyId' => $config->privateKeyId ?? null,
            'payglocalPublicKey' => isset($config->payglocalPublicKey) ? '***MASKED***' : null,
            'merchantPrivateKey' => isset($config->merchantPrivateKey) ? '***MASKED***' : null,
            'payglocalEnv' => $config->payglocalEnv ?? null,
            'baseUrl' => $config->baseUrl ?? null,
            'logLevel' => $config->logLevel ?? null,
            'tokenExpiration' => $config->tokenExpiration ?? null
        ];

        self::debug('SDK Configuration', $maskedConfig);
    }
} 