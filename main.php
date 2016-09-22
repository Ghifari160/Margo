<?php
require_once "core.php";

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

app_cookieCleanup();

if(!isUserLoggedIn() && $path !== "/login" && $path !== "/adminonly"
	&& $path !== "/e")
	header('location: '.createLoginUrl($path).'');
else if(!isAdminLoggedIn() && $path !== "/login" && $path !== "/adminonly"
	&& $path !== "/e")
	header('location: /adminonly');
else if(isUserLoggedIn() && $path == "/login")
{
	if(!isset($_REQUEST['force']) || $_REQUEST['force'] !== "1")
		header('location: /');
}
else if(isAdminLoggedIn() && $path == "/adminonly")
{
	if(!isset($_REQUEST['force']) || $_REQUEST['force'] !== "1")
		header('location: /');
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title data-italicize="margo">Margo: The Game<?php getExtraTitle(); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php app_getFavicons(); ?>
<link rel="stylesheet" href="/assets/css/v0.1-dev.css">
<script src="/assets/js/jquery.js?ver=dev"></script>
<?php
if($path == '/' || $path == '/game'):
?>
<script src="/assets/js/v0.1-dev.js?ver=dev"></script>
<?php
else:
?>
<script src="/assets/js/v0.1-dev.js?param=m-err&ver=dev"></script>
<?php
endif;
if($path == '/eros'):
?>
<script src="https://apis.google.com/js/api.js"></script>
<?php
elseif($path == "/game" || $path == "/"):
?>
<?php endif; ?>
<script src="https://apis.google.com/js/platform.js"></script>
</head>

<body>
<div class="m-modal" data-mgame="visible"></div>
<div class="m-popup" data-state="wait" data-mgame="visible">
	<div class="title">Please Wait</div>
	<div class="content">Loading page...</div>
	<div class="buttons"></div>
	<script>readjustPopupDiv();</script>
</div>

<header class="m-header">
	<canvas id="ribbon-bg"></canvas>
	<div class="m-title" data-italicize="margo">Margo: The Game</div>
</header>

<div class="m-wrapper">
<?php
if($path == '/' || $path == '/game'):
?>
	<audio id="audio-1" src="https://storage.googleapis.com/margothegame.appspot.com/audio/Overworld.mp3"></audio>
	<audio id="audio-2" src="https://storage.googleapis.com/margothegame.appspot.com/audio/Pixelland.mp3"></audio>
<?php
elseif($path == '/eros'):
?>
	<div class="m-game" data-mgame="visible">
		<div id="yt-player"></div>
	</div>

	<script>
		var height = 575,
			width = Math.round(height * 16 / 9);

		var tag = document.createElement('script');
		tag.src = "https://www.youtube.com/player_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		var vidId = "";

		$.ajax({
			url: "https://api.github.com/gists/ef1ba3355d0abcf8641f04ab01223f3b",
			type: "GET",
			dataType: 'jsonp',
			success: function(data)
			{
				$('.m-game').attr('data-id',
					data.data.files['eros.gdata'].content);

				vidId = data.data.files['eros.gdata'].content;
			},
			error: function(data)
			{
				toggleWait('Reloading page...');
				location.href = '/e?p=8';
				console.log(data);
			}
		})

		var player;
		function onYouTubePlayerAPIReady()
		{
			player = new YT.Player('yt-player',
			{
				width: width,
				height: height,
				// videoId: 'M7lc1UVf-VE',
				events:
				{
					'onReady': onPlayerReady,
					'onStateChange': onPlayerStateChange
				},
				playerVars:
				{
					'origin': "<?php echo GAPI_REDIR_PREFIX; ?>",
					'controls': 0,
					'disablekb': 0,
					'fs': 0,
					'iv_load_policy': 3,
					'rel': 0,
					'modestbranding': 1,
					'enablejsapi': 1,
					'showinfo': 0
				}
			});
		}

		function onPlayerReady(event)
		{
			player.loadVideoById(vidId);
			toggleWait();
			event.target.playVideo();
		}

		function onPlayerStateChange(event)
		{
			if(event.data == YT.PlayerState.ENDED)
				toggleErosResponse();
		}
	</script>
<?php
elseif($path == '/login'):
	if(isset($_REQUEST['redir']) && $_REQUEST['redir'] !== ""
		&& $_REQUEST['redir'] !== NULL):
		$_SESSION['login_redir'] = base64_encode(urldecode($_REQUEST['redir']));
	endif;
?>
	<div class="m-ar">
		<?php gLoginBtn(); ?>

	</div>
<?php
elseif($path == '/adminonly'):
?>
	<div class="m-err">
		<div class="m-err large">Forbidden!</div>
		<div class="m-err msg">
			Admin, developers, and testers only. <?php getRelogBtn(); ?> with another account.
		</div>
	</div>
<?php
elseif($path == '/e' && isset($_REQUEST['p']) && $_REQUEST['p'] !== "" &&
	$_REQUEST['p'] !== NULL):
?>
	<div class="m-err">
		<div class="m-err large">Login Error!</div>
		<div class="m-err msg">
			<?php decodeLoginError($_REQUEST['p']); ?>. Sorry for the inconvinience.
			<?php getRelogBtn(); ?> with another account.
		</div>
	</div>
<?php
elseif($path == "/testaudio"):
?>
	<div class="m-game" data-mgame="visible">
		<audio id="audio-1" src="https://storage.googleapis.com/margothegame.appspot.com/audio/Overworld.mp3"></audio>
		<audio id="audio-2" src="https://storage.googleapis.com/margothegame.appspot.com/audio/Pixelland.mp3"></audio>
		<script>
		$('#audio-1')[0].onended = function()
		{
			$('#audio-2')[0].play();
		}

		$('#audio-2')[0].onended = function()
		{
			$('#audio-1')[0].play();
		}

		$('#audio-1')[0].play();
		</script>
	</div>
<?php
else:
?>
	<div class="m-err">
		<div class="m-err large">Error!</div>
		<div class="m-err msg">
			Invalid URL. This error has been recorded. Sorry for the inconvinience.
		</div>
		<div class="m-err link"><a href="/">Return to home</a></div>
	</div>
<?php endif; ?>

	<footer class="m-footer">
		<div class="m-acc" data-m-param="user-info">
			<?php getUserInfo(); ?>

		</div>
		<div class="m-version">
			<a href="/about"><span class="m-title" data-italicize="margo">Margo: The Game</span></a> v<?php echo VMAJOR.".".VMINOR."-".VBUILD; ?>

		</div>
		<div class="m-copy">
			<?php app_getCopyright(); ?>
			<?php app_getLicense(); ?>
		</div>
		<div class="m-host">
			Hosted on <?php host_getNameUrl(); ?>
		</div>
	</footer>
</div>
<?php if($path !== "/eros" && $path !== "/game" && $path !== "/"): ?>
<script>toggleWait('');</script>
<?php endif; ?></body>
</html>
