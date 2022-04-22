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

function userFriendsList(mysqli $db, array $user_info): string {
	$html = "<ul>";
	$stmt = $db->prepare("SELECT * FROM `friend` WHERE user1=?");
	$stmt->bind_param("s", $user_info["username"]);
	$stmt->execute();
	$result = $stmt->get_result();
	while ($friendship = $result->fetch_assoc()) {
			$html .= sprintf('<li><a href="index.php?username=%s">%s</a></li>', $friendship["user2"], $friendship["user2"]);
	}
	$html .= "</ul>";
	return $html;
}

function userSocials(array $user_info): string
{
	$html = "";
	if (isset($user_info["twitter_username"]) && $user_info["twitter_username"] != "") {
		$TWITTER_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>';
		$html .= sprintf("<span>%s <a href='https://twitter.com/%s'>%s</a></span><br>", $TWITTER_SVG, $user_info["twitter_username"], $user_info["twitter_username"]);
	}
	if (isset($user_info["snapchat_username"]) && $user_info["snapchat_username"] != "") {
		$SNAPCHAT_SVG = '<svg viewBox="1.99 .36 20.02 22.52" width="24" height="24" xmlns="http://www.w3.org/2000/svg"><path d="M21.8 16.99c-2.87-.48-4.15-3.4-4.2-3.53l-.01-.01a1.07 1.07 0 0 1-.1-.9c.19-.45.83-.65 1.25-.79.1-.03.2-.06.28-.1.76-.3.92-.6.91-.82a.66.66 0 0 0-.5-.54.95.95 0 0 0-.36-.07.76.76 0 0 0-.31.06 2.54 2.54 0 0 1-.96.27.82.82 0 0 1-.53-.18l.04-.53V9.8a10.1 10.1 0 0 0-.24-4.04 5.25 5.25 0 0 0-4.87-3.14h-.4a5.24 5.24 0 0 0-4.87 3.14 10.09 10.09 0 0 0-.24 4.03l.03.6a.85.85 0 0 1-.58.18 2.45 2.45 0 0 1-1.01-.27.57.57 0 0 0-.25-.05.83.83 0 0 0-.81.54c-.08.43.53.74.9.89l.29.1c.42.13 1.06.33 1.25.78a1.07 1.07 0 0 1-.1.9v.01a7.03 7.03 0 0 1-1.08 1.66A5.21 5.21 0 0 1 2.2 17a.24.24 0 0 0-.2.25.38.38 0 0 0 .03.13c.18.4 1.06.75 2.55.98.14.02.2.25.28.62l.12.47a.3.3 0 0 0 .32.22 2.48 2.48 0 0 0 .42-.06 5.53 5.53 0 0 1 1.12-.12 4.95 4.95 0 0 1 .8.06 3.88 3.88 0 0 1 1.54.79 4.44 4.44 0 0 0 2.7 1.06h.25a4.45 4.45 0 0 0 2.69-1.06 3.87 3.87 0 0 1 1.53-.79 4.97 4.97 0 0 1 .8-.06 5.6 5.6 0 0 1 1.13.12 2.4 2.4 0 0 0 .42.05h.03a.28.28 0 0 0 .3-.22 6.52 6.52 0 0 0 .1-.46c.09-.37.15-.6.29-.62 1.49-.23 2.37-.57 2.55-.98a.39.39 0 0 0 .03-.13.24.24 0 0 0-.2-.25z"/></svg>';
		$html .= sprintf("<span>%s %s</span><br>", $SNAPCHAT_SVG, $user_info["snapchat_username"]);
	}
	if (isset($user_info["instagram_username"]) && $user_info["instagram_username"] != "") {
		$INSTAGRAM_SVG = '<svg width="24" height="24" viewBox="0 0 256 256" id="Flat" xmlns="http://www.w3.org/2000/svg"><path d="M160,128a32,32,0,1,1-32-32A32.03667,32.03667,0,0,1,160,128Zm68-44v88a56.06353,56.06353,0,0,1-56,56H84a56.06353,56.06353,0,0,1-56-56V84A56.06353,56.06353,0,0,1,84,28h88A56.06353,56.06353,0,0,1,228,84Zm-52,44a48,48,0,1,0-48,48A48.05436,48.05436,0,0,0,176,128Zm16-52a12,12,0,1,0-12,12A12,12,0,0,0,192,76Z"/></svg>
';
		$html .= sprintf("<span>%s <a href='https://instagram.com/%s'>%s</a></span><br>", $INSTAGRAM_SVG, $user_info["instagram_username"], $user_info["instagram_username"]);
	}

	return $html;
}

