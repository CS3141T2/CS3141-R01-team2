<?php
include '../sidebar.php';
session_start();
$db = db();

if ($_SESSION["username"] == null) {
    header("Location: /login/index.php");
    die();
}

if (isset($_POST["backToMain"])) {
    header("Location: /events/event.php");
    die();
}
?>

<!doctype html>
<html lang="en-US">
<head>
    <?php
    echo head_goodies();
    echo sideBar($_SESSION["username"]);
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php sideBarButton(); ?>
        <div class="col text-center" style="margin: 1em">
            <h1>Events</h1>
            <form method="post" action="event.php">
                <div class="col text-center">
                    <?php echo mat_but_submit('', 'backToMain', 'backToMain', 'keyboard_return', '', '', false); ?>
                </div>
            </form>
            <br>
            <?php
                if (isset($_SESSION["errorOnCreate"])) {
                    echo $_SESSION["errorOnCreate"];
                }
            ?>
        </div>
    </div>
</body>
</html>
