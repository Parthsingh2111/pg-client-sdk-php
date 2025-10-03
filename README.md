
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