<?php
require_once "config.php";

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if($path == '/assets/js/v0.1-dev.js')
{
	if(isset($_REQUEST['param']) && $_REQUEST['param'] == 'm-err')
	{
		header('Content-type: application/javascript');
		include "/assets/js/v0.1-dev.param.m-err.js";
	}
	else
	{
		header('Content-type: application/javascript');
		include "/assets/js/v0.1-dev.js";
	}
}
else if($path == '/assets/js/jquery.js')
{
	header('Content-type: application/javascript');
	include "/assets/js/jquery.js";
}
?>
