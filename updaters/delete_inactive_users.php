<?php
	require '/home/www/ligue/mysql_connect.php';
	
	$activation_delay = 24 * 3600;
	
	$users = array();
	$query = "SELECT username FROM lg_users WHERE active = 0 AND joined < ".time()." - ".$activation_delay;
	$result = mysql_query($query);
	while ($row = mysql_fetch_row($result)) {
		$users[] = $row[0];
	}
	
	$list = implode(',', $users);

	$query = "DELETE FROM lg_users WHERE username IN (".$list.")";
	mysql_query($query);
	
	$query = "DELETE FROM lg_activation WHERE username IN (".$list.")";
	mysql_query($query);
?>
