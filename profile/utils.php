<?php

/**
 * Determines the suffix that should follow a given number. For example, 1 -> "st"; 2 -> "nd"; etc.
 *
 * @param $num int the number
 * @return string the suffix
 */
function numberSuffix(int $num): string
{
	if ($num % 100 >= 10 && $num % 100 <= 19) { // "tenth" to "nineteenth" are all "th"
		return "th";
	} else { // standard rules apply
		switch ($num % 10) {
			case 1:
				return "st";
			case 2:
				return "nd";
			case 3:
				return "rd";
			default:
				return "th";
		}
	}
}

/**
 * Collects information about a user and returns it as an assoc-array. If the user does not have a row in the `account`
 * table, the return is null.
 *
 * @param $db mysqli connection to database
 * @param $username string the user's username
 * @return array the user's info
 */
function getUserInfo(mysqli $db, string $username): ?array
{
	$stmt = $db->prepare("SELECT * FROM `account` NATURAL JOIN `Identities` WHERE `username`=?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_assoc();
}

/**
 * Generates HTML that displays a student's initials in their "profile picture".
 *
 * @param $user_info array the user's info
 * @return string the profile picture
 */
function defaultProfilePictureHTML(array $user_info): string
{
	/* Given the user's name, determine their initials */
	$name_parts = explode(" ", $user_info["name"]);
	$initials = "";
	for ($i = 0; $i < count($name_parts); $i++) {
		$initials .= substr($name_parts[$i], 0, 1); // Extract first letter
	}
	$color = "#";
	if ($user_info["color"] == null) {
		$color = "cadetblue";
	} else {
		$color .= $user_info["color"];
	}

	return sprintf('
	<div style="display: flex">
		<div style="width: 150px; height: 150px; background-color: %s; margin: auto; display:flex; justify-content: center; border-radius: 75px">
			<div style="display: flex; justify-content: center; align-content: center; flex-direction: column">
				<span style="font-size: xxx-large; color: whitesmoke">%s</span>
			</div>
		</div>
	</div>
	', $color, $initials);
}

/**
 * Generates HTML for displaying a student's "title". It contains their major and year.
 *
 * @param $user_info array the user's info
 * @return string their year and major nicely formatted
 */
function userTitleHTML(array $user_info): string
{
	$output = "<em>";

	$year = $user_info["year"];
	$major = $user_info["major"];

	/* Add year if it is recorded */
	if ($year != "" && $year != null) {
		$output .= sprintf('%d<sup>%s</sup> year', $year, numberSuffix($year));
	}

	/* Add major if it is recorded */
	if ($major != "" && $major != null) {
		if ($output != "<em>") { // Put an em-dash if we have added their year as well
			$output .= " &mdash; ";
		}
		$output .= $major;
	}

	/* If the user doesn't have a year and major specified, be generic asf */
	if ($output == "<em>") {
		$output .= "Michigan Tech Student";
	}

	return $output . "</em>";
}

/**
 * Gets a list of interests a given user is interested in, and formats it nicely.
 *
 * @param mysqli $db connection to the database
 * @param array $user_info the user's info
 * @return string an HTML unordered list containing each of their interests
 */
function userInterestsList(mysqli $db, array $user_info): string
{
	$html = "<ul>";
	$stmt = $db->prepare("SELECT * FROM `interests` WHERE USER=?");
	$stmt->bind_param("s", $user_info["username"]);
	$stmt->execute();
	$result = $stmt->get_result();
	while ($interest = $result->fetch_assoc()) {
		$html .= sprintf('<li>%s</li>', $interest["interest"]);
	}
	return $html . "</ul>";
}

/**
 * Gets a list of communities a given user is a member of, and formats it nicely.
 *
 * @param mysqli $db connection to the database
 * @param array $user_info the user's info
 * @return string an HTML unordered list containing each of their memberships
 */
function userMembershipsList(mysqli $db, array $user_info): string
{
	$html = "<ul>";
	$stmt = $db->prepare("SELECT * FROM `member` WHERE account_name=?");
	$stmt->bind_param("s", $user_info["username"]);
	$stmt->execute();
	$result = $stmt->get_result();
	while ($interest = $result->fetch_assoc()) {
		$html .= sprintf('<li>%s</li>', $interest["name"]);
	}
	return $html . "</ul>";
}