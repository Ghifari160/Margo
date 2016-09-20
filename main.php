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
<?php endif; ?>
<script src="https://apis.google.com/js/platform.js"></script>
</head>

<body>
<header class="m-header">
	<canvas id="ribbon-bg"></canvas>
	<div class="m-title" data-italicize="margo">Margo: The Game</div>
</header>

<div class="m-wrapper">
<?php
if($path == '/' || $path == '/game'):
?>
	<div class="m-game">
		<!-- <canvas id="game-area"></canvas> -->
		<form action="http://margo.dev.beingthe.one:8091/#" target="_self" method="POST" enctype="application/x-www-form-urlencoded">
			<div class="trivia">
				<div class="question">
					<div>The following is an excerpt from a book</div>
					<blockquote>
						Harry — yer a wizard.
					</blockquote>
					<div>What is the title of this book?</div>
				</div>
				<div class="answers" id="ffff">
					<div class="answer" id="3fb5">
						<div class="radio">
							<div class="outer-circle">
								<div class="inner-circle"></div>
							</div>
						</div>
						<div class="label">
							<i>Harry Potter and the Sorcerer's Stone</i>
						</div>
					</div>
					<div class="answer" id="feda">
						<div class="radio">
							<div class="outer-circle">
								<div class="inner-circle"></div>
							</div>
						</div>
						<div class="label">
							<i>Harry Potter and the Half Blood Prince</i>
						</div>
					</div>
					<div class="answer" id="afe9">
						<div class="radio">
							<div class="outer-circle">
								<div class="inner-circle"></div>
							</div>
						</div>
						<div class="label">
							<i>Harry Potter and the Order of the Phoenix</i>
						</div>
					</div>
					<div class="answer" id="99ea">
						<div class="radio">
							<div class="outer-circle">
								<div class="inner-circle"></div>
							</div>
						</div>
						<div class="label">
							<i>Nightfall</i>
						</div>
					</div>
					<input type="hidden" name="ffff" id="vffff">
				</div>
				<div class="submit">
					<div class="m-btn hidden">Next Question</div>
				</div>
			</div>
		</form>
	</div>
<?php
elseif($path == '/eros'):
?>
	<div class="m-game">
		<div id="yt-player"></div>
	</div>

	<script>
		var height = $(".m-game").height(),
			width = Math.round(height * 16 / 9);

		var tag = document.createElement('script');
		tag.src = "https://www.youtube.com/player_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		var player;
		function onYouTubePlayerAPIReady()
		{
			player = new YT.Player('yt-player',
			{
				width: width,
				height: height,
				videoId: 'EkmzbxiB7rg',
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
			<span class="m-title" data-italicize="margo">Margo: The Game</span> v<?php echo VMAJOR.".".VMINOR."-".VBUILD; ?>

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

<div class="m-modal"></div>
<div class="m-popup"></div>
</body>
</html>
