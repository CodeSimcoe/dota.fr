<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/var/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'classes/MatchStates.php';
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';
	
	//Vérifications
	//logged
	if (!ArghSession::is_logged()) exit('01');
	//arguments par GET
	if (!isset($_GET['match_id']) or !isset($_GET['vote'])) exit('02');
	//validité vote
	if ($_GET['vote'] != 1 and $_GET['vote'] != 2 and $_GET['vote'] != 3) exit('03');
	//déjà voté ?
	$req = "SELECT * FROM lg_paris WHERE qui_vote = '".ArghSession::get_username()."' AND match_id = '".(int)$_GET['match_id']."'";
	$t = mysql_query($req);
	if (mysql_num_rows($t) > 0) exit('04');
	//match ouvert?
	$req = "SELECT etat FROM lg_matchs WHERE id='".(int)$_GET['match_id']."'";
	$t = mysql_query($req);
	$l=mysql_fetch_object($t);
	if ($l->etat != MatchStates::NOT_PLAYED_YET) exit('05');
	
	//ok, vote.
	$req = "INSERT INTO lg_paris (match_id, winner, qui_vote)
			VALUES ('".(int)$_GET['match_id']."', '".(int)$_GET['vote']."', '".ArghSession::get_username()."')";
	if (mysql_query($req)) echo '1';
?>