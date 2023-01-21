<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/var/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	require_once ABSOLUTE_PATH.'classes/Theme.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';
	ArghSession::begin();
	
	$theme = $_GET['theme'];
	if (!array_key_exists($theme, Theme::$THEMES));
	
	$query = "UPDATE lg_users SET theme = '".mysql_real_escape_string($theme)."' WHERE username = '".ArghSession::get_username()."'";
	mysql_query($query);
	ArghSession::set_theme($theme);
?>