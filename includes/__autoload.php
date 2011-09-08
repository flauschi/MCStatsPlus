<?php
function __autoload( $className ) {
	$__autoloadAbsolutePath = dirname(__FILE__);

	if (file_exists('includes/class.' . $className . '.php') === true) {
		require_once('includes/class.' . $className . '.php');
	}
	if (file_exists('plugins/' . $className . '.php') === true) {
		require_once('plugins/' . $className . '.php');
	}
}

?>