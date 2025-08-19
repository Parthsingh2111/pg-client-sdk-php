<?php

namespace PayGlocal\PgClientSdk\Utils;

/**
 * Validation utilities
 * Matches JavaScript validators behavior exactly
 */
class Validators
{
    /**
     * Validate required fields recursively
     * @param array $data Data to validate
     * @param array $requiredFields Array of required field paths
     * @throws \Exception
     */
    public static function validateRequiredFields(array $data, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            $keys = explode('.', $field);
            $value = $data;
            
            foreach ($keys as $key) {
                if (!isset($value[$key])) {
                    throw new \Exception("Missing required field: $field");
                }
                $value = $value[$key];
            }
            
            if (empty($value) && $value !== 0 && $value !== '0') {
                throw new \Exception("Required field cannot be empty: $field");
            }
        }
    }
} 