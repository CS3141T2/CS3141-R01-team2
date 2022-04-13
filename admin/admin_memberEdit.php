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
    <title>Community Editor; Tech Meets Tech</title>
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
if (isset($_POST["addMember"])) {
    $toInsert = $_POST["memberToAdd"];
    $toInsert1 = $_POST["toCommunity"];
    $view = 4;
    $table = "member";
    if ($toInsert == null || $toInsert1 == null) {
        echo "Field can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("INSERT INTO member(account_name, name) VALUES (?, ?)");
            $stmt->bind_param("ss", $toInsert, $toInsert1);
            $stmt->execute();
            echo "Event statement executed without errors";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

if (isset($_POST["removeMember"])) {
    $toInsert = $_POST["memberToRemove"];
    $toInsert1 = $_POST["fromCommunity"];
    $view = 4;
    $table = "member";
    if ($toInsert == null) {
        echo "Field can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("DELETE FROM member WHERE account_name=? AND name=?");
            $stmt->bind_param("ss", $toInsert, $toInsert1);
            $stmt->execute();
            echo "Event removal statement executed successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

echo "Add member to community:"
?>
<form action="admin_memberEdit.php" method="post">
    <input type="text" id="memberToAdd" name="memberToAdd">
    <input type="text" id="toCommunity" name="toCommunity">
    <input type="submit" value="Add Member" name="addMember">
</form>
<?php
echo "Remove member from community:"
?>
<form action="admin_memberEdit.php" method="post">
    <input type="text" id="memberToRemove" name="memberToRemove">
    <input type="text" id="fromCommunity" name="fromCommunity">
    <input type="submit" value="Remove Member" name="removeMember">
</form>
<table class="table table-striped">
    <thead class="thead-dark">
        <tr>
            <th>entry_no</th>
            <th>account_name</th>
            <th>name</th>
        </tr>

<?php
$table = "member";
$result = getAllFromTable($table);
while ($row = $result->fetch_assoc()) {
    echo sprintf('
                    <tr>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                    </tr>
                    ', $row["entry_no"], $row["account_name"], $row["name"]);
}
echo "<table>";
?>
    </thead>
</table>
