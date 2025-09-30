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
 * SI On-Demand Sale Service
 * Triggers SALE using mandateId
 */
class SiOnDemandService
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Initiate SI on-demand sale using mandateId
     * @param array $params expects ['mandateId' => string, 'merchantTxnId' => string]
     * @return array Response
     * @throws \Exception
     */
    public function initiateSiOnDemandVariable(array $params): array
    {
        Logger::info('Initiating SI on-demand sale', [
            'merchantTxnId' => $params['merchantTxnId'] ?? 'unknown',
            'mandateId' => $params['mandateId'] ?? null,
        ]);

        // Minimal validation (no schema)
        ValidationHelper::validatePayload($params, [
            'requiredFields' => [
                'merchantTxnId',
                'paymentData',
                'paymentData.totalAmount',
                'standingInstruction',
                'standingInstruction.mandateId'
            ],
            'validateSchema' => false
        ]);

        // Generate tokens (use the params we received)
        $tokens = TokenHelper::generateTokens($params, $this->config, 'SI on-demand sale');

        // Build headers
        $headers = HeaderHelper::buildSiHeaders($tokens['jws']);

        // API request to SALE
        $response = ApiRequestHelper::makeSiServiceRequest([
            'method' => 'POST',
            'baseUrl' => $this->config->baseUrl,
            'endpoint' => Endpoints::SI_SERVICE['SALE'],
            'requestData' => $tokens['jwe'],
            'headers' => $headers,
            'operation' => 'si on-demand, variable amount'
        ]);

        Logger::info('SI on-demand sale completed', [
            'merchantTxnId' => $params['merchantTxnId'] ?? 'unknown',
            'responseStatus' => $response['status'] ?? 'unknown'
        ]);

        return $response;
    }


     // si on demand fixed amount
    /**
     * Initiate SI on-demand sale using mandateId
     * @param array $params expects ['mandateId' => string, 'merchantTxnId' => string]
     * @return array Response
     * @throws \Exception
     */
    public function initiateSiOnDemandFixed(array $params): array
    {
        Logger::info('Initiating SI on-demand sale', [
            'merchantTxnId' => $params['merchantTxnId'] ?? 'unknown',
            'mandateId' => $params['mandateId'] ?? null,
        ]);

        // Minimal validation (no schema)
        ValidationHelper::validatePayload($params, [
            'requiredFields' => [
                'merchantTxnId',
                'standingInstruction',
                'standingInstruction.mandateId'
            ],
            'validateSchema' => false
        ]);

        // Generate tokens (use the params we received)
        $tokens = TokenHelper::generateTokens($params, $this->config, 'SI on-demand sale');

        // Build headers
        $headers = HeaderHelper::buildSiHeaders($tokens['jws']);

        // API request to SALE
        $response = ApiRequestHelper::makeSiServiceRequest([
            'method' => 'POST',
            'baseUrl' => $this->config->baseUrl,
            'endpoint' => Endpoints::SI_SERVICE['SALE'],
            'requestData' => $tokens['jwe'],
            'headers' => $headers,
            'operation' => 'si on-demand, fixed amount'
        ]);

        Logger::info('SI on-demand sale completed', [
            'merchantTxnId' => $params['merchantTxnId'] ?? 'unknown',
            'responseStatus' => $response['status'] ?? 'unknown'
        ]);

        return $response;
    }


} 