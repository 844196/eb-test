<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>eb-test</title>
</head>
<body>
	<?php
		$env = getenv('FOO_BAR');
		$secret = file_get_contents('/opt/app/secret');
	?>
	<pre>
<code>env (FOO_BAR):
<?= $env . PHP_EOL ?>

file (/opt/app/secret):
<?= $secret . PHP_EOL ?></code>
	</pre>
</body>
</html>
