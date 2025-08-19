# PayGlocal Client SDK for PHP

Official PHP SDK for PayGlocal payment gateway integration. This SDK provides a secure, lightweight, and easy-to-use interface for integrating PayGlocal payment services into PHP applications.

**🚀 Now 100% identical to the Node.js SDK in structure, logic, and functionality!**

## 🚀 Features

- **Multiple Authentication Methods**: Support for both API Key and JWT-based authentication
- **Comprehensive Payment Services**: JWT payments, API Key payments, Standing Instructions (SI), and Auth payments
- **Transaction Management**: Status checks, captures, refunds, and reversals
- **Standing Instructions**: Pause and activate SI operations
- **Security**: JWE/JWS encryption for sensitive data
- **Validation**: Built-in payload validation and schema checking
- **Logging**: Comprehensive logging with configurable levels
- **Error Handling**: Simplified error handling matching Node.js SDK
- **Performance**: Native PHP extensions for optimal performance

## 📋 Requirements

- PHP 8.0 or higher
- cURL extension
- OpenSSL extension
- JSON extension

## 🔧 Installation

### Using Composer (Recommended)

```bash
composer require payglocal/pg-client-sdk-php
```

### Manual Installation

1. Clone or download this repository
2. Run `composer install` in the project directory
3. Include the autoloader in your project

## ⚙️ Configuration

### Environment Variables

Create a `.env` file with your PayGlocal credentials:

```env
# Server Configuration
PORT=3001

# PayGlocal Configuration
PAYGLOCAL_API_KEY=your_api_key_here
PAYGLOCAL_MERCHANT_ID=your_merchant_id_here
PAYGLOCAL_PUBLIC_KEY_ID=your_public_key_id_here
PAYGLOCAL_PRIVATE_KEY_ID=your_private_key_id_here

# Key File Paths (relative to your project directory)
PAYGLOCAL_PUBLIC_KEY=keys/payglocal_public_key
PAYGLOCAL_PRIVATE_KEY=keys/payglocal_private_key

# Environment (UAT or PROD)
PAYGLOCAL_Env_VAR=UAT

# Log Level (error, warn, info, debug)
PAYGLOCAL_LOG_LEVEL=debug
```

### Key Files Setup

1. Create a `keys/` directory in your project
2. Place your PayGlocal public key in `keys/payglocal_public_key`
3. Place your merchant private key in `keys/payglocal_private_key`

## 🚀 Quick Start

### SDK Initialization

```php
<?php

require_once 'vendor/autoload.php';

use PayGlocal\PgClientSdk\PayGlocalClient;

// Read and normalize PEM key content
$payglocalPublicKey = file_get_contents('keys/payglocal_public_key');
$merchantPrivateKey = file_get_contents('keys/payglocal_private_key');

// Initialize the client
$client = new PayGlocalClient([
    'apiKey' => $_ENV['PAYGLOCAL_API_KEY'],
    'merchantId' => $_ENV['PAYGLOCAL_MERCHANT_ID'],
    'publicKeyId' => $_ENV['PAYGLOCAL_PUBLIC_KEY_ID'],
    'privateKeyId' => $_ENV['PAYGLOCAL_PRIVATE_KEY_ID'],
    'payglocalPublicKey' => $payglocalPublicKey,
    'merchantPrivateKey' => $merchantPrivateKey,
    'payglocalEnv' => $_ENV['PAYGLOCAL_Env_VAR'],
    'logLevel' => $_ENV['PAYGLOCAL_LOG_LEVEL'] ?? 'debug'
]);
```

### Payment Examples

#### JWT Payment

```php
try {
    $response = $client->initiateJwtPayment([
        'merchantTxnId' => 'TXN_' . time(),
        'paymentData' => [
            'totalAmount' => 100.00,
            'txnCurrency' => 'INR'
        ],
        'merchantCallbackURL' => 'https://your-domain.com/callback'
    ]);
    
    echo "Payment initiated: " . json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Payment failed: " . $e->getMessage();
}
```

#### API Key Payment

```php
try {
    $response = $client->initiateApiKeyPayment([
        'merchantTxnId' => 'TXN_' . time(),
        'paymentData' => [
            'totalAmount' => 100.00,
            'txnCurrency' => 'INR'
        ],
        'merchantCallbackURL' => 'https://your-domain.com/callback'
    ]);
    
    echo "Payment initiated: " . json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Payment failed: " . $e->getMessage();
}
```

#### Standing Instruction Payment

```php
try {
    $response = $client->initiateSiPayment([
        'merchantTxnId' => 'SI_TXN_' . time(),
        'paymentData' => [
            'totalAmount' => 100.00,
            'txnCurrency' => 'INR'
        ],
        'merchantCallbackURL' => 'https://your-domain.com/callback',
        'standingInstruction' => [
            'action' => 'PAUSE',
            'data' => [
                'startDate' => '20250101',
                'endDate' => '20251231',
                'frequency' => 'MONTHLY',
                'maxAmount' => 1000.00,
                'maxCount' => 12
            ]
        ]
    ]);
    
    echo "SI Payment initiated: " . json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "SI Payment failed: " . $e->getMessage();
}
```

### Transaction Management

#### Check Status

```php
try {
    $response = $client->initiateCheckStatus([
        'gid' => 'your_transaction_gid'
    ]);
    
    echo "Status: " . json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Status check failed: " . $e->getMessage();
}
```

#### Refund

```php
try {
    $response = $client->initiateRefund([
        'gid' => 'your_transaction_gid',
        'merchantTxnId' => 'your_merchant_txn_id',
        'refundType' => 'P', // P for partial, F for full
        'paymentData' => [
            'totalAmount' => 50.00
        ]
    ]);
    
    echo "Refund initiated: " . json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Refund failed: " . $e->getMessage();
}
```

#### Capture

```php
try {
    $response = $client->initiateCapture([
        'gid' => 'your_transaction_gid',
        'merchantTxnId' => 'your_merchant_txn_id',
        'captureType' => 'P', // P for partial, F for full
        'paymentData' => [
            'totalAmount' => 100.00
        ]
    ]);
    
    echo "Capture initiated: " . json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Capture failed: " . $e->getMessage();
}
```

### Standing Instructions

#### Pause SI

```php
try {
    $response = $client->initiatePauseSI([
        'merchantTxnId' => 'SI_PAUSE_' . time(),
        'standingInstruction' => [
            'action' => 'PAUSE',
            'data' => [
                'startDate' => '20250101'
            ]
        ]
    ]);
    
    echo "SI Pause initiated: " . json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "SI Pause failed: " . $e->getMessage();
}
```

#### Activate SI

```php
try {
    $response = $client->initiateActivateSI([
        'merchantTxnId' => 'SI_ACTIVATE_' . time(),
        'standingInstruction' => [
            'action' => 'ACTIVATE'
        ]
    ]);
    
    echo "SI Activate initiated: " . json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "SI Activate failed: " . $e->getMessage();
}
```

## 🏗️ Architecture

The SDK follows a modular architecture with clear separation of concerns, **identical to the Node.js SDK**:

```
src/
├── Core/           # Config, Crypto, HttpClient
├── Helper/         # Token, Header, API Request, Validation
├── Services/       # Payment, SiUpdate, Refund, Capture, Reversal, Status
├── Constants/      # Endpoints
├── Utils/          # Logger, Validators, SchemaValidator
└── PayGlocalClient.php  # Main client class
```

## 🔐 Security Features

- **JWE Encryption**: Payload encryption using RSA-OAEP-256
- **JWS Signing**: Request signing using RS256
- **Key Management**: Secure handling of public/private keys
- **Token Expiration**: Configurable token expiration (default: 5 minutes)
- **Header Masking**: Sensitive headers are masked in logs

## 📝 Logging

The SDK provides comprehensive logging with configurable levels:

```php
// Set log level
Logger::setLevel('debug'); // Options: error, warn, info, debug

// Log levels are automatically handled based on configuration
```

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Generate coverage report:

```bash
composer test-coverage
```

Run the comprehensive test file:

```bash
php test-sdk.php
```

## 📚 API Reference

### PayGlocalClient Methods

- `initiateApiKeyPayment(array $params)`: API Key-based payment
- `initiateJwtPayment(array $params)`: JWT-based payment
- `initiateSiPayment(array $params)`: Standing Instruction payment
- `initiateAuthPayment(array $params)`: Auth payment
- `initiateRefund(array $params)`: Refund transaction
- `initiateCapture(array $params)`: Capture transaction
- `initiateAuthReversal(array $params)`: Auth reversal
- `initiateCheckStatus(array $params)`: Check transaction status
- `initiatePauseSI(array $params)`: Pause Standing Instruction
- `initiateActivateSI(array $params)`: Activate Standing Instruction

## 🐛 Troubleshooting

### Common Issues

1. **Invalid environment**: Ensure `payglocalEnv` is set to either "UAT" or "PROD"
2. **Missing keys**: Verify that all required key files are present and readable
3. **Invalid PEM format**: Ensure keys are in proper PEM format
4. **Network issues**: Check firewall settings and network connectivity

### Debug Mode

Enable debug logging to troubleshoot issues:

```php
$client = new PayGlocalClient([
    // ... other config
    'logLevel' => 'debug'
]);
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Support

For support and questions:

- **Email**: support@payglocal.in
- **Documentation**: [PayGlocal Developer Portal](https://docs.payglocal.in)
- **Issues**: [GitHub Issues](https://github.com/payglocal/pg-client-sdk-php/issues)

## 🔄 Changelog

### v2.0.0 - **100% Node.js SDK Parity**
- **🎯 Complete Restructuring**: PHP SDK now matches Node.js SDK exactly
- **🔧 Consolidated SI Services**: Single `SiUpdateService` for both pause and activate
- **📝 Updated All Services**: Refund, Capture, Reversal, Status services match Node.js exactly
- **🚀 Static Method Usage**: Helper and utility classes use static methods like Node.js
- **✅ Simplified Error Handling**: Direct exceptions instead of complex error handler
- **📊 Enhanced Schema Validation**: Complete schema matching Node.js implementation
- **🧹 Removed Dependencies**: No external JWT libraries, uses native OpenSSL
- **📈 Performance Improvements**: Native PHP extensions for better performance
- **🔍 Comprehensive Testing**: Full test suite covering all functionality

### v1.0.0
- Initial release with basic payment functionality
- JWT and API Key authentication support 