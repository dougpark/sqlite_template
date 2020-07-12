<?php
// include 'Game.php';
include './lib/UUID.php';

class Db
{

    // folder db must exist and be writeable
    private $dbLoc = "db/sample.sq3";
    private $game, $tables, $settings;
    private $pdo;

    public function __construct()
    {
        //$uuid = UUID::v4();

        $this->game = new StdClass();
        $this->settings = new stdClass();
        $this->tables = new stdClass();

        $this->tables->sample = 'sample';
        $this->tables->user = 'user';

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
        $sql = "CREATE TABLE IF NOT EXISTS sample (
                    id INTEGER PRIMARY KEY, 
                    title TEXT, 
                    message TEXT, 
                    time INTEGER)";

        $data = [];

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($data);

        $sql = "CREATE TABLE IF NOT EXISTS user ( 
                    userid TEXT, 
                    name TEXT, 
                    email TEXT,
                    phone TEXT)";

        $data = [];

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($data);

        return $result;
    }

    public function version()
    {
        $sql = "SELECT sqlite_version();";

        $data = [];

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute($data);
        $result = $stmt->fetchAll();
        return $result;
    }

    // ways to get info about database and tables
    public function tableinfo()
    {
        //$sql = "pragma table_info(sample);";
        //$sql = "pragma table_info(user);";
        //$sql = "SELECT * FROM sqlite_master WHERE type = 'table'";
        $sql = "SELECT
  m.name AS table_name, 
  p.cid AS col_id,
  p.name AS col_name,
  p.type AS col_type,
  p.pk AS col_is_pk,
  p.dflt_value AS col_default_val,
  p.[notnull] AS col_is_not_null
FROM sqlite_master m
LEFT OUTER JOIN pragma_table_info((m.name)) p
  ON m.name <> p.name
WHERE m.type = 'table'
ORDER BY table_name, col_id";

        $data = [];

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute($data);
        $result = $stmt->fetchAll();
        return $result;
    }

    public function insertWithTransaction($rowCount)
    {

        //global $pdo;

        $this->pdo->beginTransaction();
        $start = 1;
        $end = $start + $rowCount;
        $lastInsertId = '';
        for ($id = $start; $id <= $end; $id++) {

            $lastInsertId = $this->insert($id, 'hi world');
        }
        $this->pdo->commit();
        return $lastInsertId;
    }

    public function insertNoTransaction($rowCount)
    {

        //global $pdo;

        //$this->pdo->beginTransaction();
        $start = 1;
        $end = $start + $rowCount;
        $lastInsertId = '';
        for ($id = $start; $id <= $end; $id++) {

            $lastInsertId = $this->insert($id, 'hi world');
        }
        //$this->pdo->commit();
        return $lastInsertId;
    }

    function insert($id, $title)
    {

        $table = "sample";

        $sql = "INSERT INTO {$table}
                (title, message, time)
                VALUES (:title, :message, :time)";

        $data = [
            'title' => $title,
            'message' => 'default message',
            'time' => 10
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $lastInsertId = $this->pdo->lastInsertId();

        return $lastInsertId;
    }
}
