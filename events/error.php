<?php
include '/home/techzrla/tmt.php';
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
    <?php echo bootstrap(); ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="col text-center" style="margin: 1em">
            <h1>Events</h1>
            <form method="post" action="event.php">
                <div class="col text-center">
                    <input class="btn btn-primary" type="submit" name="backToMain" value="Back to Main Event Page">
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
