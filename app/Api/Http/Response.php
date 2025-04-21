<?php

namespace App\Api\Http;

use JsonException;

/**
 * HTTP Response handler for JSON API responses.
 *
 * Provides standardized methods for sending JSON responses
 * with proper HTTP status codes and headers.
 */
class Response
{
    /**
     * Sends a JSON response with HTTP status code.
     *
     * @param array $data Response data to be encoded as JSON
     * @param int $status HTTP status code (default: 200)
     * @return void
     * @throws JsonException If JSON encoding fails (PHP 7.3+)
     */
    public static function json(array $data = [], int $status = 200): void
    {
        http_response_code($status);

        header('Content-Type: application/json');

        echo json_encode($data, JSON_THROW_ON_ERROR);
    }
}