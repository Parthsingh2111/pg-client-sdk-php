<?php

namespace PayGlocal\PgClientSdk\Services;

use PayGlocal\PgClientSdk\Core\Config;
use PayGlocal\PgClientSdk\Helper\TokenHelper;
use PayGlocal\PgClientSdk\Helper\ValidationHelper;
use PayGlocal\PgClientSdk\Helper\ApiRequestHelper;
use PayGlocal\PgClientSdk\Helper\HeaderHelper;
use PayGlocal\PgClientSdk\Constants\Endpoints;
use PayGlocal\PgClientSdk\Utils\Logger;

/**
 * Status Service for handling status check operations
 * Matches JavaScript status behavior exactly
 */
class StatusService
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Common status check initiation function
     * @param array $params Status check parameters
     * @param Config $config Configuration
     * @return array Status response
     * @throws \Exception
     */
    private function initiateCheckStatusOperation(array $params, Config $config): array
    {
        $gid = $params['gid'];
        
        Logger::info('Initiating status check', ['gid' => $gid]);
        
        // 1. Comprehensive validation (without schema validation for transaction services)
        ValidationHelper::validatePayload($params, [
            'requiredFields' => ['gid'],
            'validateSchema' => false // Disable schema validation for transaction services
        ]);

        // 3. Generate tokens (use the params we received)
        $endpointPath = "/gl/v1/payments/{$gid}/status";
        $tokens = TokenHelper::generateTokens([], $config, 'status', $endpointPath);
        
        $requestData = null;
        $headers = HeaderHelper::buildJwtHeaders($tokens['jws']);

        // 4. API call
        $response = ApiRequestHelper::makeTransactionServiceRequest([
            'method' => 'GET',
            'baseUrl' => $config->baseUrl,
            'endpoint' => Endpoints::TRANSACTION_SERVICE['STATUS'],
            'gid' => $gid,
            'requestData' => $requestData,
            'headers' => $headers,
            'operation' => 'status check'
        ]);

        Logger::info('Status check completed');

        return $response;
    }

    /**
     * Check payment status.
     * @param array $params Status check parameters
     * @return array Status response
     * @throws \Exception
     */
    public function initiateCheckStatus(array $params): array
    {
        return $this->initiateCheckStatusOperation($params, $this->config);
    }
} 