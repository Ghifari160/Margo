<?php
session_start();

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

function gs_getBucketPath($path)
{
	return "gs://".GS_BUCKET."/".$path;
}

function gapi_createRedirectUri()
{
	return urlencode(GAPI_REDIR_PREFIX."/auth?p=g");
}

function gapi_createLoginState()
{
	$state = sha1(openssl_random_pseudo_bytes(1024));
	$_SESSION['gapi_lstate'] = $state;

	return $state;
}

function gapi_createLoginUrl()
{
	$gapi_backend = "https://accounts.google.com/o/oauth2/v2/auth";

	$params = array(
		'response_type' => "code",
		'client_id' => urlencode(GAPI_CLIENT_ID),
		'redirect_uri' => gapi_createRedirectUri(),
		'scope' => urlencode("email https://www.googleapis.com/auth/userinfo.profile"),
		'state' => gapi_createLoginState(),
		'access_type' => 'online',
		'prompt' => urlencode('select_account')
	);

	foreach($params as $key=>$value)
	{
		$param_string .= $key.'='.$value.'&';
	}
	rtrim($param_string, '&');

	return $gapi_backend."?".$param_string;
}

function gapi_createLogoutUrl($redir = false)
{
	$url = "/auth?p=logout";
	if($redir !== false)
		$url .= "&redir=".urlencode($redir);

	return $url;
}

function gapi_getUserInfo($accessToken)
{
	if(isset($_SESSION['mgame_guser_'.$accessToken]))
	{
		$json = base64_decode($_SESSION['mgame_guser_'.$accessToken]);
		$rJson = json_decode($json, true);

		return $rJson;
	}
	else
	{
		$gapi_backend = "https://www.googleapis.com/oauth2/v3/tokeninfo";
		$params = array(
			'access_token' => $accessToken
		);

		foreach($params as $key=>$value)
		{
			$param_string .= $key.'='.$value.'&';
		}
		$param_string = rtrim($param_string, '&');

		$c = curl_init();

		curl_setopt($c, CURLOPT_URL, $gapi_backend);
		curl_setopt($c, CURLOPT_POST, count($params));
		curl_setopt($c, CURLOPT_POSTFIELDS, $param_string);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

		$results = curl_exec($c);
		curl_close($c);

		$rJson = json_decode($results, true);

		if(isset($rJson['aud']) && $rJson['aud'] == GAPI_CLIENT_ID)
		{
			$_SESSION['mgame_guser_'.$accessToken] = base64_encode($results);

			return $rJson;
		}
		else
			return false;
	}
}

function gapi_getPeople($accessToken)
{
	if(isset($_SESSION['mgame_gpeople_'.$accessToken]))
	{
		$json = base64_decode($_SESSION['mgame_gpeople_'.$accessToken]);
		$rJson = json_decode($json, true);

		return $rJson;
	}
	else
	{
		$gapi_backend = "https://www.googleapis.com/oauth2/v1/userinfo";
		$gapi_backend .= "?access_token=".$accessToken;

		$c = curl_init();

		curl_setopt($c, CURLOPT_URL, $gapi_backend);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_VERBOSE, true);

		$results = curl_exec($c);
		curl_close($c);

		$rJson = json_decode($results, true);

		if(isset($rJson['given_name']))
		{
			$_SESSION['mgame_gpeople_'.$accessToken] = base64_encode($results);

			return $rJson;
		}
		else
			return false;
	}
}

function gapi_isUserLoggedIn()
{
	if(isset($_COOKIE['mgame_g_access_token'])
		&& $_COOKIE['mgame_g_access_token'] !== ""
		&& $_COOKIE['mgame_g_access_token'] !== NULL)
	{
		if(gapi_getUserInfo($_COOKIE['mgame_g_access_token']) !== false)
			return true;
		else
		{
			$_COOKIE['mgame_g_access_token'] = "";
			unset($_COOKIE['mgame_g_access_token']);
			setcookie('mgame_g_access_token', '', time() - 86400, '/');

			return false;
		}
	}
	else
		return false;
}

function app_cookieCleanup()
{
	if(!isUserLoggedIn())
	{
		if(isset($_COOKIE['mgame_g_access_token']))
		{
			$_COOKIE['mgame_g_access_token'] = "";
			unset($_COOKIE['mgame_g_access_token']);
			setcookie('mgame_g_access_token', '', time() - 86400, '/');
		}

		if(isset($_COOKIE['mgame_user']))
		{
			$_COOKIE['mgame_user'] = "";
			unset($_COOKIE['mgame_user']);
			setcookie('mgame_user', '', time() - 86400, '/');
		}
	}
}

function gCheckWhitelist($rJson)
{
	$whitelist = g_whitelist();

	foreach($whitelist as $key=>$a)
	{
		if($rJson['sub'] == $key)
			return true;
		else if($rJson['email'] == $a['email'])
		{
			$l = $key."=".$rJson['sub'];
			// syslog(LOG_INFO, "prelog");
			syslog(LOG_INFO, $l);
			// syslog(LOG_INFO, "postlog");
			return true;
		}
	}

	syslog(LOG_WARNING, "Unauthorized user:");
	syslog(LOG_WARNING, substr(json_encode($rJson), 0, 5000));
	return false;
}

function gGetUserPerm($eid, $mode = 0)
{
	$whitelist = g_whitelist();
	$perm = "";
	$perm_formatted = "";

	if($mode == 0)
	{
		$perm = $whitelist[$eid]['perms'];
	}
	else if($mode == 1)
	{
		foreach($whitelist as $key=>$value)
		{
			if($value['email'] == $eid)
			{
				$perm = $value['perms'];
			}
		}
	}

	switch($perm)
	{
		case "admin":
			$perm_formatted = "ADMIN";
			break;

		case "dev":
			$perm_formatted = "DEVELOPER";
			break;

		case "tester":
			$perm_formatted = "TESTER";
			break;

		case "violet":
			$perm_formatted = "VIOLET";
			break;
	}

	return $perm_formatted;
}

function getUserEndPoint()
{
	if(isset($_COOKIE['mgame_user']) && $_COOKIE['mgame_user'] == true)
	{
		if(gapi_isUserLoggedIn())
			return "google";
		else
			return "not logged in";
	}
	else
		return "not logged in";
}

function isUserLoggedIn()
{
	$bool = false;

	switch(getUserEndPoint())
	{
		case "google":
			$bool = true;
			break;

		default:
			$bool = false;
			break;
	}

	return $bool;
}

function isAdminLoggedIn($strict = ADMIN_DEF)
{
	$bool = false;
	$ep = getUserEndPoint();

	if($ep == "google")
	{
		$whitelist = g_whitelist();
		$user_rJson = gapi_getUserInfo($_COOKIE['mgame_g_access_token']);
		$perm = "";

		foreach($whitelist as $key=>$value)
		{
			if($key == $user_rJson['sub'])
			{
				if($strict && $value['perms'] == "admin")
					$bool = true;
				else if($strict == false)
				{
					if($value['perms'] == "admin"
						|| $value['perms'] == 'dev'
						|| $value['perms'] == 'tester')
						$bool = true;
				}
			}
			else if($value['email'] == $user_rJson['email'])
			{
				if($strict && $value['perms'] == "admin")
					$bool = true;
				else if($strict == false)
				{
					if($value['perms'] == "admin"
						|| $value['perms'] == 'dev'
						|| $value['perms'] == 'tester')
						$bool = true;
				}
			}
		}
	}

	return $bool;
}

function getUserInfo()
{
	if(isUserLoggedIn())
	{
		$str = "Welcome, [%s] %s! %s";
		$usr_lvl = "";
		$usr_nm = "";
		$logout = "";
		$endpoint = getUserEndPoint();

		if($endpoint == "google")
		{
			$usr = gapi_getUserInfo($_COOKIE['mgame_g_access_token']);
			$usr_lvl = gGetUserPerm($usr['email'], 1);
			$people =
				gapi_getPeople($_COOKIE['mgame_g_access_token']);
			if($people !== false)
				$usr_nm = $people['given_name'];
			else
				$usr_nm = $usr['email'];
			$logout = gLogoutBtn();
		}

		echo sprintf($str, $usr_lvl, $usr_nm, $logout);
	}
	else
		echo "<a href=\"/login\">Sign in</a>";
}

function getPath()
{
	return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}

function getRelogBtn()
{
	$msg = "<a href=\"%s\">Sign in</a>";

	echo sprintf($msg, gapi_createLogoutUrl("/login"));
}

function gLoginBtn()
{
	$msg = "<a class=\"m-btn m-login g\" href=\"%s\">Sign in with Google</a>";

	echo sprintf($msg, gapi_createLoginUrl());
}

function gLogoutBtn()
{
	$msg = "<a href=\"%s\">Sign out</a>";

	return sprintf($msg, gapi_createLogoutUrl());
}

function serveImg($path)
{
	$options = ['size' => 18, 'crop' => false, 'secure_url' => true];
	$image_file = "gs://".GS_BUCKET."/".$path;
	$image_url = CloudStorageTools::getImageServingUrl($image_file, $options);
	echo $image_url;
}

function decodeLoginError($param)
{
	$msg = "Unknown error";

	switch($param)
	{
		case 0:
			$msg = "Invalid user endpoint";
			break;

		case 1:
			$msg = "Invalid login path for endpoint 'google'";
			break;

		case 2:
			$msg = "User not on the whitelist for endpoint 'google'";
			break;

		case 3:
			$msg = "Invalid access token for endpoint 'google'";
			break;

		case 4:
			$msg = "Invalid login state for endpoint 'google'";
			break;

		case 5:
			$msg = "Endpoint error for 'google'";
			break;

		default:
	}

	echo $msg.".";
}
?>
