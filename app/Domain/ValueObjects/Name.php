<?php

namespace App\Domain\ValueObjects;

use http\Exception\InvalidArgumentException;

/**
 * Name value object that enforces validation rules for names.
 *
 * Ensures names meet specific criteria:
 * - Not empty
 * - Maximum length of 100 characters
 * - Only contains valid characters (letters, spaces, apostrophes, and hyphens)
 */
class Name
{
    /** @var int Maximum allowed length for names */
    private const MAX_LENGTH = 100;

    /** @var string Regular expression pattern for valid name characters */
    private const VALID_CHARS_REGEX = '/^[\p{L}\s\'-]+$/u';

    /** @var string The validated name value */
    private string $value;

    /**
     * Creates a new validated Name instance.
     *
     * @param string $name The name to validate and store
     * @throws InvalidArgumentException If the name fails validation
     */
    public function __construct(string $name)
    {
        $this->validate($name);
        $this->value = $name;
    }

    /**
     * Validates a name according to business rules.
     *
     * @param string $name The name to validate
     * @return void
     * @throws InvalidArgumentException With specific message for each validation failure:
     *         - If name is empty
     *         - If name exceeds maximum length
     *         - If name contains invalid characters
     */
    public function validate(string $name): void
    {
        $trimmedName = trim($name);

        if (empty($trimmedName)) {
            throw new InvalidArgumentException("Name cannot be empty");
        }

        if (strlen($name) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                "Name cannot be longer than " . self::MAX_LENGTH . " characters"
            );
        }

        if (!preg_match(self::VALID_CHARS_REGEX, $name)) {
            throw new InvalidArgumentException(
                "Name contains invalid characters. Only letters, spaces, apostrophes, and hyphens are allowed"
            );
        }
    }

    /**
     * Gets the validated name value.
     *
     * @return string The validated name
     */
    public function getValue(): string
    {
        return $this->value;
    }
}