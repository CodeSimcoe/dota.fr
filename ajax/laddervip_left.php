<?php
	require_once '/home/www/ligue/classes/ArghSession.php';
	require_once '/home/www/ligue/classes/CacheManager.php';
	ArghSession::begin();
	
	ArghSession::exit_if_not_logged();

	//Récupération contenu
	$content = file(CacheManager::LADDER_VIP_PLAYERLIST);
	
	//Ouverture fichier
	$handle = fopen(CacheManager::LADDER_VIP_PLAYERLIST, 'w+');
	
	//Réécriture
	foreach ($content as $val) {
		$removed = false;
		$line = explode(';', $val);
		if ($line[0] != ArghSession::get_username()) {
			fwrite($handle, $val);
		} else {
			$removed = true;
		}
		
	}
	
	//Retour Ajax
	echo '1';
?>