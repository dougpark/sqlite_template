// global
let config = {};
let interval = 1000;
let user = {};
let messageCache = [];
let prevMessageTime = 0;
let game = {};

class Ship {
    constructor(name, size) {
        this.name = name;
        this.size = size;
    }
}

let ships = [];
ships[0] = new Ship("Starkiller Base", 5); //STARKILLER
ships[1] = new Ship("Imperial Star Destroyer", 4); //IMPERIAL STAR DESTROYER
ships[2] = new Ship("Tie Interceptor", 3); //TIE INTERCEPTOR
ships[3] = new Ship("Tie Bomber", 3); //TIE BOMBER
ships[4] = new Ship("Tie Fighter", 2); //TIE FIGHTER

// this runs when page is finished loading
$(document).ready(function () {
    

    createTables();
    insertWithTransaction();
    insertNoTransaction();
    tableInfo();
    version();

});




function tableInfo() {
    console.log('tableInfo');
    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            action: 'tableInfo'
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            $out = '<li> Table info: <br>';
            $out += 'executionTimeSeconds: ' + response.executionTimeSeconds + '<br>';
            $out += '</li>';

            $('#output').append($out);


        }
    });

}

function version() {

    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            action: 'version'
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            console.log(response.result);
            console.log(response.result[0]['sqlite_version()']);
            $out = '<li> Sqlite version: <br>';
            $out += response.result[0]['sqlite_version()'] + "<br>";
            $out += 'executionTimeSeconds: ' + response.executionTimeSeconds + '<br>';
            $out += '</li>';
            $('#output').append($out);

        }
    });

}

function insertWithTransaction() {
    rowCount = 100;
    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            rowCount: rowCount,
            action: 'insertWithTransaction'
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            $out = '<li> Insert with Transactionn: <br>';
            $out += 'rows inserted: ' + rowCount + "<br>";
            $out += 'lastInsertId: ' + response.result + '<br>';
            $out += 'executionTimeSeconds: ' + response.executionTimeSeconds + '<br>';
            $out += '</li>';
            $('#output').append($out);
        }
    });

}

function insertNoTransaction() {
    rowCount = 100;
    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            rowCount: rowCount,
            action: 'insertNoTransaction'
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            $out = '<li> Insert no Transactionn: <br>';
            $out += 'rows inserted: ' + rowCount + "<br>";
            $out += 'lastInsertId: ' + response.result + '<br>';
            $out += 'executionTimeSeconds: ' + response.executionTimeSeconds + '<br>';
            $out += '</li>';
            $('#output').append($out);

        }
    });

}

function createTables() {

    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            action: 'createTables'
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            console.log(response);
            $out = '<li> Create tables: <br>';
            $out += 'executionTimeSeconds: ' + response.executionTimeSeconds + '<br>';
            $out += '</li>';
            $('#output').append($out);

        }
    });

}









function initDb(user) {
    game = initDb2(game, user);
}

function initDb2(game, user) {

    let otherId = 'blue';
    if (user.teamId == 'blue') {
        otherId = 'orange';
    }

    game.db = {};
    game.db.us = {};
    game.db.us.id = user.teamId;
    game.db.us.hits = 0;
    game.db.us.misses = 0;
    game.db.us.total = 0;

    game.db.them = {};
    game.db.them.id = otherId;
    game.db.them.hits = 0;
    game.db.them.misses = 0;
    game.db.them.total = 0;
    game.db.result = 0;

    return game;
}

// host creates new game and then comes here
function initGame() {
    createBoard("us");
    createBoard("them");

    initDb(user);

    initializeCells("us");
    initializeCells("them");

    placeShips('us');
    placeShips('them');

    sendGame();

}

// player joins a game already created by host
function joinGame2() {
    createBoard("us");
    createBoard("them");

    initDb(user);

    initializeCells("us");
    initializeCells("them");

    // get initial game board
    getGame();

    initGameInterval();

}

function sendGame() {

    // console.log('sendGame gameToken=' + game.gameToken);
    // console.log(game);
    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            userName: user.userName,
            roomName: user.roomName,
            gameToken: game.gameToken,
            gameData: JSON.stringify(game),
            action: 'sendGame'
        },
        dataType: "json",
        success: function (response) {

            // game is ready to play
            getGame();

            initGameInterval();

        }
    });

}

function sendMessage(message) {

    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            userName: user.userName,
            roomName: user.roomName,
            gameToken: game.gameToken,
            message: JSON.stringify(message),
            action: 'sendMessage'
        },
        dataType: "json",
        success: function (response) {

            // back from sendMessage

        }
    });

}

function initGameInterval() {
    // check for updates every n seconds
    setInterval(function () {
        getMessage();
    }, interval);
}


