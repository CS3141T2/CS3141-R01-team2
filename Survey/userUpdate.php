<?php
include '../tmt.php';
session_start();
$db = db();
if (!isset($_SESSION["username"])) {
    header("Location: /login");
    die();
}
$user = $_SESSION["username"];
if (isset($_POST["No"])) {
    header("Location: /profile");
} else if (isset($_POST["Yes"])) {
    header("Location: /Survey");
}