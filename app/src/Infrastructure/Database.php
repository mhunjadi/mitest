<?php

namespace App\Infrastructure;

use PDO;
use PDOException;

class Database
{
    private $pdo;

    public function __construct($host = '127.0.0.1', $dbname = 'mitestdb', $username = 'root', $password = '')
    {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public function createTable()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS persons (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                age INT NOT NULL,
                location VARCHAR(200) NOT NULL,
                children INT NOT NULL,
                pets INT NOT NULL
            ) ENGINE=INNODB;
        ";

        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            echo 'Table creation failed: ' . $e->getMessage();
        }
    }

    public function insertRows(array $rows)
    {
        $sql = "
            INSERT INTO persons (name, age, location, children, pets) 
            VALUES (:name, :age, :location, :children, :pets)
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($rows as $row) {
                $stmt->execute([
                    ':name' => $row['name'],
                    ':age' => $row['age'],
                    ':location' => $row['location'],
                    ':children' => intval($row['children']),
                    ':pets' => intval($row['pets'])
                ]);
            }
        } catch (PDOException $e) {
            echo 'Insert failed: ' . $e->getMessage();
        }
    }

    public function deleteRows(array $criteria)
    {
        $conditions = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $sql = "DELETE FROM persons WHERE " . implode(' AND ', $conditions);

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            echo 'Delete failed: ' . $e->getMessage();
        }
    }

    public function dropTable()
    {
        $sql = "DROP TABLE IF EXISTS persons";

        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            echo 'Drop table failed: ' . $e->getMessage();
        }
    }

    public function importDataFromJsonl($filePath)
    {
        $data = [];
        $file = fopen($filePath, 'r');

        while ($line = fgets($file)) {
            $data[] = json_decode($line, true);
        }

        fclose($file);

        $this->insertRows($data);
    }
}