function getMessage() {

    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            userName: user.userName, // todo
            roomName: user.roomName,
            gameToken: game.gameToken,
            action: 'lastMessageTime'
        },
        dataType: "json",
        success: function (response) {

            //console.log(response);
            message = response.game;
            //console.log('getGameStatus2, gameToken=' + game.gameToken);

            time = message.time;
            if (prevMessageTime == time) {
                //ignore
                //console.log('no time change, ingoring message ' + time);
            } else {
                getMessage2();
            }


        }
    });
}
// called every interval
function getMessage2() {

    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            userName: user.userName, // todo
            roomName: user.roomName,
            gameToken: game.gameToken,
            action: 'getMessage'
        },
        dataType: "json",
        success: function (response) {

            //console.log(response);
            message = response.game;
            //console.log('getGameStatus2, gameToken=' + game.gameToken);

            time = message.time;
            if (prevMessageTime == time) {
                //ignore
                //console.log('no time change, ingoring message ' + time);
            } else {
                prevMessageTime = time;
                processMessage(message);
            }


        }
    });
}

function processMessage(message) {
    // loop through messages
    // take action on each message
    //console.log(message);
    msg = message.msg;
    msg.forEach(processMessage);

    function processMessage(item) {

        // don't process messages already received
        msgId = item.id;
        if (messageCache.includes(msgId)) {
            // this msg already processed
        } else {
            messageCache.push(msgId);
            action = JSON.parse(item.messageData);
            //console.log(action);
            switch (action.type) {
                case 'fire':
                    console.log('msg received: fire');
                    processFire(action);
                    break;
                case 'getTagCloud':
                    //getTagCloud();
                    break;

                default:
                    //getRecent();
            }
        }
    }

}


function processFire(action) {
    //console.log(action);
    team = action.team;
    fromTeam = action.fromTeam;
    toTeam = action.toTeam;
    calcId = action.calcId;
    newStatus = action.result;

    //console.log(user);

    if (fromTeam == user.fromTeam) {
        return;
    }

    team = 'us';
    calcId = calcId.replace('them', 'us');
    if (game.db[team][calcId].shipName) {
        showStatus('They Hit your ' + game.db[team][calcId].shipName + "!");

    } else {
        showStatus('Missed!');
    }
    $('#' + calcId).addClass('hit');
    let t = setTimeout("$('#' + calcId).removeClass('hit')", 2000);


    // update local database
    game.db[team][calcId].status = newStatus;
    // update cell
    decorateCell(team, calcId, newStatus);
    // update score here
    calcScore(team);
}

function getGame() {

    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            userName: user.userName, // todo
            roomName: user.roomName,
            gameToken: game.gameToken,
            action: 'getGame'
        },
        dataType: "json",
        success: function (response) {

            //console.log(response);
            game = response.game;

            game = swapShipLocations(game);
            //console.log('getGameStatus2, gameToken=' + game.gameToken);

            // db = game.db;
            updateBoard('us');
            updateBoard('them');

        }
    });
}

function swapShipLocations(game) {

    if (user.teamId == 'blue') {
        return game;
    }

    //console.log('swapShipLocations');

    let oldGame = JSON.parse(JSON.stringify(game));

    game = initDb2(game, user);

    updateCells('us', 'them');
    updateCells('them', 'us');

    function updateCells(newTeam, oldTeam) {
        for (col = 1; col <= 10; col++) {
            for (row = 1; row <= 10; row++) {

                let oldCalcId = oldTeam + "-" + zeroPad(row, 2) + zeroPad(col, 2);

                let cell = {};

                cell = JSON.parse(JSON.stringify(oldGame.db[oldTeam][oldCalcId]));
                let calcId = newTeam + "-" + zeroPad(row, 2) + zeroPad(col, 2);
                game.db[newTeam][calcId] = cell;

            }
        }
    }

    //console.log(game);
    return game;


}

// host creates a new game
function newGame() {

    // send user-name and room-name to server
    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            userName: user.userName, // todo
            roomName: user.roomName,
            action: 'newGame'
        },
        dataType: "json",
        success: function (response) {

            game = response.game;
            let result = game.result;

            if (result == 1) {
                // ok, signed in with userName
                // ok, create a new room with roomName

                initGame();

            } else {
                game = {};
                // failed, bad userName
                // failed, bad roomName
                // login again

            }

            // $('#status').html(result);
            // console.log('back from newGame');
            //console.log(game);
        }
    });

}

function joinGame() {
    // send user-name and room-name to server
    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            userName: user.userName, // todo fix this from login screen
            roomName: user.roomName,
            action: 'joinGame'
        },
        dataType: "json",
        success: function (response) {

            game = response.game;
            let result = game.result;
            //console.log('joinGame');
            //console.log('server result=' + result);
            //console.log(game);

            if (result == 1) {
                // ok, signed in with userName
                // ok, create a new room with roomName

                joinGame2();

            } else {
                game = {};
                // failed, bad userName
                // failed, bad roomName
                // login again

            }

            //$('#status').html(result);
            // console.log('back from newGame');
            //console.log(game);
        }
    });

}

