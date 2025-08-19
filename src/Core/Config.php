<?php

namespace PayGlocal\PgClientSdk\Core;

use PayGlocal\PgClientSdk\Utils\Logger;

/**
 * Configuration class for PayGlocalClient.
 * Matches JavaScript Config behavior exactly
 */
class Config
{
    public string $apiKey = '';
    public string $merchantId = '';
    public string $publicKeyId = '';
    public string $privateKeyId = '';
    public string $payglocalPublicKey = '';
    public string $merchantPrivateKey = '';
    public string $payglocalEnv = '';
    public string $baseUrl = '';
    public string $logLevel = 'info';
    public int $tokenExpiration = 300000;

    private const BASE_URLS = [
        'UAT' => 'https://api.uat.payglocal.in',
        'PROD' => 'https://api.payglocal.in',
    ];

    /**
     * Constructor
     * @param array $config Configuration object
     * @throws \Exception
     */
    public function __construct(array $config = [])
    {
        try {
            $this->apiKey = $config['apiKey'] ?? '';
            $this->merchantId = $config['merchantId'] ?? '';
            $this->publicKeyId = $config['publicKeyId'] ?? '';
            $this->privateKeyId = $config['privateKeyId'] ?? '';
            $this->payglocalPublicKey = $config['payglocalPublicKey'] ?? '';
            $this->merchantPrivateKey = $config['merchantPrivateKey'] ?? '';
            $this->payglocalEnv = $config['payglocalEnv'] ?? '';

            if (empty($this->payglocalEnv)) {
                throw new \Exception('Missing required configuration: payglocalEnv for base URL');
            }

            // handling the base URL(choosing and switching) according to the environment chosen for transaction by the merchant in the .env
            $baseUrlEnv = strtoupper($this->payglocalEnv);

            if (!isset(self::BASE_URLS[$baseUrlEnv])) {
                Logger::error("Invalid environment \"{$baseUrlEnv}\" provided. Must be \"UAT\" or \"PROD\".");
                throw new \Exception("Invalid environment \"{$baseUrlEnv}\" provided. Must be \"UAT\" or \"PROD\".");
            }

            $this->baseUrl = self::BASE_URLS[$baseUrlEnv];
            $this->logLevel = $config['logLevel'] ?? 'info';
            $this->tokenExpiration = $config['tokenExpiration'] ?? 300000;

            // Validate required fields
            if (empty($this->merchantId)) {
                throw new \Exception('Missing required configuration: merchantId');
            }

            // If API key is provided, token fields are not needed(we don't need the token based credentials)
            if (!empty($this->apiKey)) {
                // API Key authentication - no need for token fields
            } else {
                // Token authentication - require all token fields(we need the token based credentials)
                if (empty($this->publicKeyId) || empty($this->privateKeyId) || empty($this->payglocalPublicKey) || empty($this->merchantPrivateKey)) {
                    throw new \Exception('Missing required configuration for token authentication: publicKeyId, privateKeyId, payglocalPublicKey, merchantPrivateKey');
                }
            }

        } catch (\Exception $error) {
            Logger::error('Configuration error: ' . $error->getMessage());
            throw new \Exception($error->getMessage() ?: 'Configuration initialization failed');
        }
    }
} 