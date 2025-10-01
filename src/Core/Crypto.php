<?php

namespace PayGlocal\PgClientSdk\Core;

use PayGlocal\PgClientSdk\Utils\Logger;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Encryption\JWEBuilder;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Encryption\Serializer\CompactSerializer as JWECompactSerializer;
use Jose\Component\Signature\Serializer\CompactSerializer as JWSCompactSerializer;

/**
 * Crypto operations for JWE and JWS generation
 */
class Crypto
{
    /**
     * Convert PEM to key object 
     * @param string $pem PEM key
     * @param bool $isPrivate Is private key
     * @return JWK Key object
     * @throws \Exception
     */
    public static function pemToKey(string $pem, bool $isPrivate = false): JWK
    {
        if (empty($pem) || !str_contains($pem, '-----')) {
            throw new \Exception('pem must be a non-empty string with ----- delimiters');
        }

        try {
            return JWKFactory::createFromKey($pem);
        } catch (\Exception $err) {
            Logger::error('Jose import error: ' . $err->getMessage());
            throw new \Exception('Crypto error: Invalid PEM format: ' . $err->getMessage());
        }
    }

    /**
     * Generate JWE for payload 
     * 
     * @param array $payload Payload to encrypt
     * @param Config $config Configuration object
     * @return string JWE token
     * @throws \Exception
     */
    public static function generateJWE(array $payload, Config $config): string
    {
        $iat = time() * 1000; // Convert to milliseconds
        
        $exp = $iat + ($config->tokenExpiration ?? 300000); // Default 5 minutes
        
        $publicKey = self::pemToKey($config->payglocalPublicKey, false);
        $payloadStr = json_encode($payload);

        // Create JWE builder (jose.CompactEncrypt)
        $jweBuilder = new JWEBuilder(
            new \Jose\Component\Core\AlgorithmManager([
                new \Jose\Component\Encryption\Algorithm\KeyEncryption\RSAOAEP256(),
            ]),
            new \Jose\Component\Core\AlgorithmManager([
                new \Jose\Component\Encryption\Algorithm\ContentEncryption\A128CBCHS256(),
            ])
        );

        // Build JWE with protected header 
        
        $jwe = $jweBuilder
            ->create()
            ->withPayload($payloadStr)
            ->withSharedProtectedHeader([
                'alg' => 'RSA-OAEP-256',
                'enc' => 'A128CBC-HS256',
                'iat' => (string)$iat,
                'exp' => (string)$exp,
                'kid' => $config->publicKeyId,
                'issued-by' => $config->merchantId,
            ])
            ->addRecipient($publicKey)
            ->build();

        // Serialize to compact format 
        $serializer = new JWECompactSerializer();
        return $serializer->serialize($jwe, 0);
    }

    /**
     * Generate JWS for a digestable string
     * 
     * @param string $toDigest The input string to hash (can be JWE or payloadPath)
     * @param Config $config Configuration object
     * @return string JWS token
     * @throws \Exception
     */
    public static function generateJWS(string $toDigest, Config $config): string
    {
        $iat = time() * 1000; // Convert to milliseconds 
        $exp = $iat + ($config->tokenExpiration ?? 300000); // Default 5 minutes

        // Create digest ( crypto.createHash('sha256'))
        $digest = base64_encode(hash('sha256', $toDigest, true));
        $digestObject = [
            'digest' => $digest,
            'digestAlgorithm' => 'SHA-256',
            'exp' => $exp,
            'iat' => (string)$iat,
        ];

        $privateKey = self::pemToKey($config->merchantPrivateKey, true);

        // Create JWS builder (jose.SignJWT)
        $jwsBuilder = new JWSBuilder(
            new \Jose\Component\Core\AlgorithmManager([
                new \Jose\Component\Signature\Algorithm\RS256(),
            ])
        );

        // Build JWS with protected header 
        $jws = $jwsBuilder
            ->create()
            ->withPayload(json_encode($digestObject))
            ->addSignature($privateKey, [
                'issued-by' => $config->merchantId,
                'alg' => 'RS256',
                'kid' => $config->privateKeyId,
                'x-gl-merchantId' => $config->merchantId,
                'x-gl-enc' => 'true',
                'is-digested' => 'true',
            ])
            ->build();

        // Serialize to compact format (jose.SignJWT)
        $serializer = new JWSCompactSerializer();
        return $serializer->serialize($jws, 0);
    }
} 