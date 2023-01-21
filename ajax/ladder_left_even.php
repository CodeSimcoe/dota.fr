<?php

	require_once '/home/www/dota/classes/ArghSession.php';
	require_once '/home/www/dota/classes/CacheManager.php';
	require_once '/home/www/dota/classes/LadderManager.php';
	require_once '/home/www/dota/mysql_connect.php';
	
	ArghSession::begin();
	ArghSession::exit_if_not_logged();

	$removed = LadderManager::RemovePlayerFromCache(
		CacheManager::LADDER_PLAYERLIST_EVEN,
		ArghSession::get_username()
	);
	
	if ($removed) {
		LadderManager::AddPlayerCredit(ArghSession::get_username());
	}

	//echo '1';

?>