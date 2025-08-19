<?php

namespace PayGlocal\PgClientSdk\Utils;

use PayGlocal\PgClientSdk\Utils\Logger;

/**
 * Schema Validator for JSON schema validation
 * Matches JavaScript schemaValidator.js behavior exactly
 */
class SchemaValidator
{
    private array $payglocalSchema;

    public function __construct()
    {
        $this->payglocalSchema = $this->getPayglocalSchema();
    }

    /**
     * Get the complete PayGlocal schema (matches JavaScript exactly)
     * @return array
     */
    private function getPayglocalSchema(): array
    {
        return [
            'type' => 'array', // PHP associative arrays are arrays, not objects
            'required' => ['merchantTxnId', 'merchantCallbackURL', 'paymentData'],
            'properties' => [
                'merchantTxnId' => ['type' => 'string'],
                'merchantUniqueId' => ['type' => ['string', 'null']],
                'merchantCallbackURL' => ['type' => 'string'],
                'captureTxn' => ['type' => ['boolean', 'null']],
                'gpiTxnTimeout' => ['type' => 'string'],
                'paymentData' => [
                    'type' => 'array', // PHP associative arrays are arrays
                    'required' => ['totalAmount', 'txnCurrency'],
                    'properties' => [
                        'totalAmount' => ['type' => 'string'],
                        'txnCurrency' => ['type' => 'string'],
                        'cardData' => [
                            'type' => 'array', // PHP associative arrays are arrays
                            'properties' => [
                                'number' => ['type' => 'string'],
                                'expiryMonth' => ['type' => 'string'],
                                'expiryYear' => ['type' => 'string'],
                                'securityCode' => ['type' => 'string'],
                                'type' => ['type' => 'string']
                            ],
                            'additionalProperties' => false
                        ],
                        'tokenData' => [
                            'type' => 'array', // PHP associative arrays are arrays
                            'properties' => [
                                'number' => ['type' => 'string'],
                                'expiryMonth' => ['type' => 'string'],
                                'expiryYear' => ['type' => 'string'],
                                'cryptogram' => ['type' => 'string'],
                                'firstSix' => ['type' => 'string'],
                                'lastFour' => ['type' => 'string'],
                                'cardBrand' => ['type' => 'string'],
                                'cardCountryCode' => ['type' => 'string'],
                                'cardIssuerName' => ['type' => 'string'],
                                'cardType' => ['type' => 'string'],
                                'cardCategory' => ['type' => 'string']
                            ],
                            'additionalProperties' => false
                        ],
                        'billingData' => [
                            'type' => 'array', // PHP associative arrays are arrays
                            'properties' => [
                                'firstName' => ['type' => 'string'],
                                'lastName' => ['type' => 'string'],
                                'addressStreet1' => ['type' => 'string'],
                                'addressStreet2' => ['type' => ['string', 'null']],
                                'addressCity' => ['type' => 'string'],
                                'addressState' => ['type' => 'string'],
                                'addressPostalCode' => ['type' => 'string'],
                                'emailId' => ['type' => 'string'],
                                'phoneNumber' => ['type' => 'string']
                            ],
                            'additionalProperties' => false
                        ]
                    ],
                    'additionalProperties' => false
                ],
                'standingInstruction' => [
                    'type' => 'array', // PHP associative arrays are arrays
                    'properties' => [
                        'data' => [
                            'type' => 'array', // PHP associative arrays are arrays
                            'properties' => [
                                'amount' => ['type' => 'string'],
                                'maxAmount' => ['type' => 'string'],
                                'numberOfPayments' => ['type' => 'string'],
                                'frequency' => ['type' => 'string'],
                                'type' => ['type' => 'string'],
                                'startDate' => ['type' => 'string']
                            ],
                            'additionalProperties' => false
                        ]
                    ],
                    'additionalProperties' => false
                ],
                'riskData' => [
                    'type' => 'array', // PHP associative arrays are arrays
                    'properties' => [
                        'orderData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'array', // PHP associative arrays are arrays
                                'properties' => [
                                    'productDescription' => ['type' => 'string'],
                                    'productSKU' => ['type' => 'string'],
                                    'productType' => ['type' => 'string'],
                                    'itemUnitPrice' => ['type' => 'string'],
                                    'itemQuantity' => ['type' => 'string']
                                ],
                                'additionalProperties' => false
                            ]
                        ],
                        'customerData' => [
                            'type' => 'array', // PHP associative arrays are arrays
                            'properties' => [
                                'customerAccountType' => ['type' => 'string'],
                                'customerSuccessOrderCount' => ['type' => 'string'],
                                'customerAccountCreationDate' => ['type' => 'string'],
                                'merchantAssignedCustomerId' => ['type' => 'string']
                            ],
                            'additionalProperties' => false
                        ],
                        'shippingData' => [
                            'type' => 'array', // PHP associative arrays are arrays
                            'properties' => [
                                'firstName' => ['type' => 'string'],
                                'lastName' => ['type' => 'string'],
                                'addressStreet1' => ['type' => 'string'],
                                'addressStreet2' => ['type' => ['string', 'null']],
                                'addressCity' => ['type' => 'string'],
                                'addressState' => ['type' => 'string'],
                                'addressStateCode' => ['type' => 'string'],
                                'addressPostalCode' => ['type' => 'string'],
                                'addressCountry' => ['type' => 'string'],
                                'emailId' => ['type' => 'string'],
                                'phoneNumber' => ['type' => 'string']
                            ],
                            'additionalProperties' => false
                        ],
                        'flightData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'array', // PHP associative arrays are arrays
                                'properties' => [
                                    'agentCode' => ['type' => 'string'],
                                    'agentName' => ['type' => 'string'],
                                    'ticketNumber' => ['type' => 'string'],
                                    'reservationDate' => ['type' => 'string'],
                                    'ticketIssueCity' => ['type' => 'string'],
                                    'ticketIssueState' => ['type' => 'string'],
                                    'ticketIssueCountry' => ['type' => 'string'],
                                    'ticketIssuePostalCode' => ['type' => 'string'],
                                    'reservationCode' => ['type' => 'string'],
                                    'reservationSystem' => ['type' => 'string'],
                                    'journeyType' => ['type' => 'string'],
                                    'electronicTicket' => ['type' => 'string'],
                                    'refundable' => ['type' => 'string'],
                                    'ticketType' => ['type' => 'string'],
                                    'legData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'routeId' => ['type' => 'string'],
                                                'legId' => ['type' => 'string'],
                                                'flightNumber' => ['type' => 'string'],
                                                'departureDate' => ['type' => 'string'],
                                                'departureAirportCode' => ['type' => 'string'],
                                                'departureCity' => ['type' => 'string'],
                                                'departureCountry' => ['type' => 'string'],
                                                'arrivalDate' => ['type' => 'string'],
                                                'arrivalAirportCode' => ['type' => 'string'],
                                                'arrivalCity' => ['type' => 'string'],
                                                'arrivalCountry' => ['type' => 'string'],
                                                'carrierCode' => ['type' => 'string'],
                                                'carrierName' => ['type' => 'string'],
                                                'serviceClass' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ],
                                    'passengerData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'title' => ['type' => ['string', 'null']],
                                                'firstName' => ['type' => 'string'],
                                                'lastName' => ['type' => 'string'],
                                                'dateOfBirth' => ['type' => 'string'],
                                                'type' => ['type' => 'string'],
                                                'email' => ['type' => 'string'],
                                                'passportNumber' => ['type' => 'string'],
                                                'passportCountry' => ['type' => 'string'],
                                                'passportIssueDate' => ['type' => 'string'],
                                                'passportExpiryDate' => ['type' => 'string'],
                                                'referenceNumber' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ]
                                ],
                                'additionalProperties' => false
                            ]
                        ],
                        'trainData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'array', // PHP associative arrays are arrays
                                'properties' => [
                                    'ticketNumber' => ['type' => 'string'],
                                    'reservationDate' => ['type' => 'string'],
                                    'legData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'routeId' => ['type' => 'string'],
                                                'legId' => ['type' => 'string'],
                                                'trainNumber' => ['type' => 'string'],
                                                'departureDate' => ['type' => 'string'],
                                                'departureCity' => ['type' => 'string'],
                                                'departureCountry' => ['type' => 'string'],
                                                'arrivalDate' => ['type' => 'string'],
                                                'arrivalCity' => ['type' => 'string'],
                                                'arrivalCountry' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ],
                                    'passengerData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'title' => ['type' => ['string', 'null']],
                                                'firstName' => ['type' => 'string'],
                                                'lastName' => ['type' => 'string'],
                                                'dateOfBirth' => ['type' => 'string'],
                                                'type' => ['type' => 'string'],
                                                'email' => ['type' => 'string'],
                                                'passportNumber' => ['type' => 'string'],
                                                'passportCountry' => ['type' => 'string'],
                                                'passportIssueDate' => ['type' => 'string'],
                                                'passportExpiryDate' => ['type' => 'string'],
                                                'referenceNumber' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ]
                                ],
                                'additionalProperties' => false
                            ]
                        ],
                        'busData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'array', // PHP associative arrays are arrays
                                'properties' => [
                                    'ticketNumber' => ['type' => 'string'],
                                    'reservationDate' => ['type' => 'string'],
                                    'legData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'routeId' => ['type' => 'string'],
                                                'legId' => ['type' => 'string'],
                                                'busNumber' => ['type' => 'string'],
                                                'departureDate' => ['type' => 'string'],
                                                'departureCity' => ['type' => 'string'],
                                                'departureCountry' => ['type' => 'string'],
                                                'arrivalDate' => ['type' => 'string'],
                                                'arrivalCity' => ['type' => 'string'],
                                                'arrivalCountry' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ],
                                    'passengerData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'title' => ['type' => ['string', 'null']],
                                                'firstName' => ['type' => 'string'],
                                                'lastName' => ['type' => 'string'],
                                                'dateOfBirth' => ['type' => 'string'],
                                                'type' => ['type' => 'string'],
                                                'email' => ['type' => 'string'],
                                                'passportNumber' => ['type' => 'string'],
                                                'passportCountry' => ['type' => 'string'],
                                                'passportIssueDate' => ['type' => 'string'],
                                                'passportExpiryDate' => ['type' => 'string'],
                                                'referenceNumber' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ]
                                ],
                                'additionalProperties' => false
                            ]
                        ],
                        'shipData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'array', // PHP associative arrays are arrays
                                'properties' => [
                                    'ticketNumber' => ['type' => 'string'],
                                    'reservationDate' => ['type' => 'string'],
                                    'legData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'routeId' => ['type' => 'string'],
                                                'legId' => ['type' => 'string'],
                                                'shipNumber' => ['type' => 'string'],
                                                'departureDate' => ['type' => 'string'],
                                                'departureCity' => ['type' => 'string'],
                                                'departureCountry' => ['type' => 'string'],
                                                'arrivalDate' => ['type' => 'string'],
                                                'arrivalCity' => ['type' => 'string'],
                                                'arrivalCountry' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ],
                                    'passengerData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'title' => ['type' => ['string', 'null']],
                                                'firstName' => ['type' => 'string'],
                                                'lastName' => ['type' => 'string'],
                                                'dateOfBirth' => ['type' => 'string'],
                                                'type' => ['type' => 'string'],
                                                'email' => ['type' => 'string'],
                                                'passportNumber' => ['type' => 'string'],
                                                'passportCountry' => ['type' => 'string'],
                                                'passportIssueDate' => ['type' => 'string'],
                                                'passportExpiryDate' => ['type' => 'string'],
                                                'referenceNumber' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ]
                                ],
                                'additionalProperties' => false
                            ]
                        ],
                        'cabData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'array', // PHP associative arrays are arrays
                                'properties' => [
                                    'reservationDate' => ['type' => 'string'],
                                    'legData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'routeId' => ['type' => 'string'],
                                                'legId' => ['type' => 'string'],
                                                'pickupDate' => ['type' => 'string'],
                                                'departureCity' => ['type' => 'string'],
                                                'departureCountry' => ['type' => 'string'],
                                                'arrivalCity' => ['type' => 'string'],
                                                'arrivalCountry' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ],
                                    'passengerData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'title' => ['type' => ['string', 'null']],
                                                'firstName' => ['type' => 'string'],
                                                'lastName' => ['type' => 'string'],
                                                'dateOfBirth' => ['type' => 'string'],
                                                'type' => ['type' => 'string'],
                                                'email' => ['type' => 'string'],
                                                'passportNumber' => ['type' => 'string'],
                                                'passportCountry' => ['type' => 'string'],
                                                'passportIssueDate' => ['type' => 'string'],
                                                'passportExpiryDate' => ['type' => 'string'],
                                                'referenceNumber' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ]
                                ],
                                'additionalProperties' => false
                            ]
                        ],
                        'lodgingData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'array', // PHP associative arrays are arrays
                                'properties' => [
                                    'checkInDate' => ['type' => 'string'],
                                    'checkOutDate' => ['type' => 'string'],
                                    'lodgingType' => ['type' => 'string'],
                                    'lodgingName' => ['type' => 'string'],
                                    'city' => ['type' => 'string'],
                                    'country' => ['type' => 'string'],
                                    'rating' => ['type' => 'string'],
                                    'cancellationPolicy' => ['type' => 'string'],
                                    'bookingPersonFirstName' => ['type' => 'string'],
                                    'bookingPersonLastName' => ['type' => 'string'],
                                    'bookingPersonEmailId' => ['type' => 'string'],
                                    'bookingPersonCallingCode' => ['type' => 'string'],
                                    'bookingPersonPhoneNumber' => ['type' => 'string'],
                                    'rooms' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'array', // PHP associative arrays are arrays
                                            'properties' => [
                                                'roomType' => ['type' => 'string'],
                                                'roomCategory' => ['type' => 'string'],
                                                'roomPrice' => ['type' => 'string'],
                                                'numberOfGuests' => ['type' => 'string'],
                                                'numberOfNights' => ['type' => 'string'],
                                                'guestFirstName' => ['type' => 'string'],
                                                'guestLastName' => ['type' => 'string'],
                                                'guestEmail' => ['type' => 'string']
                                            ],
                                            'additionalProperties' => false
                                        ]
                                    ]
                                ],
                                'additionalProperties' => false
                            ]
                        ]
                    ],
                    'additionalProperties' => false
                ]
            ],
            'additionalProperties' => false
        ];
    }

    /**
     * Validate PayGlocal payload against schema (matches JavaScript exactly)
     * @param array $payload Payload to validate
     * @return array Validation result
     * @throws \Exception
     */
    public function validatePaycollectPayload(array $payload): array
    {
        $errors = $this->validateSchema($payload, $this->payglocalSchema);
        
        if (!empty($errors)) {
            Logger::error('Validation errors, verify the payload structure, problematic field', $errors);
            throw new \Exception('Schema validation failed');
        }
        
        Logger::debug('Payload has passed payglocal schema validation for payCollect method');
        return ['message' => 'Payload is valid, payload have passed payglocal schema validation for payCollect method'];
    }

    /**
     * Validate data against schema (matches JavaScript logic exactly)
     * @param mixed $data Data to validate
     * @param array $schema Schema to validate against
     * @param string $path Current path for error reporting
     * @return array Array of validation errors
     */
    private function validateSchema($data, array $schema, string $path = ''): array
    {
        $errors = [];
        
        // Check required fields
        if (isset($schema['required']) && is_array($schema['required'])) {
            foreach ($schema['required'] as $field) {
                if (!isset($data[$field])) {
                    $errors[] = [
                        'field' => $path ? "{$path}.{$field}" : $field,
                        'error' => "Missing required field: {$field}"
                    ];
                }
            }
        }
        
        // Check properties
        if (isset($schema['properties']) && is_array($data)) {
            foreach ($data as $key => $value) {
                if (isset($schema['properties'][$key])) {
                    $fieldPath = $path ? "{$path}.{$key}" : $key;
                    $propertySchema = $schema['properties'][$key];
                    
                    // Check type
                    if (isset($propertySchema['type'])) {
                        $typeErrors = $this->validateType($value, $propertySchema['type'], $fieldPath);
                        $errors = array_merge($errors, $typeErrors);
                    }
                    
                    // Check additional properties
                    if (isset($propertySchema['additionalProperties']) && $propertySchema['additionalProperties'] === false) {
                        if (isset($propertySchema['properties'])) {
                            $allowedKeys = array_keys($propertySchema['properties']);
                            foreach ($value as $subKey => $subValue) {
                                if (!in_array($subKey, $allowedKeys)) {
                                    $errors[] = [
                                        'field' => $fieldPath,
                                        'error' => "Unrecognized field \"{$subKey}\""
                                    ];
                                }
                            }
                        }
                    }
                    
                    // Recursively validate nested objects
                    if (is_array($value) && isset($propertySchema['properties'])) {
                        $nestedErrors = $this->validateSchema($value, $propertySchema, $fieldPath);
                        $errors = array_merge($errors, $nestedErrors);
                    }
                    
                    // Validate array items
                    if (is_array($value) && isset($propertySchema['items'])) {
                        foreach ($value as $index => $item) {
                            $itemPath = "{$fieldPath}[{$index}]";
                            $itemErrors = $this->validateSchema($item, $propertySchema['items'], $itemPath);
                            $errors = array_merge($errors, $itemErrors);
                        }
                    }
                }
            }
        }
        
        return $errors;
    }

    /**
     * Validate data type (matches JavaScript logic exactly)
     * @param mixed $data Data to validate
     * @param mixed $type Expected type(s)
     * @param string $path Field path for error reporting
     * @return array Array of validation errors
     */
    private function validateType($data, $type, string $path): array
    {
        $errors = [];
        
        if (is_array($type)) {
            $valid = false;
            foreach ($type as $t) {
                if ($this->isValidType($data, $t)) {
                    $valid = true;
                    break;
                }
            }
            if (!$valid) {
                $errors[] = [
                    'field' => $path,
                    'error' => 'Invalid type: expected ' . implode(' or ', $type) . ', got ' . gettype($data)
                ];
            }
        } else {
            if (!$this->isValidType($data, $type)) {
                $errors[] = [
                    'field' => $path,
                    'error' => 'Invalid type: expected ' . $type . ', got ' . gettype($data)
                ];
            }
        }
        
        return $errors;
    }

    /**
     * @param mixed $data Data to check
     * @param string $type Expected type
     * @return bool Whether data matches type
     */
    private function isValidType($data, string $type): bool
    {
        switch ($type) {
            case 'string':
                return is_string($data);
            case 'boolean':
                return is_bool($data);
            case 'array':
                return is_array($data);
            case 'object':
                return is_object($data);
            case 'null':
                return $data === null;
            default:
                return false;
        }
    }
} 