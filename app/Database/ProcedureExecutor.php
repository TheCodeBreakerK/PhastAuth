<?php

namespace App\Database;

use PDO;
use PDOException;

/**
 * Database stored procedure executor.
 *
 * Provides a standardized way to call database stored procedures
 * with parameter binding and result handling.
 */
class ProcedureExecutor
{
    /** @var DatabaseConnection Database connection instance */
    private DatabaseConnection $dbConnection;

    /**
     * Initializes the ProcedureExecutor with a database connection.
     *
     * @param DatabaseConnection $dbConnection Configured database connection
     */
    public function __construct(DatabaseConnection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Executes a stored procedure with the given parameters.
     *
     * @param string $procedureName Name of the stored procedure to execute
     * @param array|string $params Parameters to bind to the procedure (single string or array)
     * @return bool|array Returns:
     *                    - bool: True for successful execution with no results
     *                    - array: Result set for queries that return data
     *                    - false: When no rows are affected
     * @throws PDOException If database operation fails
     */
    public function execute(string $procedureName, array|string $params): bool|array
    {
        if (is_string($params)) {
            $params = [$params];
        }

        $placeholders = array_fill(0, count($params), "?");
        $query = "CALL $procedureName(" . implode(", ", $placeholders) . ")";

        try {
            $stmt = $this->dbConnection->connect()->prepare($query);

            foreach ($params as $i => $param) {
                $stmt->bindValue(++$i, $param);
            }

            $stmt->execute();

            if ($stmt->rowCount() < 1) {
                return false;
            }

            if ($stmt->columnCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }

            return true;

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}