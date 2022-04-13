<?php
include '/home/techzrla/tmt.php';
include 'utils.php';
session_start();

// User is not logged in, go to login page
if ($_SESSION['username'] == null) {
    header("Location: /login");
    die();
}
// Else verify the use is an admin
else if (isAdmin($_SESSION['username']) == false) {
    header("LOCATION: /dashboard");
    die();
}
?>
<html>
    <head>
        <title>Admin Dashboard; Tech Meets Tech</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <link rel="stylesheet" href="/home/techzrla/admin/style.css">

        <!-- JS -->


    </head>
    <body>
    <div class="wrapper">
        <nav class="sidebar">
            <div class="dismiss">
                <i class="fas fa-arrow-left"></i>
            </div>
            <ul class="list-unstyled menu-elements">
                <li class="active">
                    <a class="scroll-link" href="#top-content"><i class="fas fa-home"></i>Home</a>
                </li>
                <li>
                    <a class="scroll-link" href="#section-1"><i class="fas fa-user"></i>Users and Accounts</a>
                </li>
                <li>
                    <a class="scroll-link" href="#section-2"><i class="fas fa-users"></i>Community Events and Members</a>
                </li>
                <li>
                    <a class="scroll-link" href="#section-3"><i class="fas fa-user-plus"></i>User Events and Friends</a>
                </li>
                <li>
                    <a class="scroll-link" href="#section-4"><i class="fas fa-comment"></i>Survey Questions and Interests</a>
                </li>
            </ul>
        </nav>
        <div class="content">

        </div>
    </div>
    <br><br>
        <div class="container"  align="center" style="width=600px">
            <h2>Welcome, Administrator!</h2>
            <br>
            <form action="admin_userEdit.php">
                <button type="submit" class="btn btn-primary">Manage Accounts and Users</button>
            </form>
            <form action="admin_communityEdit.php">
                <button type="submit" class="btn btn-primary">Manage Communities and Leaders</button>
            </form>
            <form action="admin_eventEdit.php">
                <button type="submit" class="btn btn-primary">Manage Community Events</button>
            </form>
            <form action="admin_memberEdit.php">
                <button type="submit" class="btn btn-primary">Manage Community Members</button>
            </form>
            <form action="admin_userEventEdit.php">
                <button type="submit" class="btn btn-primary">Manage User Events</button>
            </form>
            <form action="index.php">
                <button type="submit" class="btn btn-primary">Manage User Friends (NYI)</button>
            </form>
            <form action="admin_surveyEdit.php">
                <button type="submit" class="btn btn-primary">Manage Survey Questions and Interests</button>
            </form>
        </div>
    </body>

</html>
