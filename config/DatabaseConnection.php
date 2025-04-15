<?php

namespace Config;

use PDO;
use PDOException;

class DatabaseConnection
{
    private ?PDO $pdo = null;
    private string $dns;

    public function __construct(
        private readonly string $host,
        private readonly string $port,
        private readonly string $database,
        private readonly string $username,
        private readonly string $password,
        private array           $options = []
    ) {
        $this->dns = 'mysql:host=' . $this->host .
                     ';port=' .      $this->port .
                     ';dbname=' .    $this->database .
                     ';charset=utf8';
        $this->options = array_merge([
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ], $this->options);
    }

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

    public function disconnect(): void
    {
        $this->pdo = null;
    }
}
