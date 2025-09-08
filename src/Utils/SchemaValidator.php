<?php

namespace PayGlocal\PgClientSdk\Utils;

/**
 * Schema Validator for JSON schema validation
 * Mirrors JavaScript schemaValidator.js behavior
 */
class SchemaValidator
{
    private array $payglocalSchema;

    private const EXPECTED_HIERARCHY = [
        'root' => ['merchantTxnId','merchantUniqueId','captureTxn','gpiTxnTimeout','totalAmount','txnCurrency','paymentData', 'standingInstruction', 'riskData','merchantCallbackURL'],
        'paymentData' => ['totalAmount','cardData', 'tokenData', 'billingData'],
        'standingInstruction' => ['data'],
        'riskData' => ['orderData', 'customerData', 'shippingData', 'flightData', 'trainData', 'busData', 'cabData', 'lodgingData'],
        'riskData_flightData' => ['legData', 'passengerData'],
        'riskData_trainData' => ['legData', 'passengerData'],
        'riskData_busData' => ['legData', 'passengerData'],
        'riskData_cabData' => ['legData', 'passengerData'],
        'riskData_lodgingData' => ['rooms']
    ];

    public function __construct()
    {
        $this->payglocalSchema = $this->getPayglocalSchema();
    }

    private function getPayglocalSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['merchantTxnId', 'merchantCallbackURL', 'paymentData'],
            'properties' => [
                'merchantTxnId' => ['type' => 'string'],
                'merchantUniqueId' => ['type' => ['string', 'null']],
                'merchantCallbackURL' => ['type' => 'string'],
                'captureTxn' => ['type' => ['boolean', 'null']],
                'gpiTxnTimeout' => ['type' => 'string'],
                'paymentData' => [
                    'type' => 'object',
                    'required' => ['totalAmount', 'txnCurrency'],
                    'properties' => [
                        'totalAmount' => ['type' => 'string'],
                        'txnCurrency' => ['type' => 'string'],
                        'cardData' => [
                            'type' => 'object',
                            'properties' => [
                                'number' => ['type' => 'string'],
                                'expiryMonth' => ['type' => 'string'],
                                'expiryYear' => ['type' => 'string'],
                                'securityCode' => ['type' => 'string'],
                                'type' => ['type' => 'string']
                            ]
                        ],
                        'tokenData' => [
                            'type' => 'object',
                            'properties' => [
                                'altId' => ['type' => 'string'],
                                'number' => ['type' => 'string'],
                                'expiryMonth' => ['type' => 'string'],
                                'expiryYear' => ['type' => 'string'],
                                'securityCode' => ['type' => 'string'],
                                'requestorID' => ['type' => 'string'],
                                'hashOfFirstSix' => ['type' => 'string'],
                                'cryptogram' => ['type' => 'string'],
                                'firstSix' => ['type' => 'string'],
                                'lastFour' => ['type' => ['string', 'null']],
                                'cardBrand' => ['type' => 'string'],
                                'cardCountryCode' => ['type' => 'string'],
                                'cardIssuerName' => ['type' => 'string'],
                                'cardType' => ['type' => 'string'],
                                'cardCategory' => ['type' => 'string'],
                                'referenceNo' => ['type' => 'string']
                            ]
                        ],
                        'billingData' => [
                            'type' => 'object',
                            'properties' => [
                                'fullName' => ['type' => 'string'],
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
                                'callingCode' => ['type' => 'string'],
                                'phoneNumber' => ['type' => 'string'],
                                'panNumber' => ['type' => 'string']
                            ]
                        ]
                        ]
                    ],
                'standingInstruction' => [
                    'type' => 'object',
                    'properties' => [
                        'data' => [
                            'type' => 'object',
                            'properties' => [
                                'amount' => ['type' => 'string'],
                                'maxAmount' => ['type' => 'string'],
                                'numberOfPayments' => ['type' => 'string'],
                                'frequency' => ['type' => 'string'],
                                'type' => ['type' => 'string'],
                                'startDate' => ['type' => 'string']
                            ]
                        ]
                    ]
                ],
                'riskData' => [
                    'type' => 'object',
                    'properties' => [
                        'orderData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'productDescription' => ['type' => 'string'],
                                    'productSKU' => ['type' => 'string'],
                                    'productType' => ['type' => 'string'],
                                    'itemUnitPrice' => ['type' => 'string'],
                                    'itemQuantity' => ['type' => 'string']
                                ]
                            ]
                        ],
                        'customerData' => [
                            'type' => 'object',
                            'properties' => [
                                'customerAccountType' => ['type' => 'string'],
                                'customerSuccessOrderCount' => ['type' => 'string'],
                                'customerAccountCreationDate' => ['type' => 'string'],
                                'merchantAssignedCustomerId' => ['type' => 'string']
                            ]
                        ],
                        'shippingData' => [
                            'type' => 'object',
                            'properties' => [
                                'fullName' => ['type' => 'string'],
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
                                'callingCode' => ['type' => 'string'],
                                'phoneNumber' => ['type' => 'string']
                            ]
                        ],
                        'flightData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
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
                                            'type' => 'object',
                                            'properties' => [
                                                'routeId' => ['anyOf' => [ ['type' => 'string'], ['type' => 'number'] ]],
                                                'legId' => ['anyOf' => [ ['type' => 'string'], ['type' => 'number'] ]],
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
                                            ]
                                        ]
                                    ],
                                    'passengerData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'title' => ['type' => ['string', 'null']],
                                                'firstName' => ['type' => 'string'],
                                                'lastName' => ['type' => 'string'],
                                                'dateOfBirth' => ['type' => 'string', 'pattern' => '^[0-9]{1,8}$'],
                                                'type' => ['type' => 'string'],
                                                'email' => ['type' => 'string'],
                                                'passportNumber' => ['type' => 'string'],
                                                'passportCountry' => ['type' => 'string'],
                                                'passportIssueDate' => ['type' => 'string', 'maxLength' => 8],
                                                'passportExpiryDate' => ['type' => 'string', 'maxLength' => 8],
                                                'referenceNumber' => ['type' => 'string']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'trainData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'ticketNumber' => ['type' => 'string'],
                                    'reservationDate' => ['type' => 'string', 'maxLength' => 8, 'pattern' => '^[0-9]{1,8}$'],
                                    'legData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'routeId' => ['anyOf' => [ ['type' => 'string'], ['type' => 'number'] ]],
                                                'legId' => ['anyOf' => [ ['type' => 'string'], ['type' => 'number'] ]],
                                                'trainNumber' => ['type' => 'string'],
                                                'departureDate' => ['type' => 'string'],
                                                'departureCity' => ['type' => 'string'],
                                                'departureCountry' => ['type' => 'string'],
                                                'arrivalDate' => ['type' => 'string'],
                                                'arrivalCity' => ['type' => 'string'],
                                                'arrivalCountry' => ['type' => 'string']
                                            ]
                                        ]
                                    ],
                                    'passengerData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'firstName' => ['type' => 'string'],
                                                'lastName' => ['type' => 'string'],
                                                'dateOfBirth' => ['type' => 'string']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'busData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'ticketNumber' => ['type' => 'string'],
                                    'reservationDate' => ['type' => 'string'],
                                    'legData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
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
                                            ]
                                        ]
                                    ],
                                    'passengerData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'firstName' => ['type' => 'string'],
                                                'lastName' => ['type' => 'string'],
                                                'dateOfBirth' => ['type' => 'string'],
                                                'passportCountry' => ['type' => 'string']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'cabData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'reservationDate' => ['type' => 'string'],
                                    'legData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'routeId' => ['type' => 'string'],
                                                'legId' => ['type' => 'string'],
                                                'pickupDate' => ['type' => 'string']
                                            ]
                                        ]
                                    ],
                                    'passengerData' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'firstName' => ['type' => 'string'],
                                                'lastName' => ['type' => 'string'],
                                                'dateOfBirth' => ['type' => 'string'],
                                                'passportCountry' => ['type' => 'string']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'lodgingData' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
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
                                            'type' => 'object',
                                            'properties' => [
                                                'roomType' => ['type' => 'string'],
                                                'roomCategory' => ['type' => 'string'],
                                                'roomPrice' => ['type' => 'string'],
                                                'numberOfGuests' => ['type' => 'string'],
                                                'numberOfNights' => ['type' => 'string'],
                                                'guestFirstName' => ['type' => 'string'],
                                                'guestLastName' => ['type' => 'string'],
                                                'guestEmail' => ['type' => 'string']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function validatePaycollectPayload(array $payload): array
    {
        $errors = $this->validateSchema($payload, $this->payglocalSchema);
        
        if (!empty($errors)) {
            $errorsMap = [];
            foreach ($errors as $error) {
                $fieldPath = $error['field'];
                $fieldPath = str_replace(['[', ']'], ['.', ''], $fieldPath);
                if (!isset($errorsMap[$fieldPath])) {
                    $errorsMap[$fieldPath] = $error['error'];
                }
            }

            $responseShape = [
                'gid' => null,
                'status' => 'REQUEST_ERROR',
                'message' => 'Invalid request fields',
                'timestamp' => date('c'),
                'reasonCode' => 'LOCAL-400-001',
                'data' => null,
                'errors' => $errorsMap
            ];

            throw new \Exception(json_encode($responseShape));
        }

        $hierarchicalValidation = $this->validateHierarchicalPlacement($payload);
        
        if (!empty($hierarchicalValidation['warnings'])) {
            Logger::warn('Hierarchical placement warnings detected', [
                'warningCount' => count($hierarchicalValidation['warnings']),
                'warnings' => $hierarchicalValidation['warnings']
            ]);
            
            foreach ($hierarchicalValidation['warnings'] as $warning) {
                Logger::warn('Hierarchical Warning: ' . $warning['message'], [
                    'currentPath' => $warning['currentPath'] ?? null,
                    'expectedPath' => $warning['expectedPath'] ?? null,
                    'objectType' => $warning['objectType'] ?? null
                ]);
            }
        }

        Logger::debug('Payload has passed payglocal schema validation for payCollect method');
        return [
            'message' => 'Payload is valid, payload have passed payglocal schema validation for payCollect method',
            'hierarchicalWarnings' => $hierarchicalValidation['warnings'] ?? [],
            'warningCount' => count($hierarchicalValidation['warnings'] ?? [])
        ];
    }

    private function validateSchema($data, array $schema, string $path = ''): array
    {
        $errors = [];
        
        // required
        if (isset($schema['required']) && is_array($schema['required'])) {
            foreach ($schema['required'] as $field) {
                if (!is_array($data) || !array_key_exists($field, $data)) {
                    $errors[] = [
                        'field' => $path ? "$path.$field" : $field,
                        'error' => "Missing required field: $field"
                    ];
                }
            }
        }
        
        // type on current node
        if (isset($schema['type'])) {
            $typeErrors = $this->validateType($data, $schema['type'], $path ?: 'root');
                        $errors = array_merge($errors, $typeErrors);
                    }
                    
        // properties (object)
        if (isset($schema['properties']) && is_array($schema['properties']) && is_array($data)) {
            foreach ($schema['properties'] as $key => $propSchema) {
                if (array_key_exists($key, $data)) {
                    $fieldPath = $path ? "$path.$key" : $key;
                    $value = $data[$key];

                    // anyOf support (e.g., string or number)
                    if (isset($propSchema['anyOf']) && is_array($propSchema['anyOf'])) {
                        $passesAny = false;
                        foreach ($propSchema['anyOf'] as $candidate) {
                            $candidateErrors = [];
                            if (isset($candidate['type'])) {
                                $candidateErrors = $this->validateType($value, $candidate['type'], $fieldPath);
                            }
                            if (empty($candidateErrors)) {
                                $passesAny = true;
                                break;
                            }
                        }
                        if (!$passesAny) {
                            $errors[] = [
                                'field' => $fieldPath,
                                'error' => 'Invalid type: expected one of anyOf'
                            ];
                        }
                    }

                    // type
                    if (isset($propSchema['type'])) {
                        $errors = array_merge($errors, $this->validateType($value, $propSchema['type'], $fieldPath));
                    }

                    // pattern
                    if (isset($propSchema['pattern']) && is_string($value)) {
                        $pattern = '/' . $propSchema['pattern'] . '/';
                        if (!preg_match($pattern, $value)) {
                            $errorMsg = (preg_match('/^\^\[0-9\]\+\$|^\^\[0-9\]\{1,8\}\$/', $propSchema['pattern']))
                                ? 'NOT_NUMERIC'
                                : 'Pattern mismatch';
                            $errors[] = [
                                'field' => $fieldPath,
                                'error' => $errorMsg
                            ];
                        }
                    }

                    // maxLength
                    if (isset($propSchema['maxLength']) && is_string($value)) {
                        if (strlen($value) > (int)$propSchema['maxLength']) {
                                    $errors[] = [
                                        'field' => $fieldPath,
                                'error' => 'OVER_MAX_LENGTH, expected maxLength: ' . $propSchema['maxLength']
                            ];
                        }
                    }

                    // recurse objects
                    if (is_array($value) && isset($propSchema['properties'])) {
                        $errors = array_merge($errors, $this->validateSchema($value, $propSchema, $fieldPath));
                    }

                    // arrays
                    if (is_array($value) && isset($propSchema['items'])) {
                        foreach ($value as $index => $item) {
                            $itemPath = $fieldPath . '[' . $index . ']';
                            $errors = array_merge($errors, $this->validateSchema($item, $propSchema['items'], $itemPath));
                        }
                    }
                }
            }
        }
        
        return $errors;
    }

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

    private function isValidType($data, string $type): bool
    {
        switch ($type) {
            case 'number':
                return is_int($data) || is_float($data);
            case 'string':
                return is_string($data);
            case 'boolean':
                return is_bool($data);
            case 'object':
                // JSON objects are associative arrays in PHP
                return is_array($data);
            case 'array':
                return is_array($data);
            case 'null':
                return $data === null;
            default:
                return false;
        }
    }

    private function validateHierarchicalPlacement(array $payload): array
    {
        $warnings = [];

        $this->check($payload, '', 'root', $warnings);

        return [
            'isValid' => true,
            'warnings' => $warnings,
            'misplacedObjects' => $warnings // keep legacy field name for compatibility
        ];
    }

    private function isContainer($val): bool
    {
        if (!$val || !is_array($val)) return false;
        if (array_keys($val) !== range(0, count($val) - 1)) return true; // Associative array (object)
        return true; // Indexed array
    }

    private function nextPathFor(string $expectedPath, string $key): string
    {
        $next = $expectedPath;
        if ($expectedPath === 'root') {
            if ($key === 'paymentData') $next = 'paymentData';
            else if ($key === 'standingInstruction') $next = 'standingInstruction';
            else if ($key === 'riskData') $next = 'riskData';
        } else if ($expectedPath === 'paymentData') {
            if ($key === 'cardData') $next = 'cardData';
            else if ($key === 'tokenData') $next = 'tokenData';
            else if ($key === 'billingData') $next = 'billingData';
        } else if ($expectedPath === 'standingInstruction') {
            if ($key === 'data') $next = 'standingInstruction_data';
        } else if ($expectedPath === 'riskData') {
            if ($key === 'orderData') $next = 'riskData_orderData';
            else if ($key === 'customerData') $next = 'riskData_customerData';
            else if ($key === 'shippingData') $next = 'riskData_shippingData';
            else if ($key === 'flightData') $next = 'riskData_flightData';
            else if ($key === 'trainData') $next = 'riskData_trainData';
            else if ($key === 'busData') $next = 'riskData_busData';
            else if ($key === 'cabData') $next = 'riskData_cabData';
            else if ($key === 'lodgingData') $next = 'riskData_lodgingData';
        } else if ($expectedPath === 'riskData_flightData') {
            if ($key === 'legData') $next = 'riskData_flightData_legData';
            else if ($key === 'passengerData') $next = 'riskData_flightData_passengerData';
        } else if ($expectedPath === 'riskData_trainData') {
            if ($key === 'legData') $next = 'riskData_trainData_legData';
            else if ($key === 'passengerData') $next = 'riskData_trainData_passengerData';
        } else if ($expectedPath === 'riskData_busData') {
            if ($key === 'legData') $next = 'riskData_busData_legData';
            else if ($key === 'passengerData') $next = 'riskData_busData_passengerData';
        } else if ($expectedPath === 'riskData_cabData') {
            if ($key === 'legData') $next = 'riskData_cabData_legData';
            else if ($key === 'passengerData') $next = 'riskData_cabData_passengerData';
        } else if ($expectedPath === 'riskData_lodgingData') {
            if ($key === 'rooms') $next = 'riskData_lodgingData_rooms';
        }
        return $next;
    }

    private function isAssociative(array $arr): bool
    {
        if ($arr === []) return true; // treat empty as associative for placement check
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function check($obj, string $path, string $expectedPath, array &$warnings): void
    {
        if (!is_array($obj)) return;

        foreach ($obj as $key => $value) {
            $currentPath = $path ? ($path . '.' . $key) : $key;

            // Only warn for containers (objects/arrays), never primitives
            if ($this->isContainer($value)) {
                $expected = self::EXPECTED_HIERARCHY[$expectedPath] ?? [];
                if (!in_array($key, $expected, true)) {
                    $warnings[] = [
                        'type' => 'hierarchical_placement',
                        'message' => 'Object "' . $key . '" at path "' . $currentPath . '" might be misplaced',
                        'currentPath' => $currentPath,
                        'expectedPath' => $expectedPath,
                        'objectType' => $this->isAssociative($value) ? 'object' : 'array'
                    ];
                    // Do not recurse into misplaced subtree
                    continue;
                }

                // Recurse into correctly placed containers
                $nextExpected = $this->nextPathFor($expectedPath, $key);
                if ($this->isAssociative($value)) {
                    $this->check($value, $currentPath, $nextExpected, $warnings);
                } else { // Indexed array
                    foreach ($value as $index => $item) {
                        $this->check($item, $currentPath . '[' . $index . ']', $nextExpected, $warnings);
                    }
                }
            }
        }
    }
}
