<?php

namespace App\Api\Controllers;

use App\Api\Http\Request;
use App\Api\Http\Response;
use JsonException;

/**
 * Not Found Controller handles 404 responses for undefined routes.
 *
 * Returns standardized JSON responses for requests to non-existent endpoints.
 */
class NotFoundController
{
    /**
     * Handles all unmatched routes by returning a 404 response.
     *
     * @param Request $request The HTTP request object
     * @param Response $response The HTTP response object
     * @return void
     * @throws JsonException
     */
    public function index(Request $request, Response $response): void
    {
        $response::json([
            'success' => false,
            'error'   => [
                'status'  => 'NOT_FOUND',
                'code'    => 404,
                'message' => 'Route not found',
                'details' => [
                    'requested_method' => $request::method(),
                    'documentation'    => 'https://http.cat/status/' . 404
                ]
            ],
            'metadata' => [
                'timestamp' => time(),
                'endpoint'  => $request::path()
            ]
        ], 404);
    }
}