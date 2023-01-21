<?
	require '/home/www/ligue/classes/ArghSession.php';
	ArghSession::begin();
	
	require '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';
	require '/home/www/ligue/classes/ArghPanel.php';
	require '/home/www/ligue/classes/Theme.php';
	require '/home/www/ligue/mysql_connect.php';
	mysql_query("DELETE FROM lg_usersonline WHERE user = '".ArghSession::get_username()."'");
	
	ArghSession::end();
?>
<html>

<head>
	<?php
		Theme::initialize_theme();
	?>
</head>
<body>
	<br /><br /><br />
	<center>
	<div style="width: 800px">
<?php
	ArghPanel::begin_tag(Lang::ARGH_DOTA_LEAGUE);
	
	echo '<center>
		<img src="pics/ranger-static.gif" alt="Drow Ranger" />
		<br /><br />
		'.Lang::SESSION_OVER.'
		<br /><br />
		<a href="index.php">'.Lang::GO_ON.'</a>
	</center>';
	
	ArghPanel::end_tag();
?>
	</center>
	</div>
</body>