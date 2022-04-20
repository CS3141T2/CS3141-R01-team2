<?php
include '../tmt.php';
session_start();
$db = db();
if (true) {
    echo "Interests:";
    $table = "surveyQuestions";
    $result1 = getAllFromTable($table);
    if ($result1->num_rows > 0) {
        while ($row1 = $result1->fetch_assoc()) {
            $qNum = $row1["questionID"];
            echo "hobby{$qNum}";
            foreach($_POST["hobby{$qNum}"] as $interests) {
                echo $interests."\n";
            }
        }
    }
    //header("Location: /profile");
}

function getAllFromTable($table) {
    //connects to database
    //retrieves data and displays
    $db = db();
    $sql = "SELECT * FROM $table";
    $statement = $db->prepare($sql);
    $statement->execute();
    return $statement->get_result();
}

