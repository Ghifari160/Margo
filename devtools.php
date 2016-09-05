<?php
require_once "core.php";

if(!isAdminLoggedIn())
	header('location: /adminonly');

if(getPath() == "/dev.phpinfo")
	phpinfo();
else if(getPath() == "/dev.gperm")
{
	echo gGetUserPerm("ghifari160@gmail.com", 1);
}
else if(getPath() == "/dev.gsession")
{
	var_dump(gapi_getPeople($_COOKIE['mgame_g_access_token']));
}
else if(getPath() == "/dev")
	header('location: /');
else
	header('location: /');
?>
