<?php
require_once ('includes/__autoload.php');
require_once('config.php');

//set_exception_handler(array('Error', 'defaultHandleError'));

$app = MCStatsPlus::instance();
$app->main();

?>