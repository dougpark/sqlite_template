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
    case 'getGame':
        $userName = (isset($_POST['userName']) ? $_POST['userName'] : null);
        $roomName = (isset($_POST['roomName']) ? $_POST['roomName'] : null);
        $gameToken = (isset($_POST['gameToken']) ? $_POST['gameToken'] : null);
        $result = $db->getGame($userName, $roomName, $gameToken);
        break;
    case 'sendGame':
        $userName = (isset($_POST['userName']) ? $_POST['userName'] : null);
        $roomName = (isset($_POST['roomName']) ? $_POST['roomName'] : null);
        $gameToken = (isset($_POST['gameToken']) ? $_POST['gameToken'] : null);
        $gameData = json_decode((isset($_POST['gameData']) ? $_POST['gameData'] : null));
        $result = $db->receiveGame($userName, $roomName, $gameToken, $gameData);
        break;
    case 'sendMessage':
        $userName = (isset($_POST['userName']) ? $_POST['userName'] : null);
        $roomName = (isset($_POST['roomName']) ? $_POST['roomName'] : null);
        $gameToken = (isset($_POST['gameToken']) ? $_POST['gameToken'] : null);
        $message = json_decode((isset($_POST['message']) ? $_POST['message'] : null));
        $result = $db->receiveMessage($userName, $roomName, $gameToken, $message);
        break;
    case 'getMessage':
        $userName = (isset($_POST['userName']) ? $_POST['userName'] : null);
        $roomName = (isset($_POST['roomName']) ? $_POST['roomName'] : null);
        $gameToken = (isset($_POST['gameToken']) ? $_POST['gameToken'] : null);
        $result = $db->sendMessage($userName, $roomName, $gameToken);
        break;
    case 'lastMessageTime':
        $userName = (isset($_POST['userName']) ? $_POST['userName'] : null);
        $roomName = (isset($_POST['roomName']) ? $_POST['roomName'] : null);
        $gameToken = (isset($_POST['gameToken']) ? $_POST['gameToken'] : null);
        $result = $db->lastMessageTime($userName, $roomName, $gameToken);
        break;
    case 'getTagCloud':
        // $result = getTagCloud();
        break;
    case 'hello':
        $result = $db->hello();
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
