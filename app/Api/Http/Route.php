<?php

namespace App\Api\Http;

/**
 * Router class for defining and managing HTTP routes.
 *
 * Provides a fluent interface for registering routes and route groups,
 * supporting common HTTP methods (GET, POST, PUT, DELETE).
 */
class Route
{
    /** @var array Registered route definitions */
    private static array $routes = [];

    /** @var string Base URI prefix for grouped routes */
    private static string $uriBasePrefix = '';

    /**
     * Register a GET route.
     *
     * @param string $uri Route URI pattern
     * @param callable|array $action Route handler (callable or [Controller::class, 'method'])
     */
    public static function get(string $uri, callable|array $action): void
    {
        self::addRoute('GET', $uri, $action);
    }

    /**
     * Register a POST route.
     *
     * @param string $uri Route URI pattern
     * @param callable|array $action Route handler (callable or [Controller::class, 'method'])
     */
    public static function post(string $uri, callable|array $action): void
    {
        self::addRoute('POST', $uri, $action);
    }

    /**
     * Register a PUT route.
     *
     * @param string $uri Route URI pattern
     * @param callable|array $action Route handler (callable or [Controller::class, 'method'])
     */
    public static function put(string $uri, callable|array $action): void
    {
        self::addRoute('PUT', $uri, $action);
    }

    /**
     * Register a DELETE route.
     *
     * @param string $uri Route URI pattern
     * @param callable|array $action Route handler (callable or [Controller::class, 'method'])
     */
    public static function delete(string $uri, callable|array $action): void
    {
        self::addRoute('DELETE', $uri, $action);
    }

    /**
     * Create a route group with a common prefix.
     *
     * @param string $uriPrefix Common URI prefix for all routes in the group
     * @param callable $callback Closure containing route definitions
     */
    public static function group(string $uriPrefix, callable $callback): void
    {
        $previousPrefix = self::$uriBasePrefix;
        self::$uriBasePrefix .= $uriPrefix;

        $callback();

        self::$uriBasePrefix = $previousPrefix;
    }

    /**
     * Add a route to the collection.
     *
     * @param string $method HTTP method
     * @param string $uri Route URI pattern
     * @param callable|array $action Route handler
     */
    private static function addRoute(string $method, string $uri, callable|array $action): void
    {
        self::$routes[] = [
            'method' => $method,
            'uri' => self::$uriBasePrefix . $uri,
            'action' => $action
        ];
    }

    /**
     * Get all registered routes.
     *
     * @return array All registered route definitions
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }
}