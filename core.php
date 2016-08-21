<?php
include "config.php";

use google\appengine\api\users\UserService;
use google\appengine\api\cloud_storage\CloudStorageTools;

function app_getCopyright()
{
	echo "Copyright &copy; ".COPYRIGHT_YEAR." ";
	author_getNameUrl();
	echo ", all rights reserved.\n";
}

function app_getLicense()
{
	echo "Distributed under the terms of the <a href=\"about/licenses\">".APP_LICENSE."</a>\n";
}

function author_getNameUrl()
{
	echo "<a href=\"".AUTHOR_URL."\" target=\"".APP_ID."\">".AUTHOR_NAME."</a>";
}

function author_getNameEmail()
{
	echo "<a href=\"mailto:".AUTHOR_EMAIL."\">".AUTHOR_NAME."</a>";
}

function host_getNameUrl()
{
	echo "<a href=\"".HOST_URL."\" target=\"".APP_ID."\">".HOST_NAME."</a>\n";
}

function getPath()
{
	return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}

function isUserLoggedIn()
{
	$user = UserService::getCurrentUser();

	if(!isset($user))
		return false;
	else
		return true;
}

function checkForUser()
{
	if(!isUserLoggedIn() && getPath() !== '/login')
		header('location: /login');
}

function isAdminLoggedIn()
{
	if(!isUserLoggedIn() || !UserService::isCurrentUserAdmin())
		return false;
	else
		return true;
}

function checkForAdmin()
{
	if(!isAdminLoggedIn() && getPath() !== '/adminonly' && getPath() !== '/login')
		header('location: /adminonly');
}

function getUserInfo()
{
	if(isUserLoggedIn())
	{
		$msg = "Welcome, ";

		if(isAdminLoggedIn())
			$msg .= "[ADMIN] %s!";
		else
			$msg .= "%s!";

		$msg .=" <a href=\"%s\">Sign out</a>.";

		echo sprintf($msg, UserService::getCurrentUser()->getNickname(),
			UserService::createLogoutUrl('/'));
	}
	else
		echo "<a href=\"/login\">Sign in</a>";
}

function getRelogBtn()
{
	$msg = "<a href=\"%s\">Sign in</a>";

	echo sprintf($msg, UserService::createLogoutUrl('/login'));
}

function gLoginBtn()
{
	$msg = "<a class=\"m-btn m-login g\" href=\"%s\">Sign in with Google</a>";

	echo sprintf($msg, UserService::createLoginUrl('/'));
}

function serveImg($path)
{
	$options = ['size' => 18, 'crop' => false, 'secure_url' => true];
	$image_file = "gs://".GS_BUCKET."/".$path;
	$image_url = CloudStorageTools::getImageServingUrl($image_file, $options);
	echo $image_url;
}
?>
