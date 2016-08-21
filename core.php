<?php

include "config.php";

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

?>
