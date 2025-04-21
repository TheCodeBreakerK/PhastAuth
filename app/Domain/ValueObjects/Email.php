<?php

namespace App\Domain\ValueObjects;

use http\Exception\InvalidArgumentException;

/**
 * Email value object that ensures email validity.
 *
 * Encapsulates email validation logic and provides a type-safe way
 * to handle email addresses throughout the application.
 */
class Email
{
    /** @var string The validated email address */
    private string $value;

    /**
     * Initializes a new Email instance.
     *
     * @param string $email The email address to validate and store
     * @throws InvalidArgumentException If the email is invalid
     */
    public function __construct(string $email)
    {
        $this->validate($email);
        $this->value = $email;
    }

    /**
     * Validates an email address format.
     *
     * @param string $email The email address to validate
     * @return void
     * @throws InvalidArgumentException If the email format is invalid
     */
    public function validate(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email");
        }
    }

    /**
     * Gets the validated email value.
     *
     * @return string The validated email address
     */
    public function getValue(): string
    {
        return $this->value;
    }
}