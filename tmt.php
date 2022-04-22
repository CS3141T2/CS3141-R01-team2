<?php

/**
 * Makes a connection to the MySQL server.
 * @return mysqli
 */
function db(): mysqli
{
	$config = parse_ini_file("/home/techzrla/creds.ini");
	return new mysqli($config["host"], $config["user"], $config["pass"], $config["data"]);
}

/**
 * Returns the CSS Bootstrap string.
 * @return string
 * @deprecated use head_goodies() instead, as it contains the return of this function plus other good stuff
 * @see head_goodies()
 */
function bootstrap(): string
{
	return '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">';
}

function head_goodies(): string
{
	$head_lines = array(
		0 => '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">',
		1 => '<meta charset="utf-8">',
		2 => '<meta name="viewport" content="width=device-width,initial-scale=1">',
		3 => '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">',
		4 => '<link href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css" rel="stylesheet">',
		5 => '<script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>',
		6 => '<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">'
	);
	return implode('', $head_lines);
}

function gotoPage($page)
{
	$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	header("Location: https://${$_SERVER['HTTP_HOST']}$uri/$page)");
}

/**
 * Checks if the user is an admin.
 * @param String $user Current logged in user
 * @return bool
 */
function isAdmin(string $user): bool
{
	$db = db();
	$stmt = $db->prepare("SELECT * FROM account WHERE username = ? AND permission = 'admin'");
	$stmt->bind_param("s", $user);
	$stmt->execute();
	if ($stmt->get_result()->num_rows == 0) {
		return false;
	}
	return true;
}

/**
 * Generates HTML code to display a Material Design button that acts as a `submit` button.
 *
 * @param string $display
 * @param string $value the text to be displayed on the button
 * @param string $name the submit name
 * @param string $icon a Material Icon code
 * @param string $style feel free to apply some styles
 * @param string $id
 * @param bool $disabled
 * @return string the button as HTML
 */
function mat_but_submit(string $display, string $value, string $name, string $icon, string $style, string $id, bool $disabled): string
{
	if ($id == '') $id = $name;
	$id = str_replace(' ', '', $id);
	$id .= '-btn';

	$disabled_str = '';
	if ($disabled) $disabled_str = 'disabled';

	if ($display != '') {
		$display_str = $display;
	} else {
		$display_str = $value;
	}

	/** @noinspection HtmlUnknownAttribute */
	return sprintf(
		'<button type="submit" class="mdc-button mdc-button--raised" name="%s" id="%s" style="%s; color: #000000; background-color: #ffcd00;" value="%s" %s>
					<div class="mdc-button__ripple"></div>
					<i class="material-icons mdc-button__icon" aria-hidden="true">%s</i>
					%s
				</button>
				<script>
					mdc.ripple.MDCRipple.attachTo(document.querySelector("#%s"));
				</script>',
		$name, $id, $style, $value, $disabled_str, $icon, $display_str, $id
	);
}