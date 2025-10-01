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
 * Standing Instruction Update Service
 */
class SiUpdateService
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Initiate SI modification (PAUSE)
     * @param array $params SI parameters
     * @return array Response
     * @throws \Exception
     */
    public function initiatePauseSI(array $params): array
    {
        Logger::info('Initiating SI pause', [
            'merchantTxnId' => $params['merchantTxnId'] ?? 'unknown'
        ]);

        // Build conditional validation only if data is present
        $conditionalValidation = null;
        if (isset($params['standingInstruction']) && isset($params['standingInstruction']['data'])) {
            $conditionalValidation = [
                'condition' => 'standingInstruction.data',
                'value' => $params['standingInstruction']['data'],
                'requiredFields' => ['standingInstruction.data.startDate']
            ];
        }

        // Validate payload (do NOT require data for indefinite pause)
        ValidationHelper::validatePayload($params, [
            'requiredFields' => [
                'merchantTxnId',
                'standingInstruction',
                'standingInstruction.action'
            ],
            'validateSchema' => false,
            'operationType' => [
                'field' => 'standingInstruction.action',
                'validTypes' => ['PAUSE']
            ],
            'conditionalValidation' => $conditionalValidation
        ]);

        // Generate tokens
        $tokens = TokenHelper::generateTokens($params, $this->config, 'SI pause');
        
        // Build headers
        $headers = HeaderHelper::buildSiHeaders($tokens['jws']);

        // Make API request (POST to MODIFY like JS)
        $response = ApiRequestHelper::makeSiServiceRequest([
            'method' => 'POST',
            'baseUrl' => $this->config->baseUrl,
            'endpoint' => Endpoints::SI_SERVICE['MODIFY'],
            'requestData' => $tokens['jwe'],
            'headers' => $headers,
            'operation' => 'SI pause'
        ]);

        Logger::info('SI pause completed successfully', [
            'merchantTxnId' => $params['merchantTxnId'] ?? 'unknown',
            'responseStatus' => $response['status'] ?? 'unknown'
        ]);

        return $response;
    }

    /**
     * Initiate SI status update (ACTIVATE)
     * @param array $params SI parameters
     * @return array Response
     * @throws \Exception
     */
    public function initiateActivateSI(array $params): array
    {
        Logger::info('Initiating SI activate', [
            'merchantTxnId' => $params['merchantTxnId'] ?? 'unknown'
        ]);

        // Validate payload
        ValidationHelper::validatePayload($params, [
            'requiredFields' => [
                'merchantTxnId',
                'standingInstruction',
                'standingInstruction.action'
            ],
            'validateSchema' => false,
            'operationType' => [
                'field' => 'standingInstruction.action',
                'validTypes' => ['ACTIVATE']
            ]
        ]);

        // Generate tokens
        $tokens = TokenHelper::generateTokens($params, $this->config, 'SI activate');
        
        // Build headers
        $headers = HeaderHelper::buildSiHeaders($tokens['jws']);

        // Make API request (PUT to STATUS like JS)
        $response = ApiRequestHelper::makeSiServiceRequest([
            'method' => 'PUT',
            'baseUrl' => $this->config->baseUrl,
            'endpoint' => Endpoints::SI_SERVICE['STATUS'],
            'requestData' => $tokens['jwe'],
            'headers' => $headers,
            'operation' => 'SI activate'
        ]);

        Logger::info('SI activate completed successfully', [
            'merchantTxnId' => $params['merchantTxnId'] ?? 'unknown',
            'responseStatus' => $response['status'] ?? 'unknown'
        ]);

        return $response;
    }
} 