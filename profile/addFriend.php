<?php

include '../sidebar.php';
session_start();
$db = db();

// Leave the page if the user is not signed in.
if (!isset($_SESSION["username"])) {
    header("Location: /login");
    die();
}


if(isset($_POST["newFriend"]) )
{
    $stmt = $db->prepare("insert into friend values(?,?)");
    $stmt->bind_param("ss", $_SESSION["username"], $_POST["friend"]);
    $stmt->execute();
    $res = $stmt->get_result();
}



?>

<html>
    <head>
        <?php
        echo head_goodies();
        echo sideBar($_SESSION["username"]);
        ?>
        <title> Add Friends </title>
    </head>
    <body>
        <br>
        <div class="container rounded shadow">
            <div style="padding: 20px" class="row">
                <?php sideBarButton(); ?>
                <br><br>
                <form style="text-align: center" method="post" action="addFriend.php">
                    <p style="display:inline;font-size: large"> Enter your friend's username here: </p>
                    <input type="text" id="friend" name="friend">
                    <br><br>
	                  <?php echo mat_but_submit('', 'Send request', 'newFriend', 'add_reaction', '', '', false); ?>
<!--                    <input type="submit" style='color: #000000; background-color: #ffcd00; min-width: 0 !important; text-align: center' class='mt-2 mdc-button mdc-button--raised tmt-button' name="newFriend" value="send request">-->
                </form>
            </div>
        </div>
    </body>
</html>
