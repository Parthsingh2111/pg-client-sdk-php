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
 * Refund Service for handling refund operations
 * Matches JavaScript refund behavior exactly
 */
class RefundService
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Common refund initiation function
     * @param array $params Refund parameters
     * @param Config $config Configuration
     * @return array Refund response
     * @throws \Exception
     */
    private function initiateRefundOperation(array $params, Config $config): array
    {
        $gid = $params['gid'];
        $refundType = $params['refundType'];
        $merchantTxnId = $params['merchantTxnId'];
        
        Logger::info('Initiating refund operation', ['gid' => $gid, 'refundType' => $refundType, 'merchantTxnId' => $merchantTxnId]);
        
        // 1. Comprehensive validation (without schema validation for transaction services)
        ValidationHelper::validatePayload($params, [
            'requiredFields' => ['gid', 'merchantTxnId', 'refundType'], // All required fields
            'validateSchema' => false, // Disable schema validation for transaction services
            'operationType' => [
                'field' => 'refundType',
                'validTypes' => ['F', 'P']
            ],
            'conditionalValidation' => [
                'condition' => 'refundType',
                'value' => 'P',
                'requiredFields' => ['paymentData', 'paymentData.totalAmount']
            ]
        ]);

        // 3. Generate tokens (use the params we received)
        $tokens = TokenHelper::generateTokens($params, $config, 'refund');
        $requestData = $tokens['jwe'];
        $headers = HeaderHelper::buildJwtHeaders($tokens['jws']);

        // 4. API call
        $response = ApiRequestHelper::makeTransactionServiceRequest([
            'method' => 'POST',
            'baseUrl' => $config->baseUrl,
            'endpoint' => Endpoints::TRANSACTION_SERVICE['REFUND'],
            'gid' => $gid,
            'requestData' => $requestData,
            'headers' => $headers,
            'operation' => 'refund'
        ]);

        Logger::info('Refund operation completed');

        return $response;
    }

    /**
     * Initiate a refund.
     * @param array $params Refund parameters
     * @return array Refund response
     * @throws \Exception
     */
    public function initiateRefund(array $params): array
    {
        return $this->initiateRefundOperation($params, $this->config);
    }
} 