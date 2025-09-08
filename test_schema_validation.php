<?php

require_once __DIR__ . '/vendor/autoload.php';

use PayGlocal\PgClientSdk\Utils\SchemaValidator;

// Test payload with valid structure
$validPayload = [
    'merchantTxnId' => 'TXN123',
    'merchantCallbackURL' => 'https://example.com/callback',
    'paymentData' => [
        'totalAmount' => '100.00',
        'txnCurrency' => 'INR',
        'cardData' => [
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'securityCode' => '123',
            'type' => 'VISA'
        ]
    ]
];

// Test payload with invalid dateOfBirth pattern
$invalidPayload = [
    'merchantTxnId' => 'TXN123',
    'merchantCallbackURL' => 'https://example.com/callback',
    'paymentData' => [
        'totalAmount' => '100.00',
        'txnCurrency' => 'INR'
    ],
    'riskData' => [
        'flightData' => [
            [
                'legData' => [
                    [
                        'routeId' => '123',
                        'legId' => '456',
                        'flightNumber' => 'AI101',
                        'departureDate' => '20231201',
                        'departureAirportCode' => 'DEL',
                        'departureCity' => 'Delhi',
                        'departureCountry' => 'India',
                        'arrivalDate' => '20231201',
                        'arrivalAirportCode' => 'BOM',
                        'arrivalCity' => 'Mumbai',
                        'arrivalCountry' => 'India',
                        'carrierCode' => 'AI',
                        'carrierName' => 'Air India',
                        'serviceClass' => 'Economy'
                    ]
                ],
                'passengerData' => [
                    [
                        'firstName' => 'John',
                        'lastName' => 'Doe',
                        'dateOfBirth' => '19900101', // Valid pattern
                        'type' => 'Adult',
                        'email' => 'john@example.com',
                        'passportNumber' => 'A1234567',
                        'passportCountry' => 'US',
                        'passportIssueDate' => '20100101', // Valid maxLength 8
                        'passportExpiryDate' => '20300101', // Valid maxLength 8
                        'referenceNumber' => 'REF123'
                    ]
                ]
            ]
        ]
    ]
];

// Test payload with invalid dateOfBirth pattern
$invalidDatePayload = [
    'merchantTxnId' => 'TXN123',
    'merchantCallbackURL' => 'https://example.com/callback',
    'paymentData' => [
        'totalAmount' => '100.00',
        'txnCurrency' => 'INR'
    ],
    'riskData' => [
        'flightData' => [
            [
                'passengerData' => [
                    [
                        'firstName' => 'John',
                        'lastName' => 'Doe',
                        'dateOfBirth' => 'invalid_date', // Invalid pattern
                        'type' => 'Adult',
                        'email' => 'john@example.com'
                    ]
                ]
            ]
        ]
    ]
];

$schemaValidator = new SchemaValidator();

echo "Testing valid payload...\n";
try {
    $result = $schemaValidator->validatePaycollectPayload($validPayload);
    echo "✓ Valid payload passed validation\n";
    echo "Hierarchical warnings: " . $result['warningCount'] . "\n";
} catch (Exception $e) {
    echo "✗ Valid payload failed: " . $e->getMessage() . "\n";
}

echo "\nTesting invalid dateOfBirth pattern...\n";
try {
    $result = $schemaValidator->validatePaycollectPayload($invalidDatePayload);
    echo "✗ Invalid payload should have failed\n";
} catch (Exception $e) {
    echo "✓ Invalid payload correctly failed: " . $e->getMessage() . "\n";
}

echo "\nTesting hierarchical validation...\n";
try {
    $result = $schemaValidator->validatePaycollectPayload($invalidPayload);
    echo "✓ Payload with hierarchical data passed validation\n";
    echo "Hierarchical warnings: " . $result['warningCount'] . "\n";
    if ($result['warningCount'] > 0) {
        echo "Warnings:\n";
        foreach ($result['hierarchicalWarnings'] as $warning) {
            echo "  - " . $warning['message'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Payload failed: " . $e->getMessage() . "\n";
}

echo "\nTest completed!\n";
