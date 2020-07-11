<?php

class AccountInfoDb
{

    // folder db must exist and be writeable
    private $dbLoc = "db/accountInfo.sq3";
    private $game, $tables, $settings;
    private $pdo;

    public function __construct()
    {
        //$uuid = UUID::v4();

        $this->game = new StdClass();
        $this->settings = new stdClass();
        $this->tables = new stdClass();

        $this->tables->data = 'data';

        $dsn = "sqlite:{$this->dbLoc}";

        try {
            $this->pdo = new \PDO($dsn);
            // Set some default database attributes
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    public function createTables()
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tables->data} (
                    key TEXT NOT NULL UNIQUE, 
                    value TEXT)";

        $data = [];

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($data);

        return $result;
    }

    function save($query)
    {
        $sql = "INSERT INTO {$this->tables->data}
                (key, value)
                VALUES (:key, :value)";

        $data = [
            'key' => $query->key,
            'value' => json_encode($query->value)
        ];

        // error_log(json_encode($sql));
        // error_log(json_encode($data));

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $lastInsertId = $this->pdo->lastInsertId();

        return $lastInsertId;
    }

    public function update($query)
    {
        $sql = "UPDATE {$this->tables->data}
			    SET value = :value
                WHERE key = :key";

        $data = [
            'key' => $query->key,
            'value' => json_encode($query->value),
        ];

        // error_log(json_encode($sql));
        // error_log(json_encode($data));

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }



    public function load($data)
    {

        $sql = "SELECT *
			        FROM {$this->tables->data}
                    WHERE key = :key
                    LIMIT 1";

        $data = [
            'key' => $data->key
        ];

        // error_log(json_encode($sql));
        // error_log(json_encode($data));

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $result = $stmt->fetchAll();
        return $result;
    }

    public function checkKey($data)
    {
        $sql = "SELECT count(*)
			        FROM {$this->tables->data}
                    WHERE key = :key";

        $data = [
            'key' => $data->key
        ];

        // error_log(json_encode($sql));
        // error_log(json_encode($data));

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        $result = $stmt->fetchColumn();
        // if ($result == '0') {
        //     $result = '';
        // }
        return $result;
    }
}
