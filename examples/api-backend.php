<?php

/**
 * PayGlocal PHP Backend API Example
 * 
 * This example demonstrates how to create a PHP backend API
 * that can be consumed by a Flutter frontend application.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PayGlocal\PgClientSdk\PayGlocalClient;

// Enable CORS for Flutter app
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuration
$config = [
    'merchantId' => $_ENV['PAYGLOCAL_MERCHANT_ID'] ?? 'your_merchant_id',
    'merchantPrivateKey' => $_ENV['PAYGLOCAL_MERCHANT_PRIVATE_KEY'] ?? 'your_private_key',
    'payglocalPublicKey' => $_ENV['PAYGLOCAL_PUBLIC_KEY'] ?? 'payglocal_public_key',
    'privateKeyId' => $_ENV['PAYGLOCAL_PRIVATE_KEY_ID'] ?? 'your_private_key_id',
    'publicKeyId' => $_ENV['PAYGLOCAL_PUBLIC_KEY_ID'] ?? 'payglocal_public_key_id',
    'environment' => $_ENV['PAYGLOCAL_ENVIRONMENT'] ?? 'sandbox',
    'debug' => $_ENV['PAYGLOCAL_DEBUG'] ?? false
];

// Initialize PayGlocal client
try {
    $client = new PayGlocalClient($config);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'SDK initialization failed: ' . $e->getMessage()]);
    exit();
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');

// Get request body
$input = json_decode(file_get_contents('php://input'), true);

// Route the request
try {
    switch ($method) {
        case 'POST':
            switch ($path) {
                case 'api/payment/initiate':
                    handlePaymentInitiate($client, $input);
                    break;
                    
                case 'api/payment/jwt':
                    handleJwtPayment($client, $input);
                    break;
                    
                case 'api/payment/si':
                    handleSiPayment($client, $input);
                    break;
                    
                case 'api/refund':
                    handleRefund($client, $input);
                    break;
                    
                case 'api/capture':
                    handleCapture($client, $input);
                    break;
                    
                case 'api/reversal':
                    handleReversal($client, $input);
                    break;
                    
                case 'api/si/pause':
                    handleSiPause($client, $input);
                    break;
                    
                case 'api/si/activate':
                    handleSiActivate($client, $input);
                    break;
                    
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'Endpoint not found']);
                    break;
            }
            break;
            
        case 'GET':
            switch ($path) {
                case 'api/status':
                    handleStatusCheck($client, $_GET);
                    break;
                    
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'Endpoint not found']);
                    break;
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Handle payment initiation
 */
function handlePaymentInitiate($client, $input) {
    try {
        $response = $client->initiateApiKeyPayment($input);
        echo json_encode([
            'success' => true,
            'data' => $response
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Handle JWT payment
 */
function handleJwtPayment($client, $input) {
    try {
        $response = $client->initiateJwtPayment($input);
        echo json_encode([
            'success' => true,
            'data' => $response
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Handle SI payment
 */
function handleSiPayment($client, $input) {
    try {
        $response = $client->initiateSiPayment($input);
        echo json_encode([
            'success' => true,
            'data' => $response
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Handle refund
 */
function handleRefund($client, $input) {
    try {
        $response = $client->initiateRefund($input);
        echo json_encode([
            'success' => true,
            'data' => $response
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Handle capture
 */
function handleCapture($client, $input) {
    try {
        $response = $client->initiateCapture($input);
        echo json_encode([
            'success' => true,
            'data' => $response
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Handle reversal
 */
function handleReversal($client, $input) {
    try {
        $response = $client->initiateAuthReversal($input);
        echo json_encode([
            'success' => true,
            'data' => $response
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Handle status check
 */
function handleStatusCheck($client, $params) {
    try {
        $response = $client->initiateCheckStatus($params);
        echo json_encode([
            'success' => true,
            'data' => $response
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Handle SI pause
 */
function handleSiPause($client, $input) {
    try {
        $response = $client->initiatePauseSI($input);
        echo json_encode([
            'success' => true,
            'data' => $response
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Handle SI activate
 */
function handleSiActivate($client, $input) {
    try {
        $response = $client->initiateActivateSI($input);
        echo json_encode([
            'success' => true,
            'data' => $response
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} 