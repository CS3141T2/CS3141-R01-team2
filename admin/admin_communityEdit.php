<?php
include '/home/techzrla/tmt.php';
session_start();
$db = db();

// User is not logged in, go to login page
if ($_SESSION['username'] == null) {
	header("Location: /login");
	die();
} else {
	$db = db();
	$perm = "admin";
	$stmt = $db->prepare("SELECT * FROM account WHERE username = ? AND permission = ? ");
	$stmt->bind_param("ss", $_SESSION['username'], $perm);
	$stmt->execute();
	if ($stmt->get_result()->num_rows == 0) {
		header("Location: /dashboard");
		die();
	}
}
?>
<html lang="en-US">
<head>
	<title>Community Editor; Tech Meets Tech</title>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
	      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>
<body>
<form action="index.php" method="post">
	<input type="submit" value='Back' name='Back'>
</form>
<?php
include 'utils.php';
if (isset($_POST["addCommunity"])) {
	$toInsert = $_POST["communityToAdd"];
	$toInsert1 = $_POST["leaderToAdd"];
	$view = 2;
	$table = "community";
	if ($toInsert == null) {
		echo "Field can't be null";
	} else {
		try {
			$db = db();
			$stmt = $db->prepare("INSERT INTO community VALUES (?, ?)");
			$stmt->bind_param("ss", $toInsert, $toInsert1);
			$stmt->execute();
			echo "Community statement executed without errors";
		} catch (Exception $e) {
			echo 'Error: caught exception ', $e->getMessage(), '\n';
		}
	}
}

if (isset($_POST["removeCommunity"])) {
	$toInsert = $_POST["communityToRemove"];
	$view = 2;
	$table = "community";
	if ($toInsert == null) {
		echo "Field can't be null";
	} else {
		try {
			$db = db();
			$stmt = $db->prepare("DELETE FROM community WHERE name=?");
			$stmt->bind_param("s", $toInsert);
			$stmt->execute();
			echo "Community removal statement executed successfully";
		} catch (Exception $e) {
			echo 'Error: caught exception ', $e->getMessage(), '\n';
		}
	}
}

echo "Add Community + Leader:"
?>
<form action="admin_communityEdit.php" method="post">
	<input type="text" id="communityToAdd" name="communityToAdd">
	<input type="text" id="leaderToAdd" name="leaderToAdd">
	<input type="submit" value="Add Community" name="addCommunity">
</form>
<?php
echo "Remove Community:"
?>
<form action="admin_communityEdit.php" method="post">
	<input type="text" id="communityToRemove" name="communityToRemove">
	<input type="submit" value="Remove Community" name="removeCommunity">
</form>
<table class="table table-striped">
	<thead class="thead-dark">
	<tr>
		<th>name</th>
		<th>leader</th>
	</tr>

  <?php
  $table = "community";
  $result = getAllFromTable($table);
  while ($row = $result->fetch_assoc()) {
	  echo sprintf('
                    <tr>
                        <th>%s</th>
                        <th>%s</th>
                    </tr>
                    ', $row["name"], $row["leader"]);
  }
  echo "<table>";
  ?>
	</thead>
</table>
</body>
</html>