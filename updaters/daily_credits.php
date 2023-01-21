<?php
	require '/home/www/ligue/mysql_connect.php';
	
	//Daily games
	$query = "UPDATE lg_users SET daily_games = '3'";
	mysql_query($query);
?>