function loadConfig() {
    // load default params from config.json
    $.ajax({
        url: "config.json",
        method: "GET",
        success: function (response) {
            // get from config file
            user.userid = response.userid;
            user.token = response.token;

            // url params overide config.json
            if (searchParams.get('userid')) {
                user.userid = searchParams.get('userid');
            };
            if (searchParams.get('token')) {
                user.token = searchParams.get('token');
            };
            config = response;
        }
    });
}

function hello() {
    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            action: 'hello'
        },
        dataType: "json",
        success: function (response) {

            //$('#status').html(response);
            // console.log('back from ajax');
            // console.log(response);
        }
    });
}

function getURLParams() {
    // get q url param for items to query from pinboard
    // ?q=u:username/
    // ?q=t:programming/
    // ?q=t:javascript/
    let url = new URL(window.location.href);
    let searchParams = new URLSearchParams(url.search);

}

function keyDown() {
    // left = 37
    // up = 38
    // right = 39
    // down = 40
    $(document).keydown(function (e) {
        let up = [0, 37, 38, 65, 87];
        let down = [0, 32, 39, 40, 68, 83];
        let r = [0, 82];
        //console.log(e.keyCode);
        if (up.indexOf(e.keyCode) > 0) {
            scrollDir(-1);
        } else if (down.indexOf(e.keyCode) > 0) {
            scrollDir();
        } else if (r.indexOf(e.keyCode) > 0) {
            scrollToId(true);
        }

    });
}

function createBoard(team) {

    let out = "";
    out += "<div class='text-center'>";
    for (col = 1; col <= 10; col++) {
        for (row = 1; row <= 10; row++) {
            let calcId = team + "-" + zeroPad(row, 2) + zeroPad(col, 2);
            let link = ` '${team}', ${row}, ${col}, '${calcId}'`;
            out += `  <a class='m-0 p-1 btn btn-dark' href="javascript:fire(${link});" role='button'>`
            out += "   <span class='title'>";
            out += "    <span id='" + calcId + "' class='badge badge-dark oi oi-target '>" + " " + "</span>";
            out += "   </span>";
            out += "  </a>";
        }
        out += " <br> ";
    }
    out += "</div>";

    $('#board-' + team).html(out);

}


function placeShips(team) {

    game.db[team].name = team;
    // place ship in grid
    for (ship = 0; ship <= 4; ship++) {
        let s = ships[ship];
        let index = ship;
        let name = s.name;
        let size = s.size;

        let isPlaced = false;
        while (!isPlaced) {
            let ix = Math.floor((Math.random() * 10) + 1);
            let iy = Math.floor((Math.random() * 10) + 1);
            let dir = Math.floor((Math.random() * 2) + 1);
            // console.log('dir= ', dir);
            if (dir == 1) { // x direction
                if (size + ix <= 10) {
                    // console.log('xdir', ix, iy, size + ix);
                    if (!occupied(team, dir, ix, iy, size)) {
                        for (x = ix; x < size + ix; x++) {
                            let cell = {};
                            let y = iy;
                            let calcId = team + "-" + zeroPad(x, 2) + zeroPad(y, 2);
                            cell.status = "S";
                            cell.shipSize = size;
                            cell.shipName = name;
                            cell.shipIndex = index;
                            game.db[team][calcId] = cell;
                            // console.log('calcId= ', calcId);
                        }
                        isPlaced = true;
                    }

                } else {
                    //console.log('abort');
                }

            } else { // y direction
                //console.log('ydir', ix, iy, size + iy);
                if (size + iy <= 10) {
                    if (!occupied(team, dir, ix, iy, size)) {
                        for (y = iy; y < size + iy; y++) {
                            let cell = {};
                            let x = ix;
                            let calcId = team + "-" + zeroPad(x, 2) + zeroPad(y, 2);
                            cell.status = "S";
                            cell.shipSize = size;
                            cell.shipName = name;
                            cell.shipIndex = index;
                            game.db[team][calcId] = cell;
                            // console.log('calcId= ', calcId);
                        }
                        isPlaced = true;
                    }

                } else {
                    //console.log('abort');
                }

            }
        }

    }

    // console.log(game.db[team]);
}

function occupied(team, dir, ix, iy, size) {

    var occ = false;
    if (dir == 1) { // xdir
        for (x = ix; x < size + ix; x++) {
            let y = iy;
            let calcId = team + "-" + zeroPad(x, 2) + zeroPad(y, 2);
            //console.log('db= ', game.db[team][calcId].status);
            if (game.db[team][calcId].status == 'S') {
                occ = true;
            }

        }

    } else { // ydir
        for (y = iy; y < size + iy; y++) {
            let x = ix;
            let calcId = team + "-" + zeroPad(x, 2) + zeroPad(y, 2);
            //console.log('db= ', game.db[team][calcId].status);
            if (game.db[team][calcId].status == 'S') {
                occ = true;
            }

        }
    }
    //console.log('occupied', team, occ);
    return occ;
}

function initializeCells(team) {


    for (col = 1; col <= 10; col++) {
        for (row = 1; row <= 10; row++) {

            let cell = {};
            cell.status = "E";
            let calcId = team + "-" + zeroPad(row, 2) + zeroPad(col, 2);
            game.db[team][calcId] = cell;

        }
    }
}

function updateBoard(team) {

    for (col = 1; col <= 10; col++) {
        for (row = 1; row <= 10; row++) {
            let calcId = team + "-" + zeroPad(row, 2) + zeroPad(col, 2);
            decorateCell(team, calcId, game.db[team][calcId].status);
        }
    }

    updateScore(team);

}

function updateScore(team) {
    let out = `m ${zeroPad(game.db[team].misses,2)}, h ${zeroPad(game.db[team].hits,2)}/${zeroPad(game.db[team].total,2)}`;

    $('#score-' + team).html(out);
}

function zeroPad(num, places) {
    var zero = places - num.toString().length + 1;
    return Array(+(zero > 0 && zero)).join("0") + num;
}

function fire(team, row, col, calcId) {

    if (team == "us") {
        showStatus("Don't fire on your own ships!");
        return
    }



    let result = 'M';
    let send = false;

    switch (game.db[team][calcId].status) {
        case "S":
            game.db[team][calcId].status = "H";
            result = "H";
            showStatus('You Hit a ' + game.db[team][calcId].shipName + "!");

            $('#' + calcId).addClass('hit');
            setTimeout("$('#' + calcId).removeClass('hit')", 2000);

            send = true;
            break;
        case "E":
            game.db[team][calcId].status = "M";
            showStatus('You Missed!');

            $('#' + calcId).addClass('mis');
            setTimeout("$('#' + calcId).removeClass('mis')", 2000);
            send = true;
            break;
        case "H":
            game.db[team][calcId].status = "H";
            showStatus("Hey don't shoot there again.");

            break;
        case "M":
            game.db[team][calcId].status = "M";
            showStatus('You shot there already.');

            break;
    }

    if (send) {
        let message = {};
        message.type = 'fire';
        message.team = team;
        message.fromTeam = user.fromTeam;
        message.toTeam = user.toTeam;
        message.result = result;
        message.row = row;
        message.col = col;
        message.calcId = calcId;
        message.ship = game.db[team][calcId].shipName;

        decorateCell(message.team, message.calcId, message.result);
        calcScore(team);
        sendMessage(message);

    }

}

function showStatus(status) {
    $('#status').html(status);
    $('.status-show, .status-hide').toggleClass('status-show status-hide');
    setTimeout("$('.status-show, .status-hide').toggleClass('status-show status-hide')", 2000);

}

function clearFireAnimation() {
    // check for updates every n seconds
    setInterval(function () {
        getMessage();
    }, interval);
}

function calcScore(team) {

    let hits = 0;
    let total = 0;
    let misses = 0;

    for (var key in game.db[team]) {
        if (game.db[team].hasOwnProperty(key)) {
            /* useful code here */
            let loc = game.db[team][key]
            //console.log(loc.status);
            if (loc.status == 'S') {
                total += 1;
            }
            if (loc.status == 'H') {
                hits += 1;
            }
            if (loc.status == 'M') {
                misses += 1;
            }
        }
    }
    total += hits;
    game.db[team].hits = hits;
    game.db[team].misses = misses;
    game.db[team].total = total;

    updateScore(team);

}

function decorateCell(team, calcId, newStatus) {

    //console.log('decorateCell');
    // E - Empty
    // S - Ship
    // H - Hit
    // M - Miss

    // todo don't display their ship locations
    if (team == "them" && newStatus == "S") {
        // newStatus = "E";
    }

    let colors = {
        'E': 'text-primary ',
        'S': 'text-warning ',
        'H': 'text-danger ',
        'M': 'text-white '
    };

    let shipColors = {
        '2': 's2',
        '3': 's3',
        '4': 's4',
        '5': 's5'
    }

    let s = colors.E + colors.S + colors.H + colors.M;
    $("#" + calcId).removeClass(s);

    if (newStatus == 'S') {
        let ship = game.db[team][calcId].shipSize;

        $("#" + calcId).addClass(shipColors[ship]);

    } else {
        $("#" + calcId).addClass(colors[newStatus]);
    }



}