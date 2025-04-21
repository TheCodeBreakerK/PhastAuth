<?php

namespace App\Domain\Entities;

use App\Database\DatabaseConnection;
use App\Database\ProcedureExecutor;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Name;
use App\Domain\ValueObjects\Password;

/**
 * User entity representing a system user.
 *
 * Handles all user-related operations including creation, authentication,
 * retrieval, updating, and deletion of user records.
 */
class User
{
    /** @var DatabaseConnection Database connection instance */
    private DatabaseConnection $database;

    /** @var ProcedureExecutor Stored procedure executor instance */
    private ProcedureExecutor $procedure;

    /**
     * Initializes a new User instance with database connection.
     */
    public function __construct()
    {
        $config = require_once __DIR__ . '/../../../config/database.php';
        $this->database = new DatabaseConnection(...array_values($config));
        $this->procedure = new ProcedureExecutor($this->database);
    }

    /**
     * Creates a new user record.
     *
     * @param array $data User data containing 'name', 'email', and 'password'
     * @return bool True if creation was successful, false otherwise
     */
    public function save(array $data): bool
    {
        $result = $this->procedure->execute(
            procedureName: 'sp_create_user',
            params: [
                (new Name($data['name']))->getValue(),
                (new Email($data['email']))->getValue(),
                (new Password($data['password']))->getValue()
            ]
        );

        $this->database->disconnect();

        return $result;
    }

    /**
     * Authenticates a user.
     *
     * @param array $data Authentication data containing 'email' and 'password'
     * @return array|bool User ID array if authentication succeeds, false otherwise
     */
    public function authentication(array $data): bool|array
    {
        $passwordHash = $this->procedure->execute(
            procedureName: 'sp_get_password_by_email',
            params: (new Email($data['email']))->getValue()
        );

        if (!$passwordHash) {
            return false;
        }

        if (!password_verify($data['password'], $passwordHash['txt_password_hash'])) {
            return false;
        }

        $userId = $this->procedure->execute(
            procedureName: 'sp_validate_user_login',
            params: [
                (new Email($data['email']))->getValue(),
                $passwordHash['txt_password_hash']
            ]
        );

        $this->database->disconnect();

        return ['id' => $userId['pk_user']];
    }

    /**
     * Finds a user by their ID.
     *
     * @param int|string $id User ID to search for
     * @return array User data including id, name, email, and timestamps
     */
    public function find(int|string $id): array
    {
        $user = $this->procedure->execute(
            procedureName: 'sp_get_user_by_id',
            params: $id
        );

        $this->database->disconnect();

        return [
            'id'         => $user['pk_user'],
            'name'       => $user['txt_name'],
            'email'      => $user['txt_email'],
            'created_at' => $user['dat_created_at'],
            'updated_at' => $user['dat_updated_at']
        ];
    }

    /**
     * Updates an existing user record.
     *
     * @param int|string $id User ID to update
     * @param array $data New user data containing 'name', 'email', and 'password'
     * @return bool True if update was successful, false otherwise
     */
    public function update(int|string $id, array $data): bool
    {
        $result = $this->procedure->execute(
            procedureName: 'sp_update_user',
            params: [
                $id,
                (new Name($data['name']))->getValue(),
                (new Email($data['email']))->getValue(),
                (new Password($data['password']))->getValue()
            ]
        );

        $this->database->disconnect();

        return $result;
    }

    /**
     * Deletes a user record.
     *
     * @param int|string $id User ID to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(int|string $id): bool
    {
        $result = $this->procedure->execute(
            procedureName: 'sp_delete_user',
            params: $id
        );

        $this->database->disconnect();

        return $result;
    }
}