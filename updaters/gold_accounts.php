<?php
	require '/home/www/ligue/mysql_connect.php';
	
	$query = "UPDATE lg_users SET is_gold = 0 WHERE gold_expire < ".time()." AND gold_expire != 0";
	mysql_query($query);
?>
