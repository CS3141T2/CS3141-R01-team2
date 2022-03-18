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
 */
function bootstrap(): string
{
	return '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">';
}