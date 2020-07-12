let accountInfo = {};
loadAccountInfo();

function loadAccountInfo() {
    loadFromServer();

    // use this to load from localstorage on the client
    // if (localStorage["accountInfo"]) {
    //     accountInfo = JSON.parse(window.atob(localStorage.getItem('accountInfo')));
    //     $('#ai-userId').val(accountInfo.userId);
    //     $('#ai-pw').val(accountInfo.pw);
    //     $('#ai-token').val(accountInfo.token);
    // }
}

function saveAccountInfo() {
    saveToServer();

    // use this to save to localstorage on the client
    // localStorage.setItem('accountInfo', window.btoa(JSON.stringify(accountInfo)));
}

function setNewUserId(userId) {
    accountInfo.userId = userId;
    saveAccountInfo();
}

function setNewPassword(pw) {
    accountInfo.pw = pw;
    saveAccountInfo();
}

function setNewToken(token) {
    accountInfo.token = token;
    saveAccountInfo();
}

function saveToServer() {
    let data = {};
    data.key = accountInfo.userId;
    data.value = accountInfo;

    $.ajax({
        url: "accountInfo.php",
        method: "POST",
        data: {
            accountInfo: JSON.stringify(data),
            action: 'save'
        },
        dataType: "json",
        success: function (response) {

            // check valid response
        }
    });

}

function loadFromServer() {
    let data = {};
    data.key = 'parkdn'; //xxx
    data.value = accountInfo;

    $.ajax({
        url: "accountInfo.php",
        method: "POST",
        data: {
            accountInfo: JSON.stringify(data),
            action: 'load'
        },
        dataType: "json",
        success: function (response) {

            // console.log(response);
            // console.log(response.result[0].value);

            accountInfo = JSON.parse(response.result[0].value);
            $('#ai-userId').val(accountInfo.userId);
            $('#ai-pw').val(accountInfo.pw);
            $('#ai-token').val(accountInfo.token);
        }
    });

}