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
















    public function newGame($userName, $roomName)
    {
        $this->game = new Game();

        // xxx for debug test only
        $this->deleteAll();

        // check db for userName, if avail then use
        $userExists = $this->checkUser($userName);
        $roomExists = $this->checkRoom($roomName);

        if ($userExists || $roomExists) {
            // error user or room exists
            $this->game->result = -1;
            $this->game->userExists = $userExists;
            $this->game->roomExists = $roomExists;
            $this->game->error = 'userName or roomName already exists.';
        } else {
            $userToken = UUID::v4();
            $roomToken = UUID::v4();
            $gameToken = UUID::v4();
            $this->createUser($userName, $userToken, $roomName, $roomToken);
            $this->createRoom($roomName, $roomToken, $userName, $userToken, $gameToken);
            $this->game->hostName = $userName;
            $this->game->hostToken = $userToken;
            $this->game->roomName = $roomName;
            $this->game->roomToken = $roomToken;
            $this->game->gameToken = $gameToken;
            // if all ok then result =1
            $this->game->result = 1;

            // save game here before returning to client
            $this->createGame();
            $this->game->status = 'open';
            $this->setGameStatus($gameToken, 'open');
            $this->saveGame($this->game->gameToken, $this->game);
        }
        return $this->game;
    }

    public function joinGame($userName, $roomName)
    { // checkUser if exist - error if yes
        // checkRoom if exists - error if no
        // get roomToken
        // new userToken
        // createUser userName, userToken, roomName, roomToken
        // add player to $this->game

        // check db for userName, if avail then use
        $userExists = $this->checkUser($userName);
        $roomExists = $this->checkRoom($roomName);

        $roomToken = $this->getRoomToken($roomName);
        $gameToken = $this->getGameTokenFromRoom($roomToken);
        $gameStatus = $this->getGameStatus($gameToken);

        // if ($userExists || !$roomExists) { // todo remove ! from userExists
        // error user or room exists
        if (false) {
            $this->game->result = -1;
            $this->game->userExists = $userExists;
            $this->game->roomExists = $roomExists;
            $this->game->error = 'userName already exists or roomName doesnt exists.';
        } else {

            if ($gameStatus == 'open') {
                $userToken = UUID::v4();
                $this->createUser($userName, $userToken, $roomName, $roomToken);

                $this->game = $this->loadGame($gameToken);

                $this->game->result = 1;
            } else {
                $this->game->result = -1;
                $this->game->error = 'game not open';
            }
        }

        return $this->game;
    }

    public function getGame($userName, $roomName, $gameToken)
    {

        //load game
        $this->game = $this->loadGame($gameToken);

        return $this->game;
    }

    public function receiveGame($userName, $roomName, $gameToken, $gameData)
    {

        $this->saveGame($gameToken, $gameData);
    }

    public function receiveMessage($userName, $roomName, $gameToken, $message)
    {
        $action = $message->type;
        switch ($action) {
            case 'fire':
                // save message to database
                // id, timestamp, gameToken, message
                //error_log('message: fire saveMessage');
                $this->saveMessage($gameToken, $message);

                break;
            default:
        }
    }

    public function sendMessage($userName, $roomName, $gameToken)
    {

        // load messages from database by gameToken
        // id, timestamp, gameToken, message
        // set message->time to last timestamp
        $message = new StdClass();
        $msg = $this->loadMessage($gameToken);

        if ($msg) {
            $prevTime = 0;
            foreach ($msg as $row) {
                $timestamp = $row['timestamp'];
                //error_log("is_typing= $is_typing");
                if ($timestamp > $prevTime) {
                    $prevTime = $timestamp;
                }
            }
            $message->time = $prevTime;
            $message->msg = $msg;
        }

        $message->userName = $userName;
        $message->roomName = $roomName;
        $message->gameToken = $gameToken;
        if (!$msg) {
            $message->time = 0;
            //error_log('message not found, sending time=0');
        }


        return $message;
    }

    public function lastMessageTime($userName, $roomName, $gameToken)
    {

        // load messages from database by gameToken
        // id, timestamp, gameToken, message
        // set message->time to last timestamp
        $message = new StdClass();
        $time = $this->loadLastMessageTime($gameToken);
        $message->time = $time;
        $message->gameToken = $gameToken;

        return $message;
    }

    /********************* Private Db Functions *************** */

    private function saveMessage($gameToken, $messageData)
    {

        $messageData = json_encode($messageData);
        $sql = "INSERT INTO {$this->tables->messageTable}
                (gameToken, messageData)
                VALUES (:gameToken, :messageData)";

        $data = [
            'gameToken' => $gameToken,
            'messageData' => $messageData,
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $messageId = $this->pdo->lastInsertId();

        return $messageId;
    }

    private function loadMessage($gameToken)
    {

        $sql = "SELECT *
			        FROM {$this->tables->messageTable}
                    WHERE gameToken = :gameToken";
        $data = [
            'gameToken' => $gameToken,
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $result = $stmt->fetchAll(); // should be php object

        return $result;
    }

    private function loadLastMessageTime($gameToken)
    {
        $sql = "SELECT timestamp
			        FROM {$this->tables->messageTable}
                    WHERE gameToken = :gameToken
                    ORDER BY timestamp DESC LIMIT 1";
        $data = [
            'gameToken' => $gameToken,
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $result = $stmt->fetchColumn();

        return $result;
    }

    private function saveGame($gameToken, $gameData)
    {

        //$gameData = json_encode($this->game);
        //$gameToken = $this->game->gameToken;

        $sql = "UPDATE {$this->tables->gameTable}
			    SET gameData = :gameData
                WHERE gameToken = :gameToken";

        $data = [
            'gameData' => json_encode($gameData),
            'gameToken' => $gameToken,
        ];
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }


    private function createGame()
    {
        // add row to gameTable
        // save global $this->game 

        $gameData = json_encode($this->game);
        $gameToken = $this->game->gameToken;

        $sql = "INSERT INTO {$this->tables->gameTable}
                (gameToken, gameData)
                VALUES (:gameToken, :gameData)";

        $data = [
            'gameToken' => $gameToken,
            'gameData' => $gameData

        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $gameId = $this->pdo->lastInsertId();

        $this->game->gameID = $gameId;

        return $gameId;
    }

    private function loadGame($gameToken)
    {

        //error_log('loadGame1, gameToken=' . $gameToken);

        $sql = "SELECT gameData
			        FROM {$this->tables->gameTable}
                    WHERE gameToken = :gameToken";
        $data = [
            'gameToken' => $gameToken,
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $result = $stmt->fetchColumn();


        // error_log('loadGame');
        // error_log('gameToken= ' . $gameToken);
        // error_log($result);

        $this->game = json_decode($result);

        // error_log('loadGame2, gameToken=' . $this->game->gameToken);

        return $this->game;
    }



    private function checkUser($userName)
    {

        $inUse = false;
        if ($this->getUserName($userName)) {
            // user already logged in
            $inUse = true;
        } else {
            // userName available so login user
            // set UserName
            // get UUID   
        }

        return $inUse; // user not in system
    }

    function checkRoom($roomName)
    {

        $inUse = false;
        if ($this->getRoomName($roomName)) {
            // user already logged in
            $inUse = true;
        } else {
            // userName available so login user
            // set UserName
            // get UUID   
        }

        return $inUse;
    }

    // PDO get username where user = userid
    public function getUserName($userName)
    {
        $sql = "SELECT username
			        FROM {$this->tables->userTable}
                    WHERE userName = :userName";
        $data = [
            'userName' => $userName,
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $result = $stmt->fetchColumn();
        return $result;
    }

    // PDO get username where user = userid
    public function getRoomName($roomName)
    {
        $sql = "SELECT roomName
			        FROM {$this->tables->roomTable}
                    WHERE roomName = :roomName";
        $data = [
            'roomName' => $roomName,
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $result = $stmt->fetchColumn();

        return $result;
    }

    public function getRoomToken($roomName)
    {
        $sql = "SELECT roomToken
			        FROM {$this->tables->roomTable}
                    WHERE roomName = :roomName";
        $data = [
            'roomName' => $roomName,
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $result = $stmt->fetchColumn();

        return $result;
    }


    public function getGameTokenFromRoom($roomToken)
    {
        $sql = "SELECT gameToken
			        FROM {$this->tables->roomTable}
                    WHERE roomToken = :roomToken";
        $data = [
            'roomToken' => $roomToken,
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $result = $stmt->fetchColumn();

        return $result;
    }


    private function getGameStatus($gameToken)
    {
        $sql = "SELECT gameStatus
			        FROM {$this->tables->gameTable}
                    WHERE gameToken = :gameToken";
        $data = [
            'gameToken' => $gameToken,
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $result = $stmt->fetchColumn();

        return $result;
    }

    private function setGameStatus($gameToken, $gameStatus)
    {
        // null, missing
        // 'open'
        // 'play'
        // 'closed'
        $sql = "UPDATE {$this->tables->gameTable}
			    SET gameStatus = :gameStatus
                WHERE gameToken = :gameToken";

        $data = [
            'gameStatus' => $gameStatus,
            'gameToken' => $gameToken,
        ];
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    // 
    private function createUser($userName, $userToken, $roomName, $roomToken)
    {

        $sql = "INSERT INTO {$this->tables->userTable}
                (userName, userToken, roomName, roomTOken)
                VALUES (:userName, :userToken, :roomName, :roomToken)";

        $data = [
            'userName' => $userName,
            'userToken' => $userToken,
            'roomName' => $roomName,
            'roomToken' => $roomToken
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $lastInsertId = $this->pdo->lastInsertId();

        return $lastInsertId;
    }

    // 
    public function createRoom($roomName, $roomToken, $hostName, $hostToken, $gameToken)
    {

        $sql = "INSERT INTO {$this->tables->roomTable}
                (roomName, roomToken, hostName, hostToken, gameToken)
                VALUES (:roomName, :roomToken, :hostName, :hostToken, :gameToken)";

        $data = [
            'roomName' => $roomName,
            'roomToken' => $roomToken,
            'hostName' => $hostName,
            'hostToken' => $hostToken,
            'gameToken' => $gameToken
        ];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $lastInsertId = $this->pdo->lastInsertId();

        return $lastInsertId;
    }

    // dnp PDO save login uuid
    public function deleteAll()
    {
        $sql = "DELETE FROM {$this->tables->userTable}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $sql = "DELETE FROM {$this->tables->roomTable}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $sql = "DELETE FROM {$this->tables->gameTable}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }

    function hello()
    {
        $sql = "SELECT *
			        FROM {$this->tables->helloTable}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchColumn();

        return $result;
    }
}
