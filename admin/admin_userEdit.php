<?php
include '/home/techzrla/tmt.php';
session_start();

// User is not logged in, go to login page
if ($_SESSION['username'] == null) {
    header("Location: /login");
    die();
}
else {
    $db = db();
    $perm = "admin";
    $stmt = $db->prepare("select * from account where username = ? and permission = ? ");
    $stmt->bind_param("ss", $_SESSION['username'], $perm);
    $stmt->execute();
    if($stmt->get_result()->num_rows == 0) {
        header("Location: /dashboard");
        die();
    }
}

?>
<html>
<head>
    <title>User Editor; Tech Meets Tech</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>
<form action="index.php" method="post">
    <input type="submit" value='Back' name='Back'>
</form>
<?php
include 'utils.php';
if (isset($_POST["addUser"])) {
    $toInsert = $_POST["userToAdd"];
    if ($toInsert == null) {
        echo "User can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("INSERT INTO account(username) VALUES (?)");
            $stmt->bind_param("s", $toInsert);
            $stmt->execute();
            echo "User add statement executed successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

// Similar functionality to adding user, but the SQL statement has changed to
// remove from accounts.
if (isset($_POST["removeUser"])) {
    $toInsert = $_POST["userToRemove"];
    if ($toInsert == null) {
        echo "User can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("DELETE FROM account WHERE username=?");
            $stmt->bind_param("s", $toInsert);
            $stmt->execute();
            echo "User removal statement executed successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

echo "Add User:"
?>
<form action="admin_userEdit.php" method="post">
    <input type="text" id="userToAdd" name="userToAdd">
    <input type="submit" value="Add User" name="addUser">
</form>
<?php
echo "Remove User:"
?>
<form action="admin_userEdit.php" method="post">
    <input type="text" id="userToRemove" name="userToRemove">
    <input type="submit" value="Remove User" name="removeUser">
</form>
<table class="table table-striped">
    <thead class="thead-dark">
        <tr>
            <th>username</th>
            <th>phone</th>
            <th>profile_description</th>
            <th>year</th>
            <th>major</th>
            <th>color</th>
            <th>twitter_username</th>
        </tr>

    <?php
    $table = "account";
    $result = null;
    $result = getAllFromTable($table);
    echo "Formal accounts in TMT";
    while ($row = $result->fetch_assoc()) {
        echo sprintf('
                    <tr>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                    </tr>
                    ', $row["username"], $row["phone"], $row["profile_description"], $row["year"], $row["major"], $row["color"], $row["twitter_username"]);
    }
    echo "<table>";
    ?>
    </thead>
</table>

