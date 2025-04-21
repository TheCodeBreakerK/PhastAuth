<?php

namespace App\Api\Controllers;

use App\Api\Http\Request;
use App\Api\Http\Response;
use App\Api\Http\ResponseFormatter;
use App\Domain\Services\UserService;
use JsonException;

/**
 * User Controller handles HTTP requests for user operations.
 *
 * Responsible for processing user-related API endpoints including:
 * - Registration
 * - Authentication
 * - Token refresh
 * - User data operations
 */
class UserController
{
    /** @var UserService Service handling user business logic */
    private UserService $userService;

    /**
     * Initializes the controller with required services.
     */
    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Handles user registration requests.
     *
     * @param Request $request HTTP request object
     * @param Response $response HTTP response object
     * @throws JsonException
     */
    public function store(Request $request, Response $response): void
    {
        $body = $request::body();
        $result = $this->userService->create($body);

        if (isset($result['error'])) {
            $response::json(
                data: ResponseFormatter::formatError($request, $result['error'], 400),
                status: 400
            );
            return;
        }

        $response::json(
            data: ResponseFormatter::formatSuccess($request, $result['success'], 201),
            status: 201
        );
    }

    /**
     * Handles user authentication requests.
     *
     * @param Request $request HTTP request object
     * @param Response $response HTTP response object
     * @throws JsonException
     */
    public function login(Request $request, Response $response): void
    {
        $body = $request::body();
        $result = $this->userService->auth($body);

        if (isset($result['error'])) {
            $response::json(
                data: ResponseFormatter::formatError($request, $result['error'], 401),
                status: 401
            );
            return;
        }

        $response::json(
            ResponseFormatter::formatSuccess(
                request: $request,
                message: $result['success'],
                data: ['token' => $result['token']]
            )
        );
    }

    /**
     * Handles token refresh requests.
     *
     * @param Request $request HTTP request object
     * @param Response $response HTTP response object
     * @throws JsonException
     */
    public function refresh(Request $request, Response $response): void
    {
        $authorization = $request::authorization();
        $result = $this->userService->refresh($authorization);

        if (isset($result['error'])) {
            $response::json(
                data: ResponseFormatter::formatError($request, $result['error'], 401),
                status: 401
            );
            return;
        }

        $response::json(
            ResponseFormatter::formatSuccess(
                request: $request,
                message: $result['success'],
                data: ['token' => $result['token']]
            )
        );
    }

    /**
     * Handles user data retrieval requests.
     *
     * @param Request $request HTTP request object
     * @param Response $response HTTP response object
     * @throws JsonException
     */
    public function fetch(Request $request, Response $response): void
    {
        $authorization = $request::authorization();
        $result = $this->userService->fetch($authorization);

        if (isset($result['error'])) {
            $response::json(
                data: ResponseFormatter::formatError($request, $result['error'], 400),
                status: 400
            );
            return;
        }

        $response::json(
            ResponseFormatter::formatSuccess(
                request: $request,
                message: $result['success'],
                data: $result['data']
            )
        );
    }

    /**
     * Handles user profile update requests.
     *
     * @param Request $request HTTP request object
     * @param Response $response HTTP response object
     * @throws JsonException
     */
    public function update(Request $request, Response $response): void
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $result = $this->userService->update($authorization, $body);

        if (isset($result['error'])) {
            $response::json(
                data: ResponseFormatter::formatError($request, $result['error'], 400),
                status: 400
            );
            return;
        }

        $response::json(
            ResponseFormatter::formatSuccess(
                request: $request,
                message: $result['success']
            )
        );
    }

    /**
     * Handles user account deletion requests.
     *
     * @param Request $request HTTP request object
     * @param Response $response HTTP response object
     * @throws JsonException
     */
    public function delete(Request $request, Response $response): void
    {
        $authorization = $request::authorization();
        $result = $this->userService->delete($authorization);

        if (isset($result['error'])) {
            $response::json(
                data: ResponseFormatter::formatError($request, $result['error'], 400),
                status: 400
            );
            return;
        }

        $response::json(
            ResponseFormatter::formatSuccess(
                request: $request,
                message: $result['success']
            )
        );
    }
}