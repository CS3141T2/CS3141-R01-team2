<?php
include '../sidebar.php';
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
        <?php
        echo head_goodies();
        echo sideBar($_SESSION['username']);
        ?>
        <title>Admin Dashboard; Tech Meets Tech</title>
    </head>
    <body>
        <br>
        <div class="container"  align="center" style="width=600px">
            <?php sideBarButton(); ?>
            <h2>Welcome, Administrator!</h2>
            <br>
            <form action="admin_userEdit.php">
                <?php echo mat_but_submit('', 'Manage Accounts and Users', '', 'manage_accounts', '', '', false); ?>
            </form>
            <form action="admin_communityEdit.php">
                <?php echo mat_but_submit('', 'Manage Communities and Leaders', '', 'groups', '', '', false); ?>
            </form>
            <form action="admin_eventEdit.php">
                <?php echo mat_but_submit('', 'Manage Community Events', '', 'edit_calendar', '', '', false); ?>
            </form>
            <form action="admin_memberEdit.php">
                <?php echo mat_but_submit('', 'Manage Community Members', '', 'group_add', '', '', false); ?>
            </form>
            <form action="admin_userEventEdit.php">
                <?php echo mat_but_submit('', 'Manage User Events', '', 'edit_calendar', '', '', false); ?>
            </form>
            <form action="admin_friendEdit.php">
                <?php echo mat_but_submit('', 'Manage User Friends', '', 'people', '', '', false); ?>
            </form>
            <form action="admin_surveyEdit.php">
                <?php echo mat_but_submit('', 'Manage Survey Questions and Interests', '', 'edit_note', '', '', false); ?>
            </form>
        </div>
    </body>

</html>
