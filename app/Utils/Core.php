<?php

namespace App\Utils;

use App\Api\Controllers\NotFoundController;
use App\Api\Http\Request;
use App\Api\Http\Response;
use App\Api\Http\ResponseFormatter;
use JsonException;

/**
 * Core application router that handles request dispatching.
 *
 * Matches incoming requests against registered routes and dispatches to controllers.
 * Supports dynamic route parameters and HTTP method validation.
 */
class Core
{
    /**
     * Dispatches the request to the appropriate controller action.
     *
     * @param array $routes Array of route configurations with:
     *                      - uri: The route pattern (may contain {id} placeholders)
     *                      - method: HTTP method (GET, POST, etc.)
     *                      - action: Array [ControllerClass, 'methodName']
     * @return void
     * @throws JsonException
     */
    public static function dispatch(array $routes): void
    {
        $url = '/';

        isset($_GET['url']) && $url .= $_GET['url'];

        $url !== '/' && $url = rtrim($url, '/');

        $isRouteFound  = false;

        foreach ($routes as $route) {
            $pattern = '#^' . preg_replace('/{id}/', '([\w-]+)', $route['uri']) . '$#';

            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches);

                if ($route['method'] !== Request::method()) {
                    Response::json([
                        'success' => false,
                        'error' => [
                            'status' => 'METHOD_NOT_ALLOWED',
                            'code' => 405,
                            'message' => 'method not allowed',
                            'details' => [
                                'requested_method' => Request::method(),
                                'allowed_method' => $route['method'],
                                'documentation' => 'https://http.cat/status/' . 405
                            ]
                        ],
                        'metadata' => [
                            'timestamp' => time(),
                            'endpoint' => Request::path()
                        ]
                    ], 405);
                    return;
                }

                $isRouteFound = true;

                [$controller, $action] = $route['action'];

                $controller = new $controller();
                $controller->$action(new Request(), new Response(), $matches);
            }
        }

        if (!$isRouteFound) {
            $controller = (new NotFoundController());
            $controller->index(new Request(), new Response());
        }
    }
}