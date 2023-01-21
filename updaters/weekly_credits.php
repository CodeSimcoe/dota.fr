<?php
	require '/home/www/ligue/mysql_connect.php';
	
	//Daily games
	$query = "UPDATE lg_users SET weekly_games = '2'";
	mysql_query($query);
?>