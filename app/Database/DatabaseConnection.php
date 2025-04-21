<?php

namespace App\Database;

use PDO;
use PDOException;

/**
 * Database connection handler using PDO.
 *
 * Manages database connections with connection pooling and provides
 * standardized configuration for PDO instances.
 */
class DatabaseConnection
{
    /** @var PDO|null The PDO connection instance */
    private ?PDO $pdo = null;

    /** @var string The constructed DSN connection string */
    private string $dns;

    /**
     * Initializes a new database connection instance.
     *
     * @param string $driver Database driver (e.g., mysql, pgsql)
     * @param string $host Database server host
     * @param string $port Database server port
     * @param string $database Database name
     * @param string $username Database username
     * @param string $password Database password
     * @param string $charset Connection charset (default: utf8mb4)
     * @param array $options Additional PDO options
     */
    public function __construct(
        private readonly string $driver,
        private readonly string $host,
        private readonly string $port,
        private readonly string $database,
        private readonly string $username,
        private readonly string $password,
        private readonly string $charset = 'utf8mb4',
        private array           $options = []
    )
    {
        $this->dns =      $this->driver .
            ':host=' .    $this->host .
            ';port=' .    $this->port .
            ';dbname=' .  $this->database .
            ';charset=' . $this->charset;

        $this->options = array_merge([
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ], $this->options);
    }

    /**
     * Establishes a database connection if none exists and returns the PDO instance.
     *
     * @return PDO The active PDO connection instance
     * @throws PDOException If connection fails
     */
    public function connect(): PDO
    {
        if ($this->pdo === null) {
            try {
                $this->pdo = new PDO(
                    $this->dns,
                    $this->username,
                    $this->password,
                    $this->options
                );
            } catch (PDOException $e) {
                throw new PDOException(
                    'Connection failed: ' . $e->getMessage(),
                    (int)$e->getCode()
                );
            }
        }

        return $this->pdo;
    }

    /**
     * Closes the current database connection.
     */
    public function disconnect(): void
    {
        $this->pdo = null;
    }
}