<?php

namespace App\Infrastructure;

use PDO;
use PDOException;

class Seeder extends Database
{
    use Filter;

    function __construct($host = '127.0.0.1', $dbname = 'mitestdb', $username = 'root', $password = '')
    {
        parent::__construct($host, $dbname, $username, $password);
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    private $lastInsertIds = [];

    public function importData($filePath, FieldMap $fieldMap, array $filters = [])
    {
        $data = $this->loadData($filePath);

        $mappedData = [];
        foreach ($data as $row) {
            $mappedData[] = $fieldMap->apply($row);
        }

        $data = $mappedData;

        foreach ($filters as $filter => $params) {
            switch ($filter) {
                case 'age':
                    $data = $this->filterByAge($data, $params[0], $params[1]);
                    break;
                case 'location':
                    $data = $this->filterByLocation($data, $params[0]);
                    break;
                case 'children_and_pets':
                    $data = $this->filterByChildrenAndPets($data);
                    break;
            }
        }

        $this->lastInsertIds = $this->insertRows($data);
    }

    public function rollbackLastImport()
    {
        if (empty($this->lastInsertIds)) {
            return;
        }

        $ids = implode(',', $this->lastInsertIds);
        $sql = "DELETE FROM persons WHERE id IN ($ids)";

        try {
            $this->pdo->exec($sql);
            $this->lastInsertIds = [];
        } catch (PDOException $e) {
            echo 'Rollback failed: ' . $e->getMessage();
        }
    }

    public function insertRows(array $rows): array
    {
        $sql = "
            INSERT INTO persons (name, age, location, children, pets) 
            VALUES (:name, :age, :location, :children, :pets)
        ";

        $insertIds = [];

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($rows as $row) {
                $stmt->execute([
                    ':name' => $row['name'],
                    ':age' => $row['age'],
                    ':location' => $row['location'],
                    ':children' => json_encode($row['children']),
                    ':pets' => json_encode($row['pets'])
                ]);
                $insertIds[] = $this->pdo->lastInsertId();
            }
        } catch (PDOException $e) {
            echo 'Insert failed: ' . $e->getMessage();
        }

        return $insertIds;
    }

    private function loadData(string $filePath): array
    {
        $data = [];
        $file = fopen($filePath, 'r');

        while ($line = fgets($file)) {
            $data[] = json_decode($line, true);
        }

        fclose($file);
        return $data;
    }
}
