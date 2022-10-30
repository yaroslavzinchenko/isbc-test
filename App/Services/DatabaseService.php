<?php

namespace App\Services;

use PDO;
use PDOException;

class DatabaseService
{
    private PDO $pdo;

    public function __construct(string $host, string $user, string $password, string $database)
    {
        $dsn = 'mysql:host='. $host .';dbname='. $database;
        $this->pdo =  new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS , true);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function executeRawQuery($query, array $parameters, string $type)
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($parameters);
        } catch (PDOException $exception) {
            // Логирование.
        }

        if ($type == 'select') {
            return $stmt->fetchAll();
        }
        if ($type == 'update') {
            // Считаем количество затронутых строк.
            return $stmt->rowCount();
        }
        if ($type == 'insert') {
            return $stmt;
        }
    }

    public function getData(string $columns, string $table, $where, array $parameters): array
    {
        /*$sql = "SELECT $columns FROM $table WHERE $where";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parameters);
        return $stmt->fetchAll();*/
    }

    public function updateData()
    {

    }

    public function insertData()
    {

    }
}