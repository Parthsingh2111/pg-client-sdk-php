<?php

/**
 * Basic Usage Example for PayGlocal PHP SDK
 * 
 * This example demonstrates how to use the PayGlocal PHP SDK
 * for basic payment operations.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PayGlocal\PgClientSdk\PayGlocalClient;

// Configuration
$config = [
    'merchantId' => 'your_merchant_id',
    'merchantPrivateKey' => '-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC...
-----END PRIVATE KEY-----',
    'payglocalPublicKey' => '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...
-----END PUBLIC KEY-----',
    'privateKeyId' => 'your_private_key_id',
    'publicKeyId' => 'payglocal_public_key_id',
    'environment' => 'sandbox',
    'debug' => true
];

try {
    // Initialize the client
    $client = new PayGlocalClient($config);
    
    echo "PayGlocal PHP SDK initialized successfully!\n";
    
    // Example 1: API Key Payment
    echo "\n=== Example 1: API Key Payment ===\n";
    
    $paymentParams = [
        'merchantTransactionId' => 'TXN_' . time(),
        'amount' => 100.00,
        'currency' => 'INR',
        'merchantId' => $config['merchantId'],
        'callbackUrl' => 'https://your-domain.com/callback',
        'customerEmail' => 'customer@example.com',
        'customerPhone' => '+919876543210',
        'paymentMethod' => 'CARD'
    ];
    
    try {
        $response = $client->initiateApiKeyPayment($paymentParams);
        echo "Payment initiated successfully!\n";
        echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "Payment failed: " . $e->getMessage() . "\n";
    }
    
    // Example 2: JWT Payment
    echo "\n=== Example 2: JWT Payment ===\n";
    
    $jwtPaymentParams = [
        'merchantTransactionId' => 'TXN_JWT_' . time(),
        'amount' => 200.00,
        'currency' => 'INR',
        'merchantId' => $config['merchantId'],
        'callbackUrl' => 'https://your-domain.com/callback'
    ];
    
    try {
        $response = $client->initiateJwtPayment($jwtPaymentParams);
        echo "JWT Payment initiated successfully!\n";
        echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "JWT Payment failed: " . $e->getMessage() . "\n";
    }
    
    // Example 3: Status Check
    echo "\n=== Example 3: Status Check ===\n";
    
    $statusParams = [
        'merchantTransactionId' => 'TXN_' . (time() - 100),
        'merchantId' => $config['merchantId']
    ];
    
    try {
        $response = $client->initiateCheckStatus($statusParams);
        echo "Status check completed!\n";
        echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "Status check failed: " . $e->getMessage() . "\n";
    }
    
    // Example 4: Refund
    echo "\n=== Example 4: Refund ===\n";
    
    $refundParams = [
        'merchantTransactionId' => 'TXN_' . (time() - 200),
        'refundAmount' => 50.00,
        'currency' => 'INR',
        'merchantId' => $config['merchantId'],
        'refundReason' => 'Customer request'
    ];
    
    try {
        $response = $client->initiateRefund($refundParams);
        echo "Refund initiated successfully!\n";
        echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "Refund failed: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "SDK initialization failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Examples completed ===\n";
echo "Check the responses above for API interaction results.\n"; 