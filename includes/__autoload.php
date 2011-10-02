<?php
function __autoload($className) {
	$__autoloadAbsolutePath = dirname(__FILE__);

	if (file_exists('includes/class.' . $className . '.php') === true) {
		require_once('includes/class.' . $className . '.php');
	}
	if (file_exists('libraries/class.' . $className . '.php') === true) {
		require_once('libraries/class.' . $className . '.php');
	}
}

?>