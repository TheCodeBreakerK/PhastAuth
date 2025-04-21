<?php

namespace App\Api\Controllers;

use App\Api\Http\Request;
use App\Api\Http\Response;
use JsonException;

/**
 * Home Controller handles the main entry point of the API.
 *
 * Responsible for welcoming requests and providing basic API information.
 */
class HomeController
{
    /**
     * Handles the welcome endpoint request.
     *
     * @param Request $request HTTP request object
     * @param Response $response HTTP response object
     * @return void
     * @throws JsonException
     */
    public function index(Request $request, Response $response): void
    {
        $response::json([
            'success' => [
                'status'      => 'OK',
                'code'        => 200,
                'message'     => 'Welcome to PhastAuth REST API',
                'description' => 'Simple and secure authentication service API',
                'version'     => '1.0.0',
                'details' => [
                    'requested_method' => Request::method(),
                    'documentation'    => 'https://http.cat/status/200'
                ]
            ],
            'error'    => false,
            'metadata' => [
                'timestamp' => time(),
                'endpoint'  => $request::path(),
            ]
        ]);
    }
}