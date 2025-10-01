<?php

namespace PayGlocal\PgClientSdk\Services;

use PayGlocal\PgClientSdk\Core\Config;
use PayGlocal\PgClientSdk\Helper\TokenHelper;
use PayGlocal\PgClientSdk\Helper\ValidationHelper;
use PayGlocal\PgClientSdk\Helper\ApiRequestHelper;
use PayGlocal\PgClientSdk\Helper\HeaderHelper;
use PayGlocal\PgClientSdk\Constants\Endpoints;
use PayGlocal\PgClientSdk\Utils\Logger;

class SiStatusService
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Check SI status using mandateId
     * @param array $params expects ['standingInstruction' => ['mandateId' => string]]
     * @return array Response
     * @throws \Exception
     */
    public function initiateSiStatusCheck(array $params): array
    {
        Logger::info('Initiating SI status check');

        ValidationHelper::validatePayload($params, [
            'requiredFields' => [ 'standingInstruction', 'standingInstruction.mandateId' ],
            'validateSchema' => false
        ]);

        $tokens = TokenHelper::generateTokens($params, $this->config, 'SI status');
        $headers = HeaderHelper::buildSiHeaders($tokens['jws']);

        $response = ApiRequestHelper::makeSiServiceRequest([
            'method' => 'POST',
            'baseUrl' => $this->config->baseUrl,
            'endpoint' => Endpoints::SI_SERVICE['STATUS'],
            'requestData' => $tokens['jwe'],
            'headers' => $headers,
            'operation' => 'SI status check'
        ]);

        Logger::info('SI status check completed');
        return $response;
    }
} 