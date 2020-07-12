<?php
session_start();
ob_start('ob_gzhandler');
$executionStartTime = microtime(true);

include 'Db.php';
$db = new Db();

$action = (isset($_POST['action']) ? $_POST['action'] : null);

// take action
$result = "error: action not found";
switch ($action) {
    case 'createTables':
        $result = $db->createTables();
        break;
    case 'insertWithTransaction':
        $rowCount = (isset($_POST['rowCount']) ? $_POST['rowCount'] : null);
        $result = $db->insertWithTransaction($rowCount);
        break;
    case 'insertNoTransaction':
        $rowCount = (isset($_POST['rowCount']) ? $_POST['rowCount'] : null);
        $result = $db->insertNoTransaction($rowCount);
        break;
    case 'tableInfo':
        $result = $db->tableInfo();
        break;
    case 'version':
        $result = $db->version();
        break;

    default:
        //$result = $db->hello();
}

$executionEndTime = microtime(true);
$seconds = $executionEndTime - $executionStartTime;
$execTime = "Execution time= {$seconds} seconds.";
//error_log(PHP_EOL . $execTime);

$data = array(
    "result" => $result,
    "action" => $action,
    "msg" => "no msg",
    'executionTimeSeconds' => $seconds,
);
// all done return results to page
echo json_encode($data);
