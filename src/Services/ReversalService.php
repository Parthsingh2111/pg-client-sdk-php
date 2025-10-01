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
 * Reversal Service for handling reversal operations
 */
class ReversalService
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Common auth reversal initiation function
     * @param array $params Reversal parameters
     * @param Config $config Configuration
     * @return array Reversal response
     * @throws \Exception
     */
    private function initiateAuthReversalOperation(array $params, Config $config): array
    {
        $gid = $params['gid'];
        $merchantTxnId = $params['merchantTxnId'];
        
        Logger::info('Initiating auth reversal operation', ['gid' => $gid, 'merchantTxnId' => $merchantTxnId]);
        
        // 1. Comprehensive validation (without schema validation for transaction services)
        ValidationHelper::validatePayload($params, [
            'requiredFields' => ['gid', 'merchantTxnId'],
            'validateSchema' => false // Disable schema validation for transaction services
        ]);

        // 3. Generate tokens (use the params we received)
        $tokens = TokenHelper::generateTokens($params, $config, 'auth-reversal');
        $requestData = $tokens['jwe'];
        $headers = HeaderHelper::buildJwtHeaders($tokens['jws']);

        // 4. API call
        $response = ApiRequestHelper::makeTransactionServiceRequest([
            'method' => 'POST',
            'baseUrl' => $config->baseUrl,
            'endpoint' => Endpoints::TRANSACTION_SERVICE['AUTH_REVERSAL'],
            'gid' => $gid,
            'requestData' => $requestData,
            'headers' => $headers,
            'operation' => 'auth reversal'
        ]);

        Logger::info('Auth reversal operation completed');

        return $response;
    }

    /**
     * Initiate an auth reversal.
     * @param array $params Reversal parameters
     * @return array Reversal response
     * @throws \Exception
     */
    public function initiateAuthReversal(array $params): array
    {
        return $this->initiateAuthReversalOperation($params, $this->config);
    }
} 