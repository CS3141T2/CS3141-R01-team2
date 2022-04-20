<?php
include '../sidebar.php';
session_start();
$db = db();

if (!isset($_SESSION['username'])) {
    header("Location: /login");
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
	<title>Friend Events</title>
</head>
<body>
<div class="container">
    <?php sideBarButton(); ?>
</div>
</body>
</html>


