<?php

/**
 * Generates a random 6-digit string.
 * @return string
 */
function emailCode(): string
{
	$str = "";
	for ($i = 0; $i < 6; $i++) {
		$str .= rand(0, 9);
	}
	return $str;
}
