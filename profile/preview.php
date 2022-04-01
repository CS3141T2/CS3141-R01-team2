<?php
include '/home/techzrla/tmt.php';
include "utils.php";
session_start();
$db = db();
$username_to_search = $_GET["username"];

$stmt = $db->prepare("SELECT * FROM `account` NATURAL JOIN `Identities` WHERE `username`=?");
$stmt->bind_param("s", $username_to_search);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();
?>


<html lang="en-US">
<head>
	<?php echo bootstrap(); ?>
	<title>&mdash; Tech Meets Tech</title>
</head>
<div class="container rounded shadow">
	<div style="display:flex; padding: 20px">
		<div style="display: flex">
			<img src="https://via.placeholder.com/150" style="width: 150px; margin:auto;" class="rounded-circle">
		</div>
		<div style="display: inline-block; margin-left: 20px; width: 500px;">
			<h1><?php echo $user_info["name"]; ?></h1>
			<p><em><?php echo $user_info["year"]; ?><sup><?php echo numberSuffix($user_info["year"]); ?></sup> year
					&mdash; <?php echo $user_info["major"] ?></em></p>
			<p><?php echo $user_info["profile_description"]; ?></p>
		</div>
	</div>


	<!--	<div class="col">-->
	<!--		<div class="row" style="width: 800px;">-->
	<!--			<div>-->
	<!--				<img src="https://via.placeholder.com/150" style="width: 150px; margin:auto;" class="rounded-circle">-->
	<!--			</div>-->
	<!--			<div class="col">-->
	<!--				<h1>Jacob Wysko</h1>-->
	<!--				<p><em>3<sup>rd</sup> year &mdash; Geospatial Engineering</em></p>-->
	<!--				</div>-->
	<!--		</div>-->
	<!--	</div>-->
</div>


</html>