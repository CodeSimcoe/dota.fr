<?php
	require_once('../classes/ArghSession.php');
	ArghSession::begin();
	ArghSession::exit_if_not_logged();
	
	require_once('../classes/ClanRanks.php');
	require('../mysql_connect.php');
	
	if (ArghSession::get_clan_rank() == ClanRanks::TAUREN) {
		
		$req = "UPDATE lg_users
				SET crank = '".(int)$_GET['r']."'
				WHERE username = '".mysql_real_escape_string($_GET['u'])."'
				AND clan = '".ArghSession::get_clan()."'";
		mysql_query($req);
	}
?>