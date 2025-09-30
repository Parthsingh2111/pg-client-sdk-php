<?php

namespace PayGlocal\PgClientSdk;

use PayGlocal\PgClientSdk\Core\Config;
use PayGlocal\PgClientSdk\Services\PaymentService;
use PayGlocal\PgClientSdk\Services\RefundService;
use PayGlocal\PgClientSdk\Services\CaptureService;
use PayGlocal\PgClientSdk\Services\ReversalService;
use PayGlocal\PgClientSdk\Services\StatusService;
use PayGlocal\PgClientSdk\Services\SiUpdateService;
use PayGlocal\PgClientSdk\Services\SiOnDemandService;
use PayGlocal\PgClientSdk\Utils\Logger;

/**
 * PayGlocalClient for interacting with PayGlocal API.
 * Matches JavaScript PayGlocalClient behavior exactly
 */
class PayGlocalClient
{
    private Config $config;

    /**
     * Constructor
     * @param array $config Configuration options
     * @throws \Exception
     */
    public function __construct(array $config = [])
    {
        $this->config = new Config($config);
        
        // Set log level (matches JavaScript behavior)
        Logger::setLevel($this->config->logLevel ?? 'info');
        
        // Log configuration (matches JavaScript behavior)
        Logger::logConfig($this->config);
        Logger::info('PayGlocalClient initialized successfully');
    }

    /**
     * Initiate API Key-based payment
     * @param array $params Payment parameters
     * @return array Payment response
     * @throws \Exception
     */
    public function initiateApiKeyPayment(array $params): array
    {
        $service = new PaymentService($this->config);
        return $service->initiateApiKeyPayment($params);
    }

    /**
     * Initiate JWT-based payment
     * @param array $params Payment parameters
     * @return array Payment response
     * @throws \Exception
     */
    public function initiateJwtPayment(array $params): array
    {
        $service = new PaymentService($this->config);
        return $service->initiateJwtPayment($params);
    }

    /**
     * Initiate Standing Instruction (SI) payment
     * @param array $params Payment parameters
     * @return array Payment response
     * @throws \Exception
     */
    public function initiateSiPayment(array $params): array
    {
        $service = new PaymentService($this->config);
        return $service->initiateSiPayment($params);
    }

    /**
     * Initiate Auth payment
     * @param array $params Payment parameters
     * @return array Payment response
     * @throws \Exception
     */
    public function initiateAuthPayment(array $params): array
    {
        $service = new PaymentService($this->config);
        return $service->initiateAuthPayment($params);
    }

    /**
     * Initiate refund
     * @param array $params Refund parameters
     * @return array Refund response
     * @throws \Exception
     */
    public function initiateRefund(array $params): array
    {
        $service = new RefundService($this->config);
        return $service->initiateRefund($params);
    }

    /**
     * Initiate capture
     * @param array $params Capture parameters
     * @return array Capture response
     * @throws \Exception
     */
    public function initiateCapture(array $params): array
    {
        $service = new CaptureService($this->config);
        return $service->initiateCapture($params);
    }

    /**
     * Initiate auth reversal
     * @param array $params Reversal parameters
     * @return array Reversal response
     * @throws \Exception
     */
    public function initiateAuthReversal(array $params): array
    {
        $service = new ReversalService($this->config);
        return $service->initiateAuthReversal($params);
    }

    /**
     * Initiate check status
     * @param array $params Status parameters
     * @return array Status response
     * @throws \Exception
     */
    public function initiateCheckStatus(array $params): array
    {
        $service = new StatusService($this->config);
        return $service->initiateCheckStatus($params);
    }

    /**
     * Initiate pause SI
     * @param array $params Pause parameters
     * @return array Pause response
     * @throws \Exception
     */
    public function initiatePauseSI(array $params): array
    {
        $service = new SiUpdateService($this->config);
        return $service->initiatePauseSI($params);
    }

    /**
     * Initiate activate SI
     * @param array $params Activate parameters
     * @return array Activate response
     * @throws \Exception
     */
    public function initiateActivateSI(array $params): array
    {
        $service = new SiUpdateService($this->config);
        return $service->initiateActivateSI($params);
    }

    /**
     * Initiate SI On-Demand sale (variable amount)
     * @param array $params expects standingInstruction.mandateId, paymentData.totalAmount, merchantTxnId
     * @return array Response
     * @throws \Exception
     */
    public function initiateSiOnDemandVariable(array $params): array
    {
        $service = new SiOnDemandService($this->config);
        return $service->initiateSiOnDemandVariable($params);
    }

    /**
     * Initiate SI On-Demand sale (fixed amount)
     * @param array $params expects standingInstruction.mandateId, merchantTxnId
     * @return array Response
     * @throws \Exception
     */
    public function initiateSiOnDemandFixed(array $params): array
    {
        $service = new SiOnDemandService($this->config);
        return $service->initiateSiOnDemandFixed($params);
    }

    /**
     * Get configuration object
     * @return Config Configuration object
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
} 