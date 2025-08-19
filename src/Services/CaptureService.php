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
 * Capture Service for handling capture operations
 * Matches JavaScript capture behavior exactly
 */
class CaptureService
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Common capture initiation function
     * @param array $params Capture parameters
     * @param Config $config Configuration
     * @return array Capture response
     * @throws \Exception
     */
    private function initiateCaptureOperation(array $params, Config $config): array
    {
        $gid = $params['gid'];
        $captureType = $params['captureType'];
        $merchantTxnId = $params['merchantTxnId'];
        
        Logger::info('Initiating capture operation', ['gid' => $gid, 'captureType' => $captureType, 'merchantTxnId' => $merchantTxnId]);
        
        // 1. Comprehensive validation (without schema validation for transaction services)
        ValidationHelper::validatePayload($params, [
            'requiredFields' => ['gid', 'merchantTxnId', 'captureType'], // All required fields
            'validateSchema' => false, // Disable schema validation for transaction services
            'operationType' => [
                'field' => 'captureType',
                'validTypes' => ['F', 'P']
            ],
            'conditionalValidation' => [
                'condition' => 'captureType',
                'value' => 'P',
                'requiredFields' => ['paymentData', 'paymentData.totalAmount']
            ]
        ]);

        // 3. Generate tokens (use the params we received)
        $tokens = TokenHelper::generateTokens($params, $config, 'capture');
        $requestData = $tokens['jwe'];
        $headers = HeaderHelper::buildJwtHeaders($tokens['jws']);

        // 4. API call
        $response = ApiRequestHelper::makeTransactionServiceRequest([
            'method' => 'POST',
            'baseUrl' => $config->baseUrl,
            'endpoint' => Endpoints::TRANSACTION_SERVICE['CAPTURE'],
            'gid' => $gid,
            'requestData' => $requestData,
            'headers' => $headers,
            'operation' => 'capture'
        ]);

        Logger::info('Capture operation completed');

        return $response;
    }

    /**
     * Initiate a capture.
     * @param array $params Capture parameters
     * @return array Capture response
     * @throws \Exception
     */
    public function initiateCapture(array $params): array
    {
        return $this->initiateCaptureOperation($params, $this->config);
    }
} 