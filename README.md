# PayGlocal Client SDK for PHP

Official PHP SDK for PayGlocal payment gateway integration. This SDK provides a secure, lightweight interface for integrating PayGlocal services into PHP applications.

## Features

- Payments: JWT, API Key, Standing Instructions (SI)
- Transaction ops: Status, Capture, Refund, Reversal
- Schema validation (JSON Schema) + hierarchical structure warnings
- JWE/JWS with OpenSSL (no external crypto deps)
- Structured logging with redaction and env-driven levels

## Requirements

- PHP 8.0+
- Extensions: curl, openssl, json

## Installation

### Composer (recommended)
```bash
composer require payglocal/pg-client-sdk-php
```

### Manual
1) Copy `src/` into your project
2) Use PSR-4 autoloading (see composer.json) or `require` as needed

## Configuration

Create `.env` (or provide via environment) and load before using the SDK.

```env
# Server (optional for local testing)
PORT=3001

# PayGlocal configuration
PAYGLOCAL_API_KEY=your_api_key
PAYGLOCAL_MERCHANT_ID=your_merchant_id
PAYGLOCAL_PUBLIC_KEY_ID=your_public_key_id
PAYGLOCAL_PRIVATE_KEY_ID=your_private_key_id

# Key file paths
PAYGLOCAL_PUBLIC_KEY=keys/payglocal_public_key
PAYGLOCAL_PRIVATE_KEY=keys/payglocal_private_key

# Environment (UAT or PROD)
PAYGLOCAL_Env_VAR=UAT

# Log level: error | warn | info | debug
PAYGLOCAL_LOG_LEVEL=info
```

Place your PEM keys under `keys/` as configured above.

## Quick Start

```php
<?php
require_once 'vendor/autoload.php';

use PayGlocal\PgClientSdk\PayGlocalClient;

$client = new PayGlocalClient([
    'apiKey' => $_ENV['PAYGLOCAL_API_KEY'],
    'merchantId' => $_ENV['PAYGLOCAL_MERCHANT_ID'],
    'publicKeyId' => $_ENV['PAYGLOCAL_PUBLIC_KEY_ID'],
    'privateKeyId' => $_ENV['PAYGLOCAL_PRIVATE_KEY_ID'],
    'payglocalPublicKey' => file_get_contents($_ENV['PAYGLOCAL_PUBLIC_KEY']),
    'merchantPrivateKey' => file_get_contents($_ENV['PAYGLOCAL_PRIVATE_KEY']),
    'payglocalEnv' => $_ENV['PAYGLOCAL_Env_VAR'],
    'logLevel' => $_ENV['PAYGLOCAL_LOG_LEVEL'] ?? 'info',
]);
```

### JWT Payment
```php
$response = $client->initiateJwtPayment([
    'merchantTxnId' => 'TXN_' . time(),
    'paymentData' => [
        'totalAmount' => '100.00',
        'txnCurrency' => 'INR',
    ],
    'merchantCallbackURL' => 'https://your-domain.com/callback',
]);
```

### API Key Payment
```php
$response = $client->initiateApiKeyPayment([
    'merchantTxnId' => 'TXN_' . time(),
    'paymentData' => [
        'totalAmount' => '100.00',
        'txnCurrency' => 'INR',
    ],
    'merchantCallbackURL' => 'https://your-domain.com/callback',
]);
```

### Standing Instruction (SI) Payment
```php
$response = $client->initiateSiPayment([
    'merchantTxnId' => 'SI_' . time(),
    'paymentData' => [
        'totalAmount' => '100.00',
        'txnCurrency' => 'INR',
    ],
    'merchantCallbackURL' => 'https://your-domain.com/callback',
    'standingInstruction' => [
        'data' => [
            'amount' => '100.00',
            'numberOfPayments' => '12',
            'frequency' => 'MONTHLY',
            'type' => 'FIXED',
            'startDate' => '20250101',
        ],
    ],
]);
```

### Status / Refund / Capture (examples)
```php
$client->initiateCheckStatus(['gid' => 'your_gid']);
$client->initiateRefund(['gid' => 'your_gid', 'merchantTxnId' => 'your_txn', 'paymentData' => ['totalAmount' => '50.00']]);
$client->initiateCapture(['gid' => 'your_gid', 'merchantTxnId' => 'your_txn', 'paymentData' => ['totalAmount' => '100.00']]);
```

### SI Pause / Activate
```php
$client->initiatePauseSI(['merchantTxnId' => 'SI_PAUSE_' . time(), 'standingInstruction' => ['action' => 'PAUSE']]);
$client->initiateActivateSI(['merchantTxnId' => 'SI_ACTIVATE_' . time(), 'standingInstruction' => ['action' => 'ACTIVATE']]);
```

## Validation

- JSON Schema validation is applied to requests (string types for monetary values and IDs where applicable).
- Hierarchical validation runs after schema validation and logs warnings only for misplaced containers (objects/arrays). It does not block requests. Example message:
  - Object "trainData" at path "trainData" might be misplaced

Notes:
- Primitive fields (e.g., `totalAmount`, `txnCurrency`) are not part of hierarchical warnings.
- Warnings are logged at `warn` level.

## Logging

- Configure via `PAYGLOCAL_LOG_LEVEL` or the `logLevel` option: `error | warn | info | debug`.
- Sensitive fields are redacted where applicable.

## Error Handling

- Validation errors throw an `Exception` with a JSON-encoded body containing a consistent error shape:
```json
{
  "gid": null,
  "status": "REQUEST_ERROR",
  "message": "Invalid request fields",
  "timestamp": "...",
  "reasonCode": "LOCAL-400-001",
  "data": null,
  "errors": { "field.path": "message" }
}
```

## Local Testing (built-in server)

```bash
php -S localhost:3001 index.php
```
Then send HTTP requests to your routes. For direct CLI runs:
```bash
php index.php
```

## Troubleshooting

- Ensure correct PEM key paths and readable permissions.
- Confirm environment (`UAT`/`PROD`) via `PAYGLOCAL_Env_VAR`.
- Match example types (monetary values as strings) to avoid validation errors.
- Increase log level to `debug` when diagnosing.

## License

MIT 