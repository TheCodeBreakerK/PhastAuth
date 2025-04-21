<?php

namespace App\Api\Http;

/**
 * HTTP Request handler that provides access to common request components.
 *
 * Offers static methods to retrieve request path, body, method, and authorization header.
 */
class Request
{
    /**
     * Gets the current request path.
     *
     * @return string The request path (e.g., '/users/1'), defaults to '/' if empty
     */
    public static function path(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    }

    /**
     * Gets the request body content based on HTTP method.
     *
     * @return array Parsed request body content:
     *               - GET: $_GET super global
     *               - POST/PUT/DELETE: Decoded JSON input
     */
    public static function body(): array
    {
        $json = json_decode(file_get_contents('php://input'), true) ?? [];

        return match (self::method()) {
            'GET'                   => $_GET,
            'POST', 'PUT', 'DELETE' => $json,
        };
    }

    /**
     * Gets the current HTTP request method.
     *
     * @return string Uppercase HTTP method (e.g., 'GET', 'POST', etc.)
     */
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Gets the authorization header content.
     *
     * @return array|string The bearer token string if valid, or error array containing:
     *                      - ['error' => 'message'] if header is missing or malformed
     */
    public static function authorization(): array|string
    {
        $authorization = getallheaders();

        if (!isset($authorization['Authorization'])) {
            return ['error' => 'Sorry, no authorization header found.'];
        }

        $authorizationPartials = explode(' ', $authorization['Authorization']);

        if (count($authorizationPartials) != 2) {
            return ['error' => 'Please enter a valid authorization header.'];
        }

        return $authorizationPartials[1] ?? '';
    }
}