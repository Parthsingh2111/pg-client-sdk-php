<?php

namespace PayGlocal\PgClientSdk\Helper;

use PayGlocal\PgClientSdk\Core\Crypto;
use PayGlocal\PgClientSdk\Utils\Logger;

/**
 * Generate JWT tokens (JWE and JWS) for API requests
 */
class TokenHelper
{
    /**
     * Generate JWT tokens (JWE and JWS) for API requests
     * @param array $payload The payload to encrypt in JWE
     * @param object $config Configuration object containing keys and settings
     * @param string $operation Operation name for logging and error messages
     * @param string|null $digestInput Optional input for JWS generation (defaults to JWE)
     * @return array Object containing jwe and jws tokens
     * @throws \Exception
     */
    public static function generateTokens(array $payload, object $config, string $operation, ?string $digestInput = null): array
    {
        $jwe = null;
        $jws = null;
        
        // Generate JWE
        try {
            $jwe = Crypto::generateJWE($payload, $config);
        } catch (\Exception $error) {
            Logger::error("JWE generation failed for $operation: " . $error->getMessage());
            throw new \Exception("Failed to generate JWE for $operation: " . $error->getMessage());
        }

        // Generate JWS
        try {
            $inputForJWS = $digestInput ?: $jwe;
            $jws = Crypto::generateJWS($inputForJWS, $config);
        } catch (\Exception $error) {
            Logger::error("JWS generation failed for $operation: " . $error->getMessage());
            throw new \Exception("Failed to generate JWS for $operation: " . $error->getMessage());
        }

        Logger::debug("Tokens generated for $operation");

        // Return only the tokens
        return ['jwe' => $jwe, 'jws' => $jws];
    }
} 