<?php
define('FRAMEWORK_VER', 1);

define('APP_ID', 'margothegame'); // App ID. If using Google Cloud Platform, enter Project ID
define('APP_NAME', 'Margo: The Game');
define('APP_URL', 'http://margo.beingthe.one/');
define('APP_URL_ALTERNATE', 'http://margo.ghifari160.com/');
define('APP_SUPPORT_EMAIL', 'business@ghifari160.com');
define('APP_LICENSE', 'MIT License');

define('AUTHOR_NAME', 'Ghifari160');
define('AUTHOR_EMAIL', 'business@ghifari160.com');
define('AUTHOR_URL', 'https://www.github.com/Ghifari160');
define('AUTHOR_URL_ALTERNATE', 'http://www.ghifari160.com/');
define('AUTHOR_PORTOFOLIO', ''); // Author's portofolio URL

define('COPYRIGHT_YEAR', 2016);

define('VMAJOR', 0);
define('VMINOR', 1);
define('VBUILD', '2441');

define('HOST_NAME', 'Google Cloud Platform');
define('HOST_URL', 'https://cloud.google.com/');

define('GS_BUCKET', 'margothegame.appspot.com');

// Google API informations has been redacted from this commit.
define('GAPI_REDIR_PREFIX', '');
define('GAPI_CLIENT_ID', '');
define('GAPI_CLIENT_SECRET', '');

define('ADMIN_DEF', false); // Strict mode: true, non-strict mode: false

function g_whitelist()
{
// Allowed perms: admin, whitelist, tester, violet
// strict mode: admin
// non-strict mode: admin, dev, tester

// Whitelist entry has been redacted from this commit
	$whitelist = array(
		'***' => array(
			'perms' => 'admin',
			'email' => '***@gmail.com'
		),
		'temp_1' => array(
			'perms' => 'dev',
			'email' => '***@s207.org'
		),
		'temp_2' => array(
			'perms' => 'tester',
			'email' => '***@s207.org'
		),
		'temp_3' => array(
			'perms' => 'tester',
			'email' => '***@s207.org'
		),
		'temp_4' => array(
			'perms' => 'violet',
			'email' => '***@gmail.com'
		)
	);

	return $whitelist;
}
?>
