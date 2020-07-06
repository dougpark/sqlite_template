<?php
$executionStartTime = microtime(true);

$dsn = "sqlite:db/viewer.sq3";
$options = [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new \PDO($dsn);
    // Set errormode to exceptions
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

$pdo->exec("CREATE TABLE IF NOT EXISTS messages (
                    id INTEGER PRIMARY KEY, 
                    title TEXT, 
                    message TEXT, 
                    time INTEGER)");

$pdo->beginTransaction();
$start = 1;
$end = $start + 100;
for ($id = $start; $id <= $end; $id++) {

    insertTest($id, 'hi world');
}
$pdo->commit();


function insertTest($id, $title)
{
    global $pdo;

    // echo $title;
    // var_dump($title);

    $msgTable = "messages";

    $sql = "INSERT INTO {$msgTable}
                (title, message, time)
                VALUES (:title, :message, :times)";

    $data = [

        'title' => $title,
        'message' => 'msg',
        'times' => 10
    ];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    $lastInsertId = $pdo->lastInsertId();

    //echo 'lastInsertId= ' . $lastInsertId;
    //var_dump($lastInsertId);
    return $lastInsertId;
}

$executionEndTime = microtime(true);
$seconds = $executionEndTime - $executionStartTime;
error_log(PHP_EOL . "This script took $seconds to execute.");
