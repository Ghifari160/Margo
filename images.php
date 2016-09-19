<?php
require_once "core.php";

use google\appengine\api\cloud_storage\CloudStorageTools;

$fullPath = getPath();

if($fullPath == "/assets/images" || $fullPath == "/assets/images/")
	header('location: /adminonly');
else
{
	$path = substr($fullPath, 15);
	$rPath = explode("/", $path);

	$file = $rPath[0];
	$size = (int)$rPath[1];
	$crop = false;

	if($rPath[2] == "crop")
		$crop = true;

	$options = [
		'size' => $size,
		'crop' => $crop,
		'secure_url' => true
	];

	$image = gs_getBucketPath($file);
	$url = CloudStorageTools::getImageServingUrl($image, $options);

	header('location: '.$url.'');
}
?>
