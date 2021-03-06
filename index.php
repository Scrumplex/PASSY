<?php
require_once __DIR__ . "/config.inc.php";
require_once __DIR__ . "/meta.inc.php";
if ($generalConfig["redirect_ssl"] && isset($_SERVER["HTTPS"]) && $_SERVER['HTTPS'] == "off") {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect);
    die();
}
?>
<!DOCTYPE html>
<!--
    PASSY - Modern HTML5 Password Manager
    Copyright (C) 2017 Sefa Eyeoglu <contact@scrumplex.net> (https://scrumplex.net)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 -->
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ff5722">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/img/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png">
    <link rel="manifest" href="manifest.json">
    <link rel="mask-icon" href="assets/img/safari-pinned-tab.svg" color="#ff5722">
    <meta name="msapplication-TileColor" content="#ff5722">
    <meta name="msapplication-TileImage" content="mstile-144x144.png">
    <title><?= $customizationConfig["title"] ?></title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/material-icons.min.css" rel="stylesheet">
    <link href="assets/css/application.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <span class="navbar-brand"><?= $customizationConfig["title"] ?></span>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li>
                <a href="#" id="btnLogout" data-page-scope="logged_in" style="display: none;">
                    <i class="material-icons">exit_to_app</i>
                </a>
            </li>
            <li class="dropdown" data-page-scope="logged_in" style="display: none;">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    <i class="material-icons" id="aMenu">more_vert</i>
                </a>
                <ul class="dropdown-menu">
                    <li style="animation-delay: 100ms">
                        <a href="#!p=user_settings" data-to-page="user_settings">
                            <i class="material-icons">edit</i> User Settings
                        </a>
                    </li>

                    <?php
                    if ($generalConfig["login_history"]["enabled"]) {
                        ?>
                        <li>
                            <a href="#!p=login_history" data-to-page="login_history">
                                <i class="material-icons">list</i> Login History
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </li>
        </ul>
    </div>
    <div class="container">
        <ul class="nav navbar-nav">
            <li data-page-highlight="login" data-page-scope="logged_out">
                <a href="#!p=login" data-to-page="login">
                    <i class="material-icons">person</i> Login
                </a>
            </li>

            <?php
            if ($generalConfig["registration"]["enabled"]) {
                ?>
                <li data-page-highlight="register" data-page-scope="logged_out">
                    <a href="#!p=register" data-to-page="register">
                        <i class="material-icons">person_pin_circle</i> Register
                    </a>
                </li>
                <?php
            }
            ?>

            <li data-page-highlight="password_list" data-page-scope="logged_in" style="display: none">
                <a href="#!p=password_list" data-to-page="password_list">
                    <i class="material-icons">lock_outline</i> Passwords
                </a>
            </li>

            <li data-page-highlight="archived_password_list" data-page-scope="logged_in" style="display: none">
                <a href="#!p=archived_password_list" data-to-page="archived_password_list">
                    <i class="material-icons">archive</i> Archive
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="statusMessageContainer" style="display: none">
    <div class="statusMessage text-center col-xs-11 col-sm-5 col-md-4 col-lg-3">
        <h3 class="statusMessageText"></h3>
        <button class="btn btn-flat btn-primary statusMessageButton"></button>
    </div>
</div>

<div class="content">
    <div class="load-spinner">
        <svg class="spinner" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
        </svg>
    </div>

    <?php
    include_once __DIR__ . "/page/page_login.inc.php";

    if ($generalConfig["registration"]["enabled"])
        include_once __DIR__ . "/page/page_register.inc.php";

    include_once __DIR__ . "/page/page_password_list.inc.php";

    include_once __DIR__ . "/page/page_archived_password_list.inc.php";

    if ($generalConfig["login_history"]["enabled"])
        include_once __DIR__ . "/page/page_login_history.inc.php";

    include_once __DIR__ . "/page/page_user_settings.inc.php";
    ?>

</div>

<div class="modal fade" id="modal_connection_lost" data-keyboard="false" data-backdrop="static" tabindex="-1"
     role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content depth-5">
            <div class="modal-header">
                <h4 class="modal-title">Connection error!</h4>
            </div>
            <div class="modal-body">
                <p>
                    Either the backend went offline or there is a problem with the server. Please contact the system
                    administrator. This may be temporary.
                </p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-flat btn-warning" onclick="location.reload()">Reload</button>
            </div>
        </div>
    </div>
</div>

<!-- CONTEXTMENU -->
<div class="dropdown contextmenu" id="dropdownContextMenu">
    <ul class="dropdown-menu">
        <li>
            <a href="#" data-to-page="refresh">
                <i class="material-icons">refresh</i> Refresh
            </a>
        </li>
    </ul>
</div>

<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-xs-12">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 140.72912 185" height="50px">
                    <g fill-rule="evenodd">
                        <path d="M0 0v120l34.640625-20V60L69.28125 80l34.64258-20L0 0zm34.640625 145L0 165l34.640625 20v-40z"
                              fill="#191919"></path>
                        <path d="M0 122.5v40l140.72913-81.25-34.64101-20z" fill="#323232"></path>
                    </g>
                </svg>
                <span class="text-muted hidden-xs" title="Build <?= PASSY_BUILD ?>"><?= PASSY_VERSION ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-xs-12">
                <p class="text-muted">
                    Store your passwords securely.
                </p>
            </div>
            <div class="col-sm-6 col-xs-12 text-right">
                <ul class="list-inline">
                    <li><a href="<?= PASSY_REPO ?>" target="_blank">GitHub</a></li>
                    <li><a href="<?= PASSY_BUGTRACKER ?>" target="_blank">Bug report</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<script src="node_modules/@passypw/wavesjs/dist/waves.js"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="assets/js/passy.js"></script>
<?php
if ($generalConfig["recaptcha"]["enabled"]) {
    ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <?php
}
?>
</body>
</html>
