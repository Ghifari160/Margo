<?php
require_once "core.php";

$fullPath = getPath();

if($fullPath == "/app"):
	header('location: /invalid+url');
elseif($fullPath == "/browserconfig.xml"):
	header('Content-type: text/xml');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
?>
<browserconfig>
	<msapplication>
		<tile>
			<square70x70logo src=<?php echo "\"".getImage("Logo-1240.png", 70)."\""; ?>/>
			<square150x150logo src=<?php echo "\"".getImage("Logo-1240.png", 150)."\""; ?>/>
			<square310x310logo src=<?php echo "\"".getImage("Logo-1240.png", 310)."\""; ?>/>
			<wide310x150logo src=<?php echo "\"".getImage("Logo-1240.png", 150)."\""; ?>/>
			<TileColor>#ffffff</TileColor>
		</tile>
	</msapplication>
</browserconfig>
<?php
else:
	$path = substr($fullPath, 5);

	if($path == "hangouts.xml"):
		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
?>

<Module>
	<ModulePrefs title=<?php echo "\"".APP_NAME."\""; ?>>
		<Require features="rpc"/>
		<Require features="views"/>
	</ModulePrefs>
	<Content type="html">
		<![CDATA[
			<script src="https://plus.google.com/hangouts/_/api/v1/hangout.js"></script>
		]]>
	</Content>
</Module>
<?php
	endif;
endif;
?>
