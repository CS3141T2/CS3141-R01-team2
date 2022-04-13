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
    <title>Survey Editor; Tech Meets Tech</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>
<br>
<body>
    <div class="sidebar">
        <form action="index.php" method="post">
            <button type="submit" class="btn btn-primary">Go Back</button>
        </form>
    </div>
<?php
include 'utils.php';
if (isset($_POST["addQuestion"])) {
    $toInsert = $_POST["questionToAdd"];
    $view = 7;
    $table = "surveyQuestions";
    if ($toInsert == null) {
        echo "Field can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("INSERT INTO surveyQuestions(questionText) VALUES (?)");
            $stmt->bind_param("s", $toInsert);
            $stmt->execute();
            echo "Add question statement executed successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

if (isset($_POST["removeQuestion"])) {
    $toInsert = $_POST["questionToRemove"];
    if ($toInsert == null) {
        echo "Field can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("DELETE FROM surveyQuestions WHERE questionID=?");
            $stmt->bind_param("i", $toInsert);
            $stmt->execute();
            echo "Question removal statement executed successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

if (isset($_POST["addInterest"])) {
    $toInsert = $_POST["interestToAdd"];
    $toInsert1 = $_POST["questionID"];
    if ($toInsert == null || $toInsert1 == null) {
        echo "Fields can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("INSERT INTO surveyInterests VALUES (?, ?)");
            $stmt->bind_param("si", $toInsert, $toInsert1);
            $stmt->execute();
            echo "Interest add statement executed successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

if (isset($_POST["removeInterest"])) {
    $toInsert = $_POST["interestToRemove"];
    if ($toInsert == null) {
        echo "Field can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("DELETE FROM surveyInterests WHERE interest=?");
            $stmt->bind_param("s", $toInsert);
            $stmt->execute();
            echo "Interest removal statement executed successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

// Question management script
echo "Add Question:"
?>
<form action="admin_surveyEdit.php" method="post">
    <input type="text" id="questionToAdd" name="questionToAdd">
    <input type="submit" value="Add Question" name="addQuestion">
</form>
<?php
echo "Remove QuestionID:"
?>
<form action="admin_surveyEdit.php" method="post">
    <input type="text" id="questionToRemove" name="questionToRemove">
    <input type="submit" value="Remove Question" name="removeQuestion">
</form>
<?php
echo "Add Interest + QuestionID:"
?>
<form action="admin_surveyEdit.php" method="post">
    <input type="text" id="interestToAdd" name="interestToAdd">
    <input type="text" id="questionID" name="questionID">
    <input type="submit" value="Add Interest" name="addInterest">
</form>
<?php
echo "Remove Interest:"
?>
<form action="admin_surveyEdit.php" method="post">
    <input type="text" id="interestToRemove" name="interestToRemove">
    <input type="submit" value="Remove Interest" name="removeInterest">
</form>
<table class="table table-striped">
    <thead class="thead-dark">
        <tr>
            <th scope="col">questionID</th>
            <th scope="col">questionText</th>
        </tr>
    </thead>
    <tbody>
<?php
$table = "surveyQuestions";
$result = getAllFromTable($table);
while ($row = $result->fetch_assoc()) {
    echo sprintf('
         <tr>
              <th>%s</th>
              <th>%s</th>
         </tr>
         ', $row["questionID"], $row["questionText"]);
}
echo "<table>";
// Interest management script
?>
    </tbody>
</table>
    <br>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">interest</th>
                <th scope="col">questionAssoc</th>
            </tr>
        </thead>

<?php
$table = "surveyInterests";
$result = getAllFromTable($table);
while ($row = $result->fetch_assoc()) {
    echo sprintf('
        <tr>
            <th>%s</th>
            <th>%s</th>
        </tr>
        ', $row["interest"], $row["questionAssoc"]);
}
echo "<table>";
?>
    </table>
</body>
