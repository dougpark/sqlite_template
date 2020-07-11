<?php
session_start();
ob_start('ob_gzhandler');
$executionStartTime = microtime(true);

include 'AccountInfoDb.php';
$db = new AccountInfoDb();

$action = (isset($_POST['action']) ? $_POST['action'] : null);

// uncomment to create the tables
//$action = 'create';

// take action
$result = "error: action not found";
switch ($action) {
    case 'create':
        $result = $db->createTables();
        break;
    case 'save':
        $result = $db->createTables();
        // get post data here
        $data = json_decode((isset($_POST['accountInfo']) ? $_POST['accountInfo'] : null));
        $result = save($data);
        break;
    case 'load':
        $result = $db->createTables();
        // get post data here
        $data = json_decode((isset($_POST['accountInfo']) ? $_POST['accountInfo'] : null));
        $result = load($data);
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

function save($data)
{
    global $db;

    // $data = new StdClass();

    // $data->key = $query->key;
    // $data->value = $query->value;

    $result = $db->checkKey($data);

    if ($result != '0') {
        $result = $db->update($data);
    } else {
        $result = $db->save($data);
    }

    return $result;
}

function load($query)
{
    global $db;

    $data = new StdClass();

    $data->key = $query->key;

    $result = $db->load($data);

    // if ok
    return $result;
}
