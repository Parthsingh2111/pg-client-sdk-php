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
 * Status Service for handling status check operations
 * Matches JavaScript status behavior exactly
 */
class StatusService
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Common status check initiation function
     * @param array $params Status check parameters
     * @param Config $config Configuration
     * @return array Status response
     * @throws \Exception
     */
    private function initiateCheckStatusOperation(array $params, Config $config): array
    {
        $gid = $params['gid'];
        
        Logger::info('Initiating status check', ['gid' => $gid]);
        
        // 1. Comprehensive validation (without schema validation for transaction services)
        ValidationHelper::validatePayload($params, [
            'requiredFields' => ['gid'],
            'validateSchema' => false // Disable schema validation for transaction services
        ]);

        // 3. Generate tokens (use the params we received)
        $endpointPath = "/gl/v1/payments/{$gid}/status";
        $tokens = TokenHelper::generateTokens([], $config, 'status', $endpointPath);
        
        $requestData = null;
        $headers = HeaderHelper::buildJwtHeaders($tokens['jws']);

        // 4. API call
        $response = ApiRequestHelper::makeTransactionServiceRequest([
            'method' => 'GET',
            'baseUrl' => $config->baseUrl,
            'endpoint' => Endpoints::TRANSACTION_SERVICE['STATUS'],
            'gid' => $gid,
            'requestData' => $requestData,
            'headers' => $headers,
            'operation' => 'status check'
        ]);

        Logger::info('Status check completed');

        return $response;
    }

    /**
     * Check payment status.
     * @param array $params Status check parameters
     * @return array Status response
     * @throws \Exception
     */
    public function initiateCheckStatus(array $params): array
    {
        return $this->initiateCheckStatusOperation($params, $this->config);
    }
} 










// <?php

// //https://web-token.spomky-labs.com/the-components/signed-tokens-jws/jws-creation
// //https://web-token.spomky-labs.com/the-components/signed-tokens-jws
// //https://github.com/web-token/jwt-framework/
// // FOR PHP 7.1
// //composer require web-token/jwt-framework:1.3 --ignore-platform-reqs

// // FOR PHP 7.2-7.4
// //composer require web-token/jwt-framework:2.2 --ignore-platform-reqs

// // FOR PHP 8.0
// //composer require web-token/jwt-framework --ignore-platform-reqs

// require 'vendor/autoload.php';

// use Jose\Component\Encryption\Algorithm\ContentEncryption\A128CBCHS256;
// use Jose\Component\Encryption\Algorithm\KeyEncryption\RSAOAEP256;
// use Jose\Component\Core\AlgorithmManager;
// use Jose\Component\Core\Converter\StandardConverter;
// use Jose\Component\Encryption\Compression\CompressionMethodManager;
// use Jose\Component\Encryption\Compression\Deflate;
// use Jose\Component\Encryption\JWEBuilder;
// use Jose\Component\Encryption\Serializer\CompactSerializer;
// use Jose\Component\KeyManagement\JWKFactory;
// use Jose\Component\Signature\JWSBuilder;
// use Jose\Component\Signature\Algorithm\RS256;

// function generateRandomString($length = 16)
// {
//     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
//     $charactersLength = strlen($characters);
//     $randomString = '';
//     for ($i = 0; $i < $length; $i++) {
//         $randomString .= $characters[rand(0, $charactersLength - 1)];
//     }
//     return $randomString;
// }

// $keyEncryptionAlgorithmManager = new AlgorithmManager([
//     new RSAOAEP256(),
// ]);
// $contentEncryptionAlgorithmManager = new AlgorithmManager([
//     new A128CBCHS256(),
// ]);
// $compressionMethodManager = new CompressionMethodManager([
//     new Deflate(),
// ]);

// $jweBuilder = new JWEBuilder(
//     $keyEncryptionAlgorithmManager,
//     $contentEncryptionAlgorithmManager,
//     $compressionMethodManager
// );

// $txnid = "EetZNdaLzpkxug2L";
// $url = "https://api.uat.payglocal.in/gl/v1/payments/$txnid/status";
// $payload = "/gl/v1/payments/$txnid/status";


// $algorithmManager = new AlgorithmManager([
//     new RS256(),
// ]);

// $jwsBuilder = new JWSBuilder(
//     $algorithmManager
// );

// $jwskey = JWKFactory::createFromKeyFile(
//     'kId-eVyEPJDSR0Xm3rQs_axisbank.pem', // Private Key Path
//     // The filename
//     null,
//     [
//         'kid' => 'kId-eVyEPJDSR0Xm3rQs', // Private Key ID
//         'use' => 'sig'
//         //'alg' => 'RSA-OAEP-256',
//     ]
// );

// $jwsheader = [
//     'issued-by' => 'ptplaxis1', // Merchant ID
//     'is-digested' => 'true',
//     'alg' => 'RS256',
//     'x-gl-enc' => 'true',
//     'x-gl-merchantId' => 'ptplaxis1',// Merchant ID
//     'kid' => 'kId-eVyEPJDSR0Xm3rQs', // Private Key ID
//     'x-gl-kid-mid'=> 'axisbank'
// ];

// $hashedPayload = base64_encode(hash('sha256', $payload, $BinaryOutputMode = true));



// $jwspayload = json_encode([
//     'digest' => $hashedPayload,
//     'digestAlgorithm' => "SHA-256",
//     'exp' => 300000,
//     'iat' => (string)round(microtime(true) * 1000)
// ]);

// $jws = $jwsBuilder
//     ->create()              // We want to create a new JWS
//     ->withPayload($payload) // We set the payload
//     ->addSignature($jwskey, $jwsheader)
//     ->build();

// //print_r($jws);


// $jwsserializer = new \Jose\Component\Signature\Serializer\CompactSerializer(); // The serializer
// $jwstoken = $jwsserializer->serialize($jws, 0); // We serialize the recipient at index 0 (we only have one recipient).


// $curl = curl_init();

// // Valid GID from previous transaction


// curl_setopt_array($curl, array(
//     CURLOPT_URL => $url,
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_ENCODING => '',
//     CURLOPT_MAXREDIRS => 10,
//     CURLOPT_TIMEOUT => 0,
//     CURLOPT_FOLLOWLOCATION => true,
//     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     // CURLOPT_CUSTOMREQUEST => 'POST',
//     // CURLOPT_POSTFIELDS => $token,
//     CURLOPT_HTTPHEADER => array(
//         'x-gl-token-external: ' . $jwstoken,
//          'x-gl-kid-mid: axisbank',
//         'Content-Type: text/plain'
//     ),
// ));

// $response = curl_exec($curl);

// $data = json_decode($response, true);

// curl_close($curl);

// echo '
// ';

// echo $response;
// echo '
// ';
 