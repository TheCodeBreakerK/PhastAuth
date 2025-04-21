<?php

namespace App\Domain\Services;

use App\Api\Http\JWT;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Utils\Validator;
use Exception;
use http\Exception\InvalidArgumentException;
use PDOException;

/**
 * User service handling business logic for user operations.
 *
 * Implements UserRepository interface and provides:
 * - User registration
 * - Authentication
 * - Token management
 * - User data operations
 */
class UserService implements UserRepository
{
    /**
     * Creates a new user account.
     *
     * @param array $data User data containing name, email, and password
     * @return array Result array with success/error message
     */
    public function create(array $data): array
    {
        try {
            $fields = Validator::validate([
                'name'     => $data['name']     ?? '',
                'email'    => $data['email']    ?? '',
                'password' => $data['password'] ?? ''
            ]);

            $user = (new User())->save($fields);

            if (!$user) {
                return ['error' => 'Account creation failed. Please try again.'];
            }

            return ['success' => 'Account created successfully'];

        } catch (PDOException|InvalidArgumentException|Exception $e) {
            return ['error' => 'Registration error: ' . $e->getMessage()];
        }
    }

    /**
     * Authenticates a user and generates JWT token.
     *
     * @param array $data Login credentials (email and password)
     * @return array Result array with success/error message and token if successful
     */
    public function auth(array $data): array
    {
        try {
            $fields = Validator::validate([
                'email'    => $data['email']    ?? '',
                'password' => $data['password'] ?? ''
            ]);

            $user = (new User())->authentication($fields);

            if (!$user) {
                return ['error' => 'Invalid credentials. Please check your email and password.'];
            }

            return [
                'success' => 'Login successful',
                'token' => JWT::generateToken($user)
            ];

        } catch (PDOException|InvalidArgumentException|Exception $e) {
            return ['error' => 'Authentication failed: ' . $e->getMessage()];
        }
    }

    /**
     * Refreshes an expired JWT token.
     *
     * @param mixed $authorization Either token string or error array
     * @return array Result array with new token or error message
     */
    public function refresh(mixed $authorization): array
    {
        try {
            if (is_array($authorization)) {
                return ['error' => 'Authorization error: ' . $authorization['error']];
            }

            $newToken = JWT::refreshToken($authorization);

            if (!$newToken) {
                return ['error' => 'Token refresh failed. Please login again.'];
            }

            return [
                'success' => 'Token refreshed successfully',
                'token' => $newToken
            ];

        } catch (PDOException|InvalidArgumentException|Exception $e) {
            return ['error' => 'Token refresh error: ' . $e->getMessage()];
        }
    }

    /**
     * Fetches user data using JWT token.
     *
     * @param mixed $authorization Either token string or error array
     * @return array Result array with user data or error message
     */
    public function fetch(mixed $authorization): array
    {
        try {
            if (is_array($authorization)) {
                return ['error' => 'Authorization error: ' . $authorization['error']];
            }

            $dataToken = JWT::verifyToken($authorization);

            if (!$dataToken) {
                return ['error' => 'Session expired. Please login again.'];
            }

            $user = (new User())->find($dataToken['id']);

            if (!$user) {
                return ['error' => 'User account not found.'];
            }

            return [
                'success' => 'User data retrieved successfully',
                'data'    => $user
            ];

        } catch (PDOException|InvalidArgumentException|Exception $e) {
            return ['error' => 'Data fetch error: ' . $e->getMessage()];
        }
    }

    /**
     * Updates user account information.
     *
     * @param mixed $authorization Either token string or error array
     * @param array $data New user data
     * @return array Result array with success/error message
     */
    public function update(mixed $authorization, array $data): array
    {
        try {
            if (is_array($authorization)) {
                return ['error' => 'Authorization error: ' . $authorization['error']];
            }

            $dataToken = JWT::verifyToken($authorization);

            if (!$dataToken) {
                return ['error' => 'Session expired. Please login again.'];
            }

            $fields = Validator::validate([
                'name'     => $data['name']     ?? '',
                'email'    => $data['email']    ?? '',
                'password' => $data['password'] ?? ''
            ]);

            $user = (new User())->update($dataToken['id'], $fields);

            if (!$user) {
                return ['error' => 'Account update failed. Please try again.'];
            }

            return ['success' => 'Account updated successfully'];

        } catch (PDOException|InvalidArgumentException|Exception $e) {
            return ['error' => 'Update error: ' . $e->getMessage()];
        }
    }

    /**
     * Deletes user account.
     *
     * @param mixed $authorization Either token string or error array
     * @return array Result array with success/error message
     */
    public function delete(mixed $authorization): array
    {
        try {
            if (is_array($authorization)) {
                return ['error' => 'Authorization error: ' . $authorization['error']];
            }

            $dataToken = JWT::verifyToken($authorization);

            if (!$dataToken) {
                return ['error' => 'Session expired. Please login again.'];
            }

            $user = (new User())->delete($dataToken['id']);

            if (!$user) {
                return ['error' => 'Account deletion failed. Please try again.'];
            }

            return ['success' => 'Account deleted successfully'];

        } catch (PDOException|InvalidArgumentException|Exception $e) {
            return ['error' => 'Deletion error: ' . $e->getMessage()];
        }
    }
}