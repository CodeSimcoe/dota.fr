<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/var/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';
	ArghSession::begin();
	
	$banner = (int)$_GET['banner'];
	
	$query = "UPDATE lg_users SET banner = '".mysql_real_escape_string($banner)."' WHERE username = '".ArghSession::get_username()."'";
	mysql_query($query);
	ArghSession::set_banner($banner);
?>