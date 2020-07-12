// this runs when page is finished loading
$(document).ready(function () {
    createTables();
    insertWithTransaction();
    insertNoTransaction();
    tableInfo();
    version();
});


function tableInfo() {

    $.ajax({
        url: "api.php",
        method: "POST",
        data: {
            action: 'tableInfo'
        },
        dataType: "json",
        success: function (response) {

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

            $out = '<li> Create tables: <br>';
            $out += 'executionTimeSeconds: ' + response.executionTimeSeconds + '<br>';
            $out += '</li>';
            $('#output').append($out);

        }
    });

}