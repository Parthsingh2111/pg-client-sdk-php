<?php

namespace PayGlocal\PgClientSdk\Helper;

use PayGlocal\PgClientSdk\Utils\Validators;
use PayGlocal\PgClientSdk\Utils\SchemaValidator;
use PayGlocal\PgClientSdk\Utils\Logger;

/**
 * Simple validation function
 * Matches JavaScript validationHelper behavior exactly
 */
class ValidationHelper
{
    /**
     * Simple validation function
     * @param array $payload Payload to validate
     * @param array $options Validation options
     * @param array $options['requiredFields'] Array of required field paths
     * @param bool $options['validateSchema'] Whether to validate schema (default: true)
     * @param array|null $options['operationType'] Operation type validation
     * @param array|null $options['conditionalValidation'] Conditional validation
     * @return void
     * @throws \Exception
     */
    public static function validatePayload(array $payload, array $options = []): void
    {
        $requiredFields = $options['requiredFields'] ?? [];
        $validateSchema = $options['validateSchema'] ?? true;
        $operationType = $options['operationType'] ?? null;
        $conditionalValidation = $options['conditionalValidation'] ?? null;

        try {
            // 1. All Required Fields Validation (SECOND - to catch missing service-specific fields)
            if (!empty($requiredFields)) {
                $validationData = [];
                foreach ($requiredFields as $field) {
                    $keys = explode('.', $field);
                    $value = $payload;
                    foreach ($keys as $key) {
                        $value = $value[$key] ?? null;
                    }
                    $validationData[$field] = $value;
                }
                
                Validators::validateRequiredFields($validationData, $requiredFields);
            }

            // 2. Operation Type Validation (THIRD - to validate specific operation types)
            if ($operationType) {
                $field = $operationType['field'] ?? '';
                $validTypes = $operationType['validTypes'] ?? [];
                
                $keys = explode('.', $field);
                $typeValue = $payload;
                foreach ($keys as $key) {
                    $typeValue = $typeValue[$key] ?? null;
                }
                
                if (!in_array($typeValue, $validTypes)) {
                    throw new \Exception(
                        "Invalid value for $field: $typeValue. Expected one of: " . implode(', ', $validTypes)
                    );
                }
            }
             
            // 3. Conditional Field Validation
            if ($conditionalValidation) {
                $condition = $conditionalValidation['condition'] ?? '';
                $value = $conditionalValidation['value'] ?? null;
                $conditionalFields = $conditionalValidation['requiredFields'] ?? [];
                
                $keys = explode('.', $condition);
                $actual = $payload;
                foreach ($keys as $key) {
                    $actual = $actual[$key] ?? null;
                }

                if ($actual === $value && !empty($conditionalFields)) {
                    // Validate conditional fields directly from payload
                    foreach ($conditionalFields as $field) {
                        $keys = explode('.', $field);
                        $val = $payload;
                        foreach ($keys as $key) {
                            if (!isset($val[$key])) {
                                throw new \Exception("Missing required field: $field");
                            }
                            $val = $val[$key];
                        }
                        
                        if (empty($val) && $val !== 0 && $val !== '0') {
                            throw new \Exception("Required field cannot be empty: $field");
                        }
                    }
                }
            }

            // 4. Schema Validation (SECOND - only if required fields are present)
            if ($validateSchema) {
                $schemaValidator = new SchemaValidator();
                $schemaValidator->validatePaycollectPayload($payload);
            }
            
            Logger::debug('Validation passed');
        } catch (\Exception $error) {
            Logger::error('Validation failed', $error);
            throw new \Exception($error->getMessage());
        }
    }
} 