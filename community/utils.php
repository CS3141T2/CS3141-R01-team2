<?php
/**
 * Checks if user is a community leader
 * @param mysqli $db        Database connection
 * @param string $comm      Community name
 * @return void
 */
function isLeader(mysqli $db, string $comm): void
{
    $stmt = $db->prepare("SELECT * FROM community where name = ? and leader = ? ");
    $stmt->bind_param("ss", $comm, $_SESSION["username"]);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "<td><p>You are the leader!</p></td>";
    } else {
        echo sprintf('<td><form method="post">%s</form></td>', mat_but_submit('', "Leave $comm", "leave", "logout", "", "$comm", false));
    }
}

/**
 * When join/leave button is clicked, join/leave community
 * @param mysqli $db    Database connection
 * @return void         Adds/removes user from community
 */
function communityHandle(mysqli $db)
{
    // If button is clicked, add myself to community
    if (isset($_POST['join'])) {
        header("Location: /community/");
        $value = $_POST['join'];
        $add = $db->prepare("call addToCommunity(?, ?)");
        $add->bind_param("ss", $_SESSION["username"], $value);
        $add->execute();
        die();
    }

    // If button is clicked, remove myself from community
    if (isset($_POST['leave'])) {
        header("Location: /community/");
        $club = trim(preg_split('/\s+/', $_POST['leave'])[1]);
        $db = db();
        $stmt = $db->prepare("DELETE FROM member where account_name = ? and name = ?");
        $stmt->bind_param("ss", $_SESSION['username'], $club);
        $stmt->execute();
        die();
    }
}
