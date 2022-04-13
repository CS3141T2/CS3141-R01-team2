<?php
include '/home/techzrla/tmt.php';
session_start();

// User is not logged in, redirect to login page
if ($_SESSION["username"] == null) {
    header("LOCATION: /login");
    die();
}
// Check if the user is an admin
else if (isAdmin($_SESSION['username']) == false)
{
    header("LOCATION: /dashboard");
    die();
} else {
    echo "Hello!";
    echo "Welcome " . $_SESSION["username"];
    echo "Click on the table you would like to edit:";
    ?>
    <form action="ProjectLogin.php" method="post">
        <input type="submit" value='logout' name='logout'>
    </form>
    <?php
}
?>

<?php
if (isset($_POST["Back"]) || !isset($_SESSION['view'])) {
    $_SESSION['view'] = 0;
} else {
    if (isset($_POST["Manage Accounts"])) {
        echo "Done1";
    }
    if (isset($_POST["Manage Communities"])) {
        echo "Done2";
    }
    if (isset($_POST["Manage Community Events"])) {
        echo "Done3";
    }
    if (isset($_POST["Manage Community Members"])) {
        echo "Done4";
    }
    if (isset($_POST["Manage User Events"])) {
        echo "Done5";
    }
    if (isset($_POST["Manage User Friends"])) {
        echo "Done6";
    }
}
?>
<html>
    <head>
        <?php echo bootstrap(); ?>
        <title>Admin functions</title>
    </head>
    <body>
        <form action="admin_dash.php" method="post">
            <?php
            if ($_SESSION['view'] != 1) {
                ?>
                <input type="submit" value="Manage Accounts" name="Manage Accounts">
                <input type="submit" value="Manage Communities" name="Manage Communities">
                <input type="submit" value="Manage Community Events" name="Manage Community Events">
                <input type="submit" value="Manage Community Members" name="Manage Community Members">
                <input type="submit" value="Manage User Events" name="Manage User Events">
                <input type="submit" value="Manage User Friends" name="Manage User Friends">
                <?php
            } else {
                ?>
                <input type="submit" value='back' name='Back'>
                <?php
            }
            ?>
        </form>
    </body>


</html>
