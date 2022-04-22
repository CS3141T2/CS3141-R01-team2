<?php
include '../tmt.php';
session_start();
$db = db();
if (true) {
    try {
        $stmt = $db->prepare("DELETE FROM interests WHERE user=?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
    } catch (Exception $e) {
        header("Location: /profile");
    }
    $username = $_SESSION["username"];
    $table = "surveyQuestions";
    $result1 = getAllFromTable($table);
    if ($result1->num_rows > 0) {
        while ($row1 = $result1->fetch_assoc()) {
            $qNum = $row1["questionID"];
            if (isset($_POST["hobby{$qNum}"])) {
                foreach($_POST["hobby{$qNum}"] as $interests)
                    if (!empty($interests)) {
                        try {
                            $db = db();
                            $stmt = $db->prepare("INSERT INTO interests VALUES (?, ?)");
                            $stmt->bind_param("ss", $username, $interests);
                            $stmt->execute();
                        } catch (Exception $e) {
                            header("Location: /profile");
                        }
                    }
            }
        }
    }
    header("Location: /profile");
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

