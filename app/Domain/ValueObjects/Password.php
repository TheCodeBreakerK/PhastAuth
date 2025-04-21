<?php

namespace App\Domain\ValueObjects;

use http\Exception\InvalidArgumentException;

/**
 * Password value object that enforces strong password requirements.
 *
 * Validates and securely hashes passwords using ARGON2ID algorithm.
 * Enforces the following rules:
 * - Minimum 8 characters length
 * - At least one digit
 * - At least one lowercase letter
 * - At least one uppercase letter
 * - At least one special character
 */
class Password
{
    /** @var int Minimum password length requirement */
    private const MIN_LENGTH = 8;

    /** @var string Regex pattern to check for digits */
    private const DIGIT_REGEX = '/\d/';

    /** @var string Regex pattern to check for lowercase letters */
    private const LOWERCASE_REGEX = '/[a-z]/';

    /** @var string Regex pattern to check for uppercase letters */
    private const UPPERCASE_REGEX = '/[A-Z]/';

    /** @var string Regex pattern to check for special characters */
    private const SPECIAL_CHAR_REGEX = '/\W/';

    /** @var string The hashed password value */
    private string $value;

    /**
     * Creates a new Password instance with validation and secure hashing.
     *
     * @param string $password The plain text password to validate and hash
     * @throws InvalidArgumentException If password fails validation requirements
     */
    public function __construct(string $password)
    {
        $this->validate($password);
        $this->value = password_hash($password, PASSWORD_ARGON2ID);
    }

    /**
     * Validates password against strength requirements.
     *
     * @param string $password Plain text password to validate
     * @return void
     * @throws InvalidArgumentException With specific message for each validation failure:
     *         - Minimum length not met
     *         - Missing digit requirement
     *         - Missing lowercase letter
     *         - Missing uppercase letter
     *         - Missing special character
     */
    public function validate(string $password): void
    {
        if (strlen($password) < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                "Password must be at least " . self::MIN_LENGTH . " characters long"
            );
        }

        if (!preg_match(self::DIGIT_REGEX, $password)) {
            throw new InvalidArgumentException(
                "Password must contain at least one digit"
            );
        }

        if (!preg_match(self::LOWERCASE_REGEX, $password)) {
            throw new InvalidArgumentException(
                "Password must contain at least one lowercase letter"
            );
        }

        if (!preg_match(self::UPPERCASE_REGEX, $password)) {
            throw new InvalidArgumentException(
                "Password must contain at least one uppercase letter"
            );
        }

        if (!preg_match(self::SPECIAL_CHAR_REGEX, $password)) {
            throw new InvalidArgumentException(
                "Password must contain at least one special character"
            );
        }
    }

    /**
     * Gets the hashed password value.
     *
     * @return string The hashed password (using ARGON2ID algorithm)
     */
    public function getValue(): string
    {
        return $this->value;
    }
}