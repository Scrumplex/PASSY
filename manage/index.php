<?php
define("END", "FRONT");
include __DIR__ . "/../include/user.inc.php";
if (!isLoggedIn()) {
    header("Location: ../");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ff5722">
    <title>PASSY</title>

    <link href="https://cdn.scrumplex.net/css/bootstrap/3.3.7/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.scrumplex.net/css/material-icons/3.0.1/material-icons.min.css" rel="stylesheet">
    <link href="https://cdn.scrumplex.net/css/ripple.js/1.2.1/ripple.min.css" rel="stylesheet">
    <link href="../assets/css/app.min.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <!-- @formatter:off -->
            <span class="navbar-brand">PASSY</span>
            <!-- @formatter:on -->
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="#" id="btnLogout"><i class="material-icons">exit_to_app</i></a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false"><i class="material-icons" id="aMenu">more_vert</i></a>
                <ul class="dropdown-menu">
                    <li style="animation-delay: 100ms"><a href="#"><i class="material-icons">edit</i> Profile
                            Settings</a></li>
                    <li style="animation-delay: 100ms"><a href="#!p=login_history" data-to-page="login_history"><i
                                    class="material-icons">list</i> Login History</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="container">
        <ul class="nav navbar-nav" style="position: static">
            <li data-page-highlight="passwords"><a href="#!p=passwords" data-to-page="passwords"><i
                            class="material-icons">lock_outline</i>
                    Passwords <span class="sr-only">(current)</span></a></li>
            <li data-page-highlight="archive"><a href="#!p=archive" data-to-page="archive"><i class="material-icons">archive</i>
                    Archive</a></li>
        </ul>
    </div>
</nav>
<div class="statusMessageContainer" style="display: none">
    <div class="statusMessage text-center col-xs-11 col-sm-5 col-md-4 col-lg-3">
        <h3 class="statusMessageText">No connection!</h3>
        <button class="btn btn-flat btn-primary statusMessageButton" data-location="reload">Retry</button>
    </div>
</div>
<div class="content">
    <div class="load-spinner">
        <svg class="spinner" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
        </svg>
    </div>

    <div id="page_passwords" class="container" style="display: none">
        <div class="jumbotron">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="text-center">Passwords</h2>
                    <p>Add new passwords or view your saved passwords.</p>
                </div>
            </div>
            <div class="row row-margin">
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table id="tablePasswords" class="table table-hover">
                            <thead>
                            <tr>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Website</th>
                                <th>Date added</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody id="tbodyPasswords">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-fab" id="btnAdd" title="Add password..."><i class="material-icons">add</i></button>
    </div>
    <div id="page_archive" class="container" style="display: none">
        <div class="jumbotron">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="text-center">Archive</h2>
                    <p>The passwords appearing here can be restored or deleted permanently. They will be deleted two
                        weeks after they have been archived.</p>
                </div>
            </div>
            <div class="row row-margin">
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table id="tableArchivedPasswords" class="table table-hover">
                            <thead>
                            <tr>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Website</th>
                                <th>Date archived</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody id="tbodyArchivedPasswords">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="page_login_history" class="container" style="display: none">
        <div class="jumbotron">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="text-center">Login History</h2>
                    <p>Find out who accessed your account.</p>
                </div>
            </div>
            <div class="row row-margin">
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table id="tableLoginHistory" class="table table-hover">
                            <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Location</th>
                                <th>User-Agent</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody id="tbodyLoginHistory">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTEXTMENU -->
<div class="dropdown contextmenu" id="dropdownContextMenu">
    <ul class="dropdown-menu">
        <li><a href="#" id="aRefresh"><i class="material-icons">refresh</i> Refresh</a></li>
    </ul>
</div>

<!-- MODALS -->
<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content depth-5">
            <div class="modal-header">
                <h4 class="modal-title">Create password</h4>
            </div>
            <form method="post" action="backend/addPassword.php" id="formAddPassword" autocomplete="off">
                <div class="modal-body">
                    <p>
                        Fill the form below to create a new password entry. You will be able to view the password, edit
                        the entry and share the entry with others later.
                    </p>
                    <div class="form-group">
                        <div class="text">
                            <input type="text" class="form-control" title="Username" name="username"
                                   autocomplete="off"/>
                            <label>Username (optional)</label>
                        </div>
                        <div class="text">
                            <input type="password" class="form-control" title="Password" name="password" required
                                   autocomplete="off"/>
                            <label>Password</label>
                        </div>
                        <div class="text">
                            <input type="text" class="form-control" title="Password" name="website" autocomplete="off"/>
                            <label>Website (optional)</label>
                        </div>
                    </div>
                    <div class="alert alert-danger" id="errorDatabase" style="display: none">
                        <strong>Error!</strong> There was a problem with the database connection.
                    </div>
                    <div class="alert alert-danger" id="errorUnknown" style="display: none">
                        <strong>Error!</strong> An unhandled error occurred!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-flat btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content depth-5">
            <div class="modal-header">
                <h4 class="modal-title">Edit password</h4>
            </div>
            <form method="post" action="backend/editPassword.php" id="formEditPassword" autocomplete="off">
                <input id="formEditPasswordId" type="hidden" name="id" value="" style="display: none"/>
                <div class="modal-body">
                    <p>
                        Put text here
                    </p>
                    <div class="form-group">
                        <div class="text">
                            <input id="formEditPasswordUsername" type="text" class="form-control" title="Username"
                                   name="username"
                                   autocomplete="off"/>
                            <label>Username (optional)</label>
                        </div>
                        <div class="text">
                            <input id="formEditPasswordPassword" type="text" class="form-control" title="Password"
                                   name="password" required
                                   autocomplete="off"/>
                            <label>Password</label>
                        </div>
                        <div class="text">
                            <input id="formEditPasswordWebsite" type="text" class="form-control" title="Password"
                                   name="website" autocomplete="off"/>
                            <label>Website (optional)</label>
                        </div>
                    </div>
                    <div class="alert alert-danger" id="errorDatabase" style="display: none">
                        <strong>Error!</strong> There was a problem with the database connection.
                    </div>
                    <div class="alert alert-danger" id="errorUnknown" style="display: none">
                        <strong>Error!</strong> An unhandled error occurred!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-flat btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include __DIR__ . "/../include/ui/footer.php";
include __DIR__ . "/../include/ui/tracker.php";
?>
<script src="https://cdn.scrumplex.net/js/jquery/3.1.0/jquery.min.js "></script>
<script src="https://cdn.scrumplex.net/js/bootstrap/3.3.7/bootstrap.min.js "></script>
<script src="https://cdn.scrumplex.net/js/ripple.js/1.2.1/ripple.min.js"></script>
<script src="../assets/js/global.js"></script>
<script src="../assets/js/app.main.js"></script>
</body>
</html>
