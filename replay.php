<?php

require_once '/home/www/dota/classes/ReplayParser.php';

$dbname = 'forum';
$dbuser = 'forum';
$dbpasswd = 'oO47715R2';


$replay_id = 0;
if (isset($_GET['id'])) {
	$replay_id = (int)$_GET['id'];
}

$file = '';
if (isset($_GET['file'])) {
	$file = $_GET['file'];
}

if (!file_exists('/home/www/dota/forum_replay/'.$replay_id.'.w3g.txt'))
{
	copy('/home/www/forum/files/'.$file, '/home/www/dota/forum_replay/'.$replay_id.'.w3g');
	$parser = new ReplayParser('/home/www/dota/forum_replay/'.$replay_id.'.w3g');
	$parser->txt_serialize();
}
$replay = DotaReplay::load_from_txt('/home/www/dota/forum_replay/'.$replay_id.'.w3g.txt');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="themes/default/default.css" type="text/css">
	<link rel="stylesheet" href="themes/default/parser.css" type="text/css">
</head>
<body>
<div style="width: 600px;">
	<?php 
	if (array_key_exists('cm', $replay->modes)) {
		echo ReplayFunctions::html_picks($replay);
		echo '<br />';
	}
	?>
	<?php echo ReplayFunctions::html_stats($replay); ?>
</div>
</body>
</html>