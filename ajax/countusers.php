<?php
	//GET
	$allowedFields = array(
		'username',
		'bnet',
		'ggc',
		'access'
	);
	if (!in_array($_GET['field'], $allowedFields)) {
		exit('0');
	}
	
	$user = substr($_GET['user'], 0, 25);

	require('mysql_connect.php');
	
	$req = "SELECT COUNT(*) FROM lg_users WHERE ".$_GET['field']." LIKE '%".mysql_real_escape_string($user)."%'";
	$t = mysql_query($req);
	$l = mysql_fetch_row($t);
	
	echo $l[0];
?>