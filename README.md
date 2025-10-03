
# PayGlocal Client SDK for PHP

A production-ready, secure, and modular PHP SDK for integrating with the PayGlocal payment gateway. This SDK supports API key payments, JWT-based payments, standing instructions (SI), auth payments, captures, refunds, reversals, status checks, and SI on-demand.

---

## Features

- Enterprise security: JWE (RSA-OAEP-256) and JWS (RS256)
- Complete payment suite: API key, JWT, SI, auth, captures, refunds, reversals, status checks
- Robust validation: schema validation and hierarchical placement warnings
- Structured logging with configurable levels and redaction
- Minimal dependencies; OpenSSL-based crypto (no external crypto libs)

---

## Installation

Option A: Using Composer (recommended)

```bash
composer require payglocal/pg-client-sdk-php
```

Option B: Local clone (monorepo usage)

```bash
git clone https://github.com/Parthsingh2111/pg-client-sdk-php.git
cd pg-client-sdk-php && composer install
```

Then include `vendor/autoload.php` in your project to load the SDK classes.

---

## Configuration

Create a `.env` file and load it before using the SDK:

```env
# PayGlocal configuration
PAYGLOCAL_API_KEY=your_api_key                 # Optional (only for API key auth)
PAYGLOCAL_MERCHANT_ID=your_merchant_id
PAYGLOCAL_PUBLIC_KEY_ID=your_public_key_id
PAYGLOCAL_PRIVATE_KEY_ID=your_private_key_id

# Key file paths (or inline PEM values)
PAYGLOCAL_PUBLIC_KEY=keys/payglocal_public_key
PAYGLOCAL_PRIVATE_KEY=keys/payglocal_private_key

# Environment (UAT or PROD)
PAYGLOCAL_Env_VAR=UAT
PAYGLOCAL_LOG_LEVEL=info                       # error | warn | info | debug
```

### Key files layout

```
project/
├── keys/
│   ├── payglocal_public_key
│   └── payglocal_private_key
├── .env
└── index.php
```

---

## SDK Initialization

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use PayGlocal\PgClientSdk\PayGlocalClient;

function readPemFromEnv(?string $value, string $baseDir): string {
    $val = $value ?? '';
    if ($val === '') return '';
    if (strpos($val, '-----BEGIN') !== false) return $val; // inline PEM
    if (file_exists($val)) return (string)file_get_contents($val); // absolute/relative path
    $rel = rtrim($baseDir, '/').'/'.ltrim($val, '/');
    if (file_exists($rel)) return (string)file_get_contents($rel);
    return '';
}

$payglocalPublicKey = readPemFromEnv($_ENV['PAYGLOCAL_PUBLIC_KEY'] ?? '', __DIR__);
$merchantPrivateKey = readPemFromEnv($_ENV['PAYGLOCAL_PRIVATE_KEY'] ?? '', __DIR__);

$client = new PayGlocalClient([
    'apiKey' => $_ENV['PAYGLOCAL_API_KEY'] ?? '',
    'merchantId' => $_ENV['PAYGLOCAL_MERCHANT_ID'] ?? '',
    'publicKeyId' => $_ENV['PAYGLOCAL_PUBLIC_KEY_ID'] ?? '',
    'privateKeyId' => $_ENV['PAYGLOCAL_PRIVATE_KEY_ID'] ?? '',
    'payglocalPublicKey' => $payglocalPublicKey,
    'merchantPrivateKey' => $merchantPrivateKey,
    'payglocalEnv' => $_ENV['PAYGLOCAL_Env_VAR'] ?? 'UAT',
    'logLevel' => $_ENV['PAYGLOCAL_LOG_LEVEL'] ?? 'info',
]);
```

---

## Quick Start Examples

### Payment operations

```php
// JWT-based payment (recommended)
$client->initiateJwtPayment([
    'merchantTxnId' => 'TXN_' . time(),
    'paymentData' => [
        'totalAmount' => '1000.00',
        'txnCurrency' => 'INR',
        'billingData' => [ 'emailId' => 'customer@example.com' ]
    ],
    'merchantCallbackURL' => 'https://merchant.com/callback',
]);

// API key payment
$client->initiateApiKeyPayment([
    'merchantTxnId' => 'TXN_' . time(),
    'paymentData' => [ 'totalAmount' => '500.00', 'txnCurrency' => 'INR' ],
    'merchantCallbackURL' => 'https://merchant.com/callback',
]);

// Standing instruction (SI) payment
$client->initiateSiPayment([
    'merchantTxnId' => 'SI_' . time(),
    'paymentData' => [ 'totalAmount' => '1000.00', 'txnCurrency' => 'INR' ],
    'standingInstruction' => [
        'data' => [
            'numberOfPayments' => '12', 'frequency' => 'MONTHLY',
            'type' => 'FIXED', 'amount' => '1000.00', 'startDate' => '2025-09-01'
        ]
    ],
    'merchantCallbackURL' => 'https://merchant.com/callback',
]);

// Auth payment
$client->initiateAuthPayment([
    'merchantTxnId' => 'AUTH_' . time(),
    'paymentData' => [ 'totalAmount' => '2000.00', 'txnCurrency' => 'INR' ],
    'captureTxn' => false,
    'merchantCallbackURL' => 'https://merchant.com/callback',
]);
```

### SI on-demand

```php
// Variable amount
$client->initiateSiOnDemandVariable([
    'merchantTxnId' => 'SI_SALE_' . time(),
    'standingInstruction' => [ 'mandateId' => 'md_xxx' ],
    'paymentData' => [ 'totalAmount' => '150.00', 'txnCurrency' => 'INR' ],
]);

// Fixed amount
$client->initiateSiOnDemandFixed([
    'merchantTxnId' => 'SI_SALE_' . time(),
    'standingInstruction' => [ 'mandateId' => 'md_xxx' ],
]);
```

### Transaction management

```php
// Check status
$client->initiateCheckStatus([ 'gid' => 'gl_o-xxxx' ]);

// Capture (full)
$client->initiateCapture([ 'captureType' => 'F', 'gid' => 'gl_o-xxxx', 'merchantTxnId' => 'CAPTURE_' . time() ]);

// Capture (partial)
$client->initiateCapture([ 'captureType' => 'P', 'gid' => 'gl_o-xxxx', 'merchantTxnId' => 'CAPTURE_' . time(), 'paymentData' => [ 'totalAmount' => '250.00' ] ]);

// Refund (full)
$client->initiateRefund([ 'refundType' => 'F', 'gid' => 'gl_o-xxxx', 'merchantTxnId' => 'REFUND_' . time(), 'paymentData' => [ 'totalAmount' => 0 ] ]);

// Refund (partial)
$client->initiateRefund([ 'refundType' => 'P', 'gid' => 'gl_o-xxxx', 'merchantTxnId' => 'REFUND_' . time(), 'paymentData' => [ 'totalAmount' => '250.00' ] ]);

// Auth reversal
$client->initiateAuthReversal([ 'gid' => 'gl_o-xxxx', 'merchantTxnId' => 'REVERSAL_' . time() ]);
```

### Standing instruction management

```php
// Pause SI
$client->initiatePauseSI([
    'merchantTxnId' => 'PAUSE_SI_' . time(),
    'standingInstruction' => [ 'action' => 'PAUSE', 'mandateId' => 'md_xxx' ],
]);

// Activate SI
$client->initiateActivateSI([
    'merchantTxnId' => 'ACTIVATE_SI_' . time(),
    'standingInstruction' => [ 'action' => 'ACTIVATE', 'mandateId' => 'md_xxx' ],
]);

// SI status check
$client->initiateSiStatusCheck([
    'standingInstruction' => [ 'mandateId' => 'md_xxx' ],
]);
```

---

## PHP Backend Integration

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use PayGlocal\PgClientSdk\PayGlocalClient;

// load .env then init keys (see SDK Initialization)
$client = new PayGlocalClient([ /* ...config from env... */ ]);

// Minimal JWT route example (pseudo)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/api/pay/jwt') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $payload = [
        'merchantTxnId' => $input['merchantTxnId'] ?? '',
        'paymentData' => $input['paymentData'] ?? [],
        'merchantCallbackURL' => $input['merchantCallbackURL'] ?? '',
    ];
    $payment = $client->initiateJwtPayment($payload);
    echo json_encode($payment);
    exit;
}
```

---

## Logging and Monitoring

Set the log level via config: `error`, `warn`, `info`, or `debug`.

```php
$client = new PayGlocalClient([
    // ...
    'logLevel' => 'debug',
]);
```

---

## Error Handling

```php
try {
    $res = $client->initiateJwtPayment($payload);
} catch (\Throwable $e) {
    error_log('Payment failed: ' . $e->getMessage());
}
```

Common scenarios: missing required fields, invalid configuration, network/API errors. Ensure `merchantTxnId`, `paymentData.totalAmount`, `paymentData.txnCurrency`, and `merchantCallbackURL` where applicable.

---

## Configuration Options

```php
$client = new PayGlocalClient([
    // Required for token-based auth
    'merchantId' => 'your_merchant_id',
    'publicKeyId' => 'your_public_key_id',
    'privateKeyId' => 'your_private_key_id',
    'payglocalPublicKey' => '-----BEGIN PUBLIC KEY-----...-----END PUBLIC KEY-----',
    'merchantPrivateKey' => '-----BEGIN PRIVATE KEY-----...-----END PRIVATE KEY-----',

    // Optional
    'apiKey' => 'your_api_key',                // API key auth
    'payglocalEnv' => 'UAT',                   // UAT or PROD
    'logLevel' => 'info',
    'tokenExpiration' => 300000,
]);
```

---

## API Reference

### Payment methods

| Method | Description | Parameters | Returns |
|--------|-------------|------------|---------|
| `initiateJwtPayment(params)` | JWT-based payment with encryption | `{merchantTxnId, paymentData, merchantCallbackURL}` | `{paymentLink, gid}` |
| `initiateApiKeyPayment(params)` | API key-based payment | `{merchantTxnId, paymentData, merchantCallbackURL}` | `{paymentLink, statusLink}` |
| `initiateSiPayment(params)` | Standing instruction payment | `{merchantTxnId, paymentData, standingInstruction, merchantCallbackURL}` | `{paymentLink, gid}` |
| `initiateAuthPayment(params)` | Auth payment | `{merchantTxnId, paymentData, captureTxn, merchantCallbackURL}` | `{paymentLink, gid}` |

### Transaction management

| Method | Description | Parameters | Returns |
|--------|-------------|------------|---------|
| `initiateCheckStatus(params)` | Check payment status | `{gid}` | `{status, gid, message}` |
| `initiateCapture(params)` | Capture payment | `{gid, merchantTxnId, captureType, paymentData?}` | `{status, gid, captureId}` |
| `initiateRefund(params)` | Refund payment | `{gid, merchantTxnId, refundType, paymentData?}` | `{status, gid, refundId}` |
| `initiateAuthReversal(params)` | Reverse auth | `{gid, merchantTxnId}` | `{status, gid, reversalId}` |

### Standing instruction management

| Method | Description | Parameters | Returns |
|--------|-------------|------------|---------|
| `initiatePauseSI(params)` | Pause SI | `{merchantTxnId, standingInstruction}` | `{status, mandateId}` |
| `initiateActivateSI(params)` | Activate SI | `{merchantTxnId, standingInstruction}` | `{status, mandateId}` |

### SI On-Demand

| Method | Description | Parameters | Returns |
|--------|-------------|------------|---------|
| `initiateSiOnDemandVariable(params)` | SI sale with variable amount | `{merchantTxnId, standingInstruction.mandateId, paymentData.totalAmount, paymentData.txnCurrency}` | `{status, gid}` |
| `initiateSiOnDemandFixed(params)` | SI sale with fixed amount (from mandate) | `{merchantTxnId, standingInstruction.mandateId}` | `{status, gid}` |

---

## Method Call Examples (PHP)

```php
// JWT Payment
$client->initiateJwtPayment([
    'merchantTxnId' => 'TXN_' . time(),
    'paymentData' => [ 'totalAmount' => '1000.00', 'txnCurrency' => 'INR' ],
    'merchantCallbackURL' => 'https://merchant.com/callback',
]);

// API Key Payment
$client->initiateApiKeyPayment([
    'merchantTxnId' => 'TXN_' . time(),
    'paymentData' => [ 'totalAmount' => '500.00', 'txnCurrency' => 'INR' ],
    'merchantCallbackURL' => 'https://merchant.com/callback',
]);

// SI Payment
$client->initiateSiPayment([
    'merchantTxnId' => 'SI_' . time(),
    'paymentData' => [ 'totalAmount' => '1000.00', 'txnCurrency' => 'INR' ],
    'standingInstruction' => [ 'data' => [ 'numberOfPayments' => '12', 'frequency' => 'MONTHLY', 'type' => 'FIXED', 'amount' => '1000.00', 'startDate' => '2025-09-01' ] ],
    'merchantCallbackURL' => 'https://merchant.com/callback',
]);

// Auth Payment
$client->initiateAuthPayment([
    'merchantTxnId' => 'AUTH_' . time(),
    'paymentData' => [ 'totalAmount' => '2000.00', 'txnCurrency' => 'INR' ],
    'captureTxn' => false,
    'merchantCallbackURL' => 'https://merchant.com/callback',
]);

// Check Status
$client->initiateCheckStatus([ 'gid' => 'gl_o-xxxx' ]);

// Capture (Full)
$client->initiateCapture([ 'captureType' => 'F', 'gid' => 'gl_o-xxxx', 'merchantTxnId' => 'CAPTURE_' . time() ]);

// Capture (Partial)
$client->initiateCapture([ 'captureType' => 'P', 'gid' => 'gl_o-xxxx', 'merchantTxnId' => 'CAPTURE_' . time(), 'paymentData' => [ 'totalAmount' => '250.00' ] ]);

// Refund (Full)
$client->initiateRefund([ 'refundType' => 'F', 'gid' => 'gl_o-xxxx', 'merchantTxnId' => 'REFUND_' . time(), 'paymentData' => [ 'totalAmount' => 0 ] ]);

// Refund (Partial)
$client->initiateRefund([ 'refundType' => 'P', 'gid' => 'gl_o-xxxx', 'merchantTxnId' => 'REFUND_' . time(), 'paymentData' => [ 'totalAmount' => '250.00' ] ]);

// Auth Reversal
$client->initiateAuthReversal([ 'gid' => 'gl_o-xxxx', 'merchantTxnId' => 'REVERSAL_' . time() ]);

// SI Pause
$client->initiatePauseSI([ 'merchantTxnId' => 'PAUSE_SI_' . time(), 'standingInstruction' => [ 'action' => 'PAUSE', 'mandateId' => 'md_xxx' ] ]);

// SI Activate
$client->initiateActivateSI([ 'merchantTxnId' => 'ACTIVATE_SI_' . time(), 'standingInstruction' => [ 'action' => 'ACTIVATE', 'mandateId' => 'md_xxx' ] ]);

// SI On-Demand Variable
$client->initiateSiOnDemandVariable([ 'merchantTxnId' => 'SI_SALE_' . time(), 'standingInstruction' => [ 'mandateId' => 'md_xxx' ], 'paymentData' => [ 'totalAmount' => '150.00', 'txnCurrency' => 'INR' ] ]);

// SI On-Demand Fixed
$client->initiateSiOnDemandFixed([ 'merchantTxnId' => 'SI_SALE_' . time(), 'standingInstruction' => [ 'mandateId' => 'md_xxx' ] ]);
```

---

## Troubleshooting

- Verify environment variables and key paths (or inline PEMs)
- Use `logLevel=debug` during development
- Ensure monetary values are strings (e.g., '1000.00') where required

---

## Security Considerations

- Never commit private keys to source control
- Use HTTPS and secure storage for secrets in production

---

## Support

- Email: singhparth2111@gmail.com
- Issues: open on the repository

---

## License

MIT License
