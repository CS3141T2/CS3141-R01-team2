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
    $stmt0 = $db->prepare("select count(*) from friend");
    $stmt0->execute();
    $res0 = $stmt0->get_result();

    $stmt = $db->prepare("insert into friend values(?,?)");
    $stmt->bind_param("ss", $_SESSION["username"], $_POST["friend"]);
    $stmt->execute();
    $res = $stmt->get_result();

    $stmt2 = $db->prepare("select count(*) from friend");
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    if( $res0 != $res2 )
    {
        echo "<p> succesfully added user " . $_POST["friend"] . "</p>";
    }
    else
    {
        echo "<p>add friend failed</p>";
    }

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
        <div>
            <?php sideBarButton(); ?>
            <form method="post" action="addFriend.php">
                <p style="display:inline"> Enter your friend's username here </p>
                <input type="text" id="friend" name="friend">
                <br>
                <input type="submit" name="newFriend" value="send request">
            </form>
        </div>

    </body>
</html>
