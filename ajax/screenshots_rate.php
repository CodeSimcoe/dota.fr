<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	ArghSession::exit_if_not_logged();
	
	if (empty($_GET['id']) || empty($_GET['rating'])) exit;
	
	require ABSOLUTE_PATH.'mysql_connect.php';
	require ABSOLUTE_PATH.'classes/ScreenshotModule.php';
	
	$screenshot_id = (int)$_GET['id'];
	$rating = min(max(0, (int)$_GET['rating']), 5);
	
	$ss = new Screenshot($screenshot_id);
	
	if (!$ss->user_has_voted(ArghSession::get_username())) {
		$query = "INSERT INTO lg_screenshots_ratings (screenshot_id, rating, username) VALUES ('".$screenshot_id."', '".$rating."', '".ArghSession::get_username()."')";
		mysql_query($query);
	}
?>