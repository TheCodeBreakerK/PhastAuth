<?php

namespace App\Utils;

use Exception;

/**
 * Simple field validation utility.
 *
 * Provides basic validation functionality for required fields in an array.
 */
class Validator
{
    /**
     * Validates that all specified fields contain non-empty values.
     *
     * @param array $fields Associative array of fields to validate (field => value)
     * @return array The original fields array if validation passes
     * @throws Exception When any field is empty (after trimming)
     */
    public static function validate(array $fields): array
    {
        foreach ($fields as $field => $value) {
            if (empty(trim($value))) {
                throw new Exception("Field '{$field}' is required");
            }
        }

        return $fields;
    }
}