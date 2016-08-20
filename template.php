<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Margo: The Game | Template</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/v0.1-dev.css">
<script src="assets/js/jquery.js"></script>
<script src="assets/js/v0.1-dev.js"></script>
</head>

<body>
<header class="m-header">
	<canvas id="ribbon-bg"></canvas>
	<div class="m-title" data-italicize="margo">Margo: The Game</div>
</header>

<div class="m-wrapper">
	<div class="m-game">
		<canvas id="game-area"></canvas>
	</div>

	<footer class="m-footer">
		<div class="m-version">
			<span data-italicize="margo">Margo: The Game</span> v{v.Major}.{v.Minor}-{v.Build}
		</div>
		<div class="m-copy">
			Copyright &copy; 2016 <a href="//www.ghifari160.com" target="_blank">Ghifari160</a>, all rights reserved.
			Distributed under the terms of the <a href="/about/licenses">MIT License</a>.
		</div>
	</footer>
</div>
</body>
</html>
