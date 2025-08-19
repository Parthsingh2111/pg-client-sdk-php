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
 * Payment Service for handling all payment types
 * Matches JavaScript payment behavior exactly
 */
class PaymentService
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Main Payment Initiator
     * @param array $params Payment parameters
     * @param Config $config Configuration object
     * @param array $options Payment options
     * @return array Payment response
     * @throws \Exception
     */
    private function initiatePayment(array $params, Config $config, array $options = []): array
    {
        $operation = $options['operation'] ?? 'payment';
        $endpoint = $options['endpoint'] ?? Endpoints::PAYMENT['INITIATE'];
        $requiredFields = $options['requiredFields'] ?? [];
        $useJWT = $options['useJWT'] ?? true;
        $customValidation = $options['customValidation'] ?? null;
        $customHeaders = $options['customHeaders'] ?? [];

        Logger::info("Initiating $operation", [
            'merchantTxnId' => $params['merchantTxnId'] ?? 'unknown',
            'useJWT' => $useJWT,
            'endpoint' => $endpoint
        ]);

        // 1-3. Comprehensive Validation (Schema, Custom, Required Fields)
        ValidationHelper::validatePayload($params, [
            'operation' => $operation,
            'requiredFields' => $requiredFields,
            'customValidation' => $customValidation
        ]);

        // 4. Custom Validation Processing (Payment-specific business logic)
        if ($customValidation && is_callable($customValidation)) {
            Logger::debug("Executing custom validation for $operation");
            $customValidation($params);
        }

        // 5. Token Generation and Header Building
        $requestData = null;
        $headers = [];
        if ($useJWT) {
            $tokens = TokenHelper::generateTokens($params, $config, $operation);
            $requestData = $tokens['jwe'];
            $headers = HeaderHelper::buildJwtHeaders($tokens['jws'], $customHeaders);
        } else {
            $requestData = $params;
            $headers = HeaderHelper::buildApiKeyHeaders($config->apiKey, $customHeaders);
        }

        // 6. API Call with Response Processing
        $response = ApiRequestHelper::makePaymentRequest([
            'method' => 'POST',
            'baseUrl' => $config->baseUrl,
            'endpoint' => $endpoint,
            'requestData' => $requestData,
            'headers' => $headers,
            'operation' => $operation
        ]);

        Logger::info("$operation completed successfully", [
            'merchantTxnId' => $params['merchantTxnId'] ?? 'unknown',
            'responseStatus' => $response['status'] ?? 'unknown'
        ]);

        return $response;
    }

    /**
     * Initiate API Key-based payment
     * @param array $params Payment parameters
     * @return array Payment response
     * @throws \Exception
     */
    public function initiateApiKeyPayment(array $params): array
    {
        return $this->initiatePayment($params, $this->config, [
            'operation' => 'API key payment',
            'useJWT' => false,
            'requiredFields' => [
                'merchantTxnId',
                'paymentData',
                'merchantCallbackURL',
                'paymentData.totalAmount',
                'paymentData.txnCurrency'
            ],
            'customHeaders' => [
                'x-gl-auth' => $this->config->apiKey
            ]
        ]);
    }

    /**
     * Initiate JWT-based payment
     * @param array $params Payment parameters
     * @return array Payment response
     * @throws \Exception
     */
    public function initiateJwtPayment(array $params): array
    {
        return $this->initiatePayment($params, $this->config, [
            'operation' => 'JWT payment',
            'useJWT' => true,
            'requiredFields' => [
                'merchantTxnId',
                'paymentData',
                'merchantCallbackURL',
                'paymentData.totalAmount',
                'paymentData.txnCurrency'
            ]
        ]);
    }

    /**
     * Initiate Standing Instruction (SI) payment
     * @param array $params Payment parameters
     * @return array Payment response
     * @throws \Exception
     */
    public function initiateSiPayment(array $params): array
    {
        return $this->initiatePayment($params, $this->config, [
            'operation' => 'SI payment',
            'useJWT' => true,
            'requiredFields' => [
                'merchantTxnId',
                'paymentData',
                'standingInstruction',
                'merchantCallbackURL',
                'paymentData.totalAmount',
                'paymentData.txnCurrency',
                'standingInstruction.data',
                'standingInstruction.data.numberOfPayments',
                'standingInstruction.data.frequency',
                'standingInstruction.data.type',
            ],
            'customValidation' => function($params) {
                // Custom validation for SI payment
                if (isset($params['standingInstruction']['action'])) {
                    $validActions = ['PAUSE', 'ACTIVATE'];
                    if (!in_array(strtoupper($params['standingInstruction']['action']), $validActions)) {
                        throw new \Exception('Invalid action in standingInstruction. Must be one of: ' . implode(', ', $validActions));
                    }
                }
            }
        ]);
    }

    /**
     * Initiate Auth payment
     * @param array $params Payment parameters
     * @return array Payment response
     * @throws \Exception
     */
    public function initiateAuthPayment(array $params): array
    {
        return $this->initiatePayment($params, $this->config, [
            'operation' => 'Auth payment',
            'useJWT' => true,
            'requiredFields' => [
                'merchantTxnId',
                'paymentData',
                'merchantCallbackURL',
                'paymentData.totalAmount',
                'paymentData.txnCurrency'
            ],
            'customValidation' => function($params) {
                // Custom validation for Auth payment
                if (isset($params['paymentData']['totalAmount'])) {
                    if (!is_numeric($params['paymentData']['totalAmount']) || $params['paymentData']['totalAmount'] <= 0) {
                        throw new \Exception('Invalid totalAmount. Must be a positive number.');
                    }
                }
            }
        ]);
    }
} 