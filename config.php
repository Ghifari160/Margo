<?php
define('FRAMEWORK_VER', 1);

define('APP_ID', 'margothegame'); // App ID. If using Google Cloud Platform, enter Project ID
define('APP_NAME', 'Margo: The Game');
define('APP_URL', 'http://margo.beingthe.one/');
define('APP_URL_ALTERNATE', 'http://margo.ghifari160.com/');
define('APP_SUPPORT_EMAIL', 'margothegame@ghifari160.com');
define('APP_LICENSE', 'MIT License');

define('AUTHOR_NAME', 'UnCoded');
define('AUTHOR_EMAIL', 'margothegame@ghifari160.com');
define('AUTHOR_URL', '/about/UnCoded');
define('AUTHOR_URL_ALTERNATE', '');
define('AUTHOR_PORTOFOLIO', ''); // Author's portofolio URL

define('COPYRIGHT_YEAR', 2016);

define('VMAJOR', 1);
define('VMINOR', 0);
define('VBUILD', 'e85b907d-R2');

define('HOST_NAME', 'Google Cloud Platform');
define('HOST_URL', 'https://cloud.google.com/');

define('GS_BUCKET', 'margothegame.appspot.com');

define('GAPI_REDIR_PREFIX', '***');
define('GAPI_CLIENT_ID', '***');
define('GAPI_CLIENT_SECRET', '***');

define('ADMIN_DEF', false); // Strict mode: true, non-strict mode: false

function g_whitelist()
{
// Allowed perms: admin, whitelist, tester, violet
// strict mode: admin
// non-strict mode: admin, dev, tester
	$whitelist = array(
		'***' => array(
			'perms' => 'admin',
			'email' => 'ghi***@gmail.com'
		),
		'***' => array(
			'perms' => 'dev',
			'email' => 'gad***@s207.org'
		),
		'***' => array(
			'perms' => 'tester',
			'email' => 'dpa***@s207.org'
		),
		'***' => array(
			'perms' => 'tester',
			'email' => 'dro***@s207.org'
		),
		'***' => array(
			'perms' => 'violet',
			'email' => 'olym***@gmail.com'
		)
	);

	return $whitelist;
}
?>
