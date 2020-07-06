<?php
session_start();
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <link rel="icon" href="./favicon.png" />
    <link rel="apple-touch-icon" href="./favicon.png" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="apple-mobile-web-app-title" content="Sqlite Template">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" crossorigin="anonymous"> -->

    <!-- w3.css -->
    <link rel="stylesheet" href="./css/w3.css">
    <!-- <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> -->

    <!-- iconic open fonts -->
    <!-- https://useiconic.com/open -->
    <link href="./iconic/font/css/open-iconic.css" rel="stylesheet">

    <!-- Local Styles -->
    <link rel="stylesheet" href="./css/style.css">

    <title>Sqlite Template</title>

    <script>
        function swapStatus() {
            $('.status-show, .status-hide').toggleClass('status-show status-hide');
        }
    </script>
</head>

<body class="w3-content" style="max-width:500px">





    <!-- Main Nav Bar -->
    <div class="w3-top status-show">
        <div class="w3-bar w3-round-large z-nav-background" style="max-width:500px">
            <span class="">
                <a onclick="xswapStatus()" class="w3-bar-item w3-round-large z-nav-text">back</a>
            </span>

            <span class="">
                <a onclick="document.getElementById('id01').style.display='block'" class="w3-bar-item z-nav-text w3-round-large w3-right">
                    <span class="oi" data-glyph="info" title="info" aria-hidden="true"></span> About</a>
            </span>

            <span class="">
                <a onclick="swapStatus()" class="w3-bar-item z-nav-text w3-round-large w3-right">
                    <span class="oi" data-glyph="list" title="info" aria-hidden="true"></span> Settings</a>
            </span>

            <!-- <button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-black">Open Modal</button> -->

        </div>
    </div>

    <!-- Settings Nav Bar -->
    <div class="w3-top status-hide">
        <div class="w3-bar z-nav-background" style="max-width:500px">
            <span class="">
                <a onclick="swapStatus()" class="w3-bar-item w3-round-large z-nav-text">
                    <span class="oi" data-glyph="chevron-left" title="info" aria-hidden="true"></span> Done</a>
            </span>
        </div>
    </div>

    <!-- <div class="w3-bottom">
        <div class="w3-bar w3-indigo" style="max-width:500px">

            <a href="#" class="w3-bar-item w3-button w3-padding-32">Home</a>
            <a href="#" class="w3-bar-item w3-button w3-padding-32">Link 1</a>
            <a href="#" class="w3-bar-item w3-button w3-padding-32">Link 2</a>
            <a href="#" class="w3-bar-item w3-button w3-padding-32">Link 3</a>
        </div>
    </div> -->

    <div class="w3-container w3-card  w3-margin-top">
    </div>
    <div class="w3-container w3-card  w3-margin-top">
    </div>

    <div id="m-1" class="status-show w3-container w3-card  w3-margin-top">
        <h1>Sqlite Template</h1>

        <ul class="w3-ul">
            <li>
                <h3>w3-content</h3>
            </li>
        </ul>

        <ul id="output" class="w3-ul">
        </ul>
    </div>

    <div class="includeHtml" title="./lib/settings.html"></div>
    <div class="includeHtml" title="./lib/settingsModal.html"></div>
    <div class="includeHtml" title="./lib/accountInfo.html"></div>

    <!-- Important for jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous">
    </script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" crossorigin="anonymous"> -->
    </script>
    <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" crossorigin="anonymous"> -->
    </script>


    <!-- Local Js -->
    <script src="./lib/includeHtml.js"></script>
    <script src="./main.js"></script>
    <script src="./lib/darkMode.js"></script>
</body>

</html>