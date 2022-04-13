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

function userSocials(array $user_info): string
{
	if (isset($user_info["twitter_username"]) && $user_info["twitter_username"] != "") {
		$TWITTER_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>';
		return sprintf("<span>%s <a href='https://twitter.com/%s'>%s</a></span>", $TWITTER_SVG, $user_info["twitter_username"], $user_info["twitter_username"]);
	} else {
		return "";
	}
}

