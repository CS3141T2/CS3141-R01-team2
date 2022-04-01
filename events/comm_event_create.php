<?php
include '/home/techzrla/tmt.php';
session_start();
$db = db();

if ($_SESSION["username"] == null) {
    header("Location: /login/index.php");
    die();
}

if ( (($_POST["commName"]) != "") && (($_POST["nameEvent"]) != "") && (($_POST["typeSelect"]) != "") && (($_POST["description"]) != "") && (($_POST["date"]) != "") && (($_POST["location"]) != "")){
    $username = $_SESSION["username"];
    $getAcctName = $db->prepare("SELECT username FROM account where username=?");
    $getAcctName->bind_param("s", $username);
    $getAcctName->execute();
    $result = $getAcctName->get_result();
    if ($result->num_rows < 1) {
        echo "<div class='alert alert-warning' role='alert'>Must have an <a href='https://dev.techmeetstech.xyz/dashboard/' class='alert-link'>account</a> to create an individual event.</div>";
        die();
    }

    $commName = $_POST["commName"];
    $getCommLeader = $db->prepare("SELECT leader FROM community where name=?");
    $getCommLeader->bind_param("s", $commName);
    $getCommLeader->execute();
    $commResult = $getCommLeader->get_result();
    if ($commResult->num_rows < 1){
        echo "<div class='alert alert-warning' role='alert'>Community does not exist. <a href='https://dev.techmeetstech.xyz/dashboard/' class='alert-link'>Back to dashboard.</a></div>";
        die();
    }

    $commLeader = $commResult->fetch_assoc();
    if (strcmp($username, $commLeader) != 0) {
        echo "<div class='alert alert-warning' role='alert'>Must be a community leader. <a href='https://dev.techmeetstech.xyz/dashboard/' class='alert-link'>Back to dashboard.</a></div>";
        die();
    }

    $addEvent = $db->prepare("call addCommunityEvent(?, ?, ?, ?, ?, ?)");
    $addEvent->bind_param("ssssss", $_POST["commName"], $_POST["date"],  $_POST["location"], $_POST["nameEvent"], $_POST["typeSelect"], $_POST["description"]);
    $addEvent->execute();

    header("Location: /events/event.php");
    die();
}
?>

<html lang="en-US">
<head>
    <?php echo bootstrap(); ?>
    <title>Events</title>
</head>
    <div class="col text-center">
        <h1>Create a Community Event</h1>
        <form method="post" action="comm_event_create.php">
            <label>
                Community Name:
                <input class="form-control" type="text" name="commName">
            </label>
            <label>
                Name of Event:
                <input class="form-control" type="text" name="nameEvent">
            </label>
            <label>
                Type of Event:
                <input class="form-control" type="text" name="typeSelect">
            </label>
            <label>
                Description of Event:
                <input class="form-control" type="text" name="description">
            </label>
            <label>
                Event Date:
                <input class="form-control" type="date" name="date">
            </label>
            <label>
                Location:
                <input class="form-control" type="text" name="location">
            </label>
            <br><br>
            <input class="btn btn-primary" type="submit" value="Submit">
        </form>
    </div>
</html>