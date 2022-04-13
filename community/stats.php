<?php
include '/home/techzrla/tmt.php';
session_start();
$db = db();

/*if ($_SESSION["username"] == null) { // User is logged in, go to dashboard
    header("Location: /login");
    die();
}*/

if (!isset($_GET))

?>


<html lang="en-US">
<head>
    <?php echo bootstrap(); ?>
    <title>Communities &mdash; Tech Meets Tech</title>
</head>
<body>
    <div class="container">
        <div class="col text-center">
            <h1>Communities</h1>
            <p>Find your communities</p>
        </div>
        <div class="col text-center" style="max-width: 20em; margin: 1em auto">
            <form method="post" action="search.php">
                <input class="form-control" name="x" type="text" placeholder="Community" maxlength="30" style="max-width: 14em; margin: 1em auto">
                <input class="btn btn-primary" type="submit" name="search" value="search" style="max-width: 14em; margin: 1em auto">
            </form>
        </div>
        <div class="col text-center" style="min-width: 25%; max-width: 50%; margin: 1em auto">
            <?php buildTable($db);?>
        </div>
    </div>
</body>

</html>