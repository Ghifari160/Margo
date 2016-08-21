<?php
require_once "core.php";

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Margo: The Game | Template</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<?php endif; ?>
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
		<canvas id="game-area"></canvas>
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
		<div class="m-version">
			<span data-italicize="margo">Margo: The Game</span> v<?php echo VMAJOR.".".VMINOR."-".VBUILD; ?>

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
</body>
</html>
