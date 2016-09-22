<?php
require_once "core.php";

function loginErr($param)
{
	header('location: /e?p='.$param.'');
}

function verArr($str, $arr)
{
	$p = false;

	for($i = 0; $i < count($arr); $i++)
	{
		if($str == $arr[$i])
		{
			$p = $i;
		}
	}

	return $p;
}

if(!isset($_REQUEST['p']) || $_REQUEST['p'] == "" || $_REQUEST['p'] == NULL)
	loginErr(0);
else if($_REQUEST['p'] == "g")
{
	$whitelist = array(
		'ids' => array(
			null
		),
		'emails' => array(
			'ghifari160@gmail.com'
		),
		'perms' => array(
			'admin'
		)
	);
	$blacklist = array();

	if(!isset($_REQUEST['state'])
		|| $_REQUEST['state'] == "" || $_REQUEST['state'] == NULL
		|| !isset($_REQUEST['code'])
		|| $_REQUEST['code'] == "" || $_REQUEST['code'] == NULL)
		loginErr(1);
	else
	{
		if($_REQUEST['state'] == $_SESSION['gapi_lstate'])
		{
			unset($_SESSION['gapi_lstate']);

			$gapi_backend = "https://www.googleapis.com/oauth2/v4/token";
			$redirUrl = "/";

			$params = array(
				'code' => urlencode($_REQUEST['code']),
				'client_id' => urlencode(GAPI_CLIENT_ID),
				'client_secret' => urlencode(GAPI_CLIENT_SECRET),
				'redirect_uri' => gapi_createRedirectUri(),
				'grant_type' => urlencode("authorization_code")
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

			$results = rtrim($results, '1');
			$rJson = json_decode($results, true);

			if(($user = gapi_getUserInfo($rJson['access_token'])) !== false)
			{
				// if(gCheckWhitelist($user))
				// {
					setcookie('mgame_user', true, time() + $rJson['expires_in'],
						'/');
					setcookie('mgame_g_access_token', $rJson['access_token'],
						time() + $rJson['expires_in'], '/');

					if(isset($_SESSION['login_redir']))
					{
						$redirUrl = base64_decode($_SESSION['login_redir']);
						unset($_SESSION['login_redir']);
					}

					header('location: '.$redirUrl.'');
				// }
				// else
				// 	loginErr(2);
			}
			else
				loginErr(3);
		}
		else
			loginErr(4);
	}
}
else if($_REQUEST['p'] == "logout")
{
	$ep = getUserEndPoint();

	if($ep == "google")
	{
		$gapi_backend = "https://accounts.google.com/o/oauth2/revoke";

		$params = array(
			'token' => $_COOKIE['mgame_g_access_token']
		);

		foreach($params as $key=>$value)
		{
			$param_string .= $key."=".$value."&";
		}
		$param_string = rtrim($param_string, '&');

		$c = curl_init();

		curl_setopt($c, CURLOPT_URL, $gapi_backend);
		curl_setopt($c, CURLOPT_POST, count($params));
		curl_setopt($c, CURLOPT_POSTFIELDS, $param_string);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

		$results = curl_exec($c);
		curl_close($c);

		if($results == "{}")
		{
			$redir = "/";

			$_COOKIE['mgame_g_access_token'] = "";
			$_COOKIE['mgame_user'] = "";
			unset($_SESSION['mgame_guser_'.$_COOKIE['mgame_g_access_token']],
				$_SESSION['mgame_gpeople_'.$_COOKIE['mgame_g_access_token']],
				$_COOKIE['mgame_user'], $_COOKIE['mgame_g_access_token']);

			setcookie('mgame_user', '', time() - 86400, '/');
			setcookie('mgame_g_access_token', '', time() - 86400, '/');

			if(isset($_REQUEST['redir']) && $_REQUEST['redir'] !== "" &&
				$_REQUEST['redir'] !== NULL)
			{
				$redir = urldecode($_REQUEST['redir']);
			}

			header('location: '.$redir.'');
		}
		else
			loginErr(5);
	}
	else
	{
		$redir = "/";

		if(isset($_REQUEST['redir']) && $_REQUEST['redir'] !== "" &&
			$_REQUEST['redir'] !== NULL)
		{
			$redir = urldecode($_REQUEST['redir']);
		}

		header('location: '.$redir.'');
	}
}
else
	loginErr(1);
?>
