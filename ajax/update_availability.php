<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';
	
	ArghSession::exit_if_not_logged();
	
	require_once ABSOLUTE_PATH.'classes/Team.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';
	
	//Function call is safe
	Team::update_availability($_GET['id'], $_GET['status'], ArghSession::get_username(), ArghSession::get_clan());
?>