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

                if (!isset($params['standingInstruction']['data']) || !is_array($params['standingInstruction']['data'])) {
                    throw new \Exception('standingInstruction.data is required');
                }

                $data = $params['standingInstruction']['data'];

                // type must be FIXED or VARIABLE
                if (!isset($data['type']) || !in_array(strtoupper($data['type']), ['FIXED', 'VARIABLE'])) {
                    throw new \Exception('Invalid SI type. Must be either FIXED or VARIABLE');
                }

                $type = strtoupper($data['type']);

                // startDate required for FIXED, must not be present for VARIABLE
                if ($type === 'FIXED' && empty($data['startDate'])) {
                    throw new \Exception('startDate is required for FIXED SI type');
                }
                if ($type === 'VARIABLE' && isset($data['startDate']) && $data['startDate'] !== '' && $data['startDate'] !== null) {
                    throw new \Exception('startDate should not be included for VARIABLE SI type');
                }

                // Either amount or maxAmount must be present
                $hasAmount = isset($data['amount']) && $data['amount'] !== '' && $data['amount'] !== null;
                $hasMaxAmount = isset($data['maxAmount']) && $data['maxAmount'] !== '' && $data['maxAmount'] !== null;
                if (!($hasAmount || $hasMaxAmount)) {
                    throw new \Exception('Either amount or maxAmount is required for standingInstruction.data');
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
                // captureTxn must be false for Auth payment
                if (!array_key_exists('captureTxn', $params) || is_null($params['captureTxn'])) {
                    throw new \Exception('captureTxn is required and must be false for Auth payment');
                }
                if (filter_var($params['captureTxn'], FILTER_VALIDATE_BOOLEAN)) {
                    throw new \Exception('captureTxn should be false for Auth payment');
                }
                if (isset($params['paymentData']['totalAmount'])) {
                    if (!is_numeric($params['paymentData']['totalAmount']) || $params['paymentData']['totalAmount'] <= 0) {
                        throw new \Exception('Invalid totalAmount. Must be a positive number.');
                    }
                }
            }
        ]);
    }
}
