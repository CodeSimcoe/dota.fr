<?php
	//Error Reporting
	ini_set('display_errors', 1);
	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	
	require_once '/home/www/ligue/classes/ArghSession.php';
	require_once '/home/www/ligue/classes/CacheManager.php';
	require_once '/home/www/ligue/mysql_connect.php';
	
	ArghSession::begin();
	
	//if (ArghSession::get_username() != 'ThunderBolt_') exit;
	ArghSession::exit_if_not_logged();
	ArghSession::set_gold_and_xp();
	
	echo '<pre>';
	print_r($_SESSION);
	echo '</pre>';
	
	if (!ArghSession::is_vouched()) exit;
	
	//Déclaration
	function getPts($player) {
		$req = "SELECT pts_vip FROM lg_users WHERE username='".$player."'";
		//$req = "SELECT xp FROM lg_laddervip_players WHERE username='".$player."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0];
	}
	
	function getStatus($player) {
		$req = "SELECT ladder_status FROM lg_users WHERE username='".$player."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0];
	}
	
	//Include
	//require_once '/home/www/ligue/mysql_connect.php';
	
	if (getStatus(ArghSession::get_username()) != 'ready') exit;
	
	/*
	function giveXPRank($rank) {
		//Fibonacci sum
		$values = array(1, 3, 6, 11, 19, 32, 53, 87, 142, 231, 375, 608, 985, 1595, 2582, 4179, 6763, 10944, 17709, 28655);
		foreach ($values as $key => $val) {
			if ($rank <= $val) {
				return $key + 1;
			}
		}
		return 0;
	}
	
	$req = "SELECT * FROM lg_laddervip_players WHERE played > 0 ORDER BY xp DESC";
	$t = mysql_query($req);
	
	$i = 0;
	while ($l = mysql_fetch_object($t)) {
		//echo $l->username.'<br />';
		$i++;
		if ($l->username == ArghSession::get_username()) {
			$rank = giveXPRank($i);
			break;
		}
	}
	*/

	//Ouverture fichier
	$handle = fopen(CacheManager::LADDER_VIP_PLAYERLIST, 'a');
	
	//Contenu à écrire
	$content = ArghSession::get_username().';'.getPts(ArghSession::get_username()).';'.ArghSession::get_garena().';'.time()."\n";
	
	//Ecriture
	fwrite($handle, $content);
	fclose($handle);
	
	//Comptage du nombre de joueurs
	$players = array();
	
	//Récup données
	$content = file(CacheManager::LADDER_VIP_PLAYERLIST);
	
	foreach ($content as $val) {
		$line = explode(';', $val);
		if (count($line) == 4) {
			$players = array_merge($players, array($line[0]));
		}
	}
	
	//10 joueurs => lancement
	if (count($players) == 10) {
	
		//Récup Id
		$prev = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM lg_laddervip_games"));
		$prev = $prev[0];
		
		//Election caps
		$sql_in_clause = "('".implode("','", $players)."')";
		$req = "
			SELECT u.username
			FROM lg_users u LEFT JOIN lg_laddervip_vouchlist v ON u.username = v.username
			WHERE u.username IN ".$sql_in_clause."
			ORDER BY v.rank DESC, RAND()
			LIMIT 2";
		
		$t = mysql_query($req);
		$l1 = mysql_fetch_row($t);
		$l2 = mysql_fetch_row($t);
		
		/*
		//Alternance aléatoire
		if (rand(0, 1) == 0) {
			$cap1 = $l1[0];
			$cap2 = $l2[0];
		} else {
			$cap1 = $l2[0];
			$cap2 = $l1[0];
		}
		*/
		
		if (getPts($l1[0]) < getPts($l2[0])) {
			$cap1 = $l1[0];
			$cap2 = $l2[0];
		} else {
			$cap1 = $l2[0];
			$cap2 = $l1[0];
		}
		
		$j = 2;
		
		$players_b = $players;
		
		foreach ($players_b as $val) {
			//Simple joueur ou cap ?
			if ($val != $cap1 and $val != $cap2) {
				$players[$j] = $val;
				$j++;
			} elseif ($val == $cap1) {
				$players[0] = $cap1;
			} elseif ($val == $cap2) {
				$players[1] = $cap2;
			}
		}
		
		//Insertion Table
		//status='p_picking1',
		$time = time();
		$upd = "
			UPDATE lg_laddervip_games
			SET opened = '".$time."',
				actiontime = '".$time."',
				status = 's_picking1',
				cap1='".$players[0]."',
				cap2='".$players[1]."',
				p1='".$players[2]."',
				p2='".$players[3]."',
				p3='".$players[4]."',
				p4='".$players[5]."',
				p5='".$players[6]."',
				p6='".$players[7]."',
				p7='".$players[8]."',
				p8='".$players[9]."'
			WHERE id = '".$prev."'
		";
		mysql_query($upd) or die(mysql_error());
		
		//Ouverture Nouvelle Partie
		$ins = "INSERT INTO lg_laddervip_games (status) VALUES ('opened')";
		mysql_query($ins);
		
		//Vidage cache
		$handle = fopen(CacheManager::LADDER_VIP_PLAYERLIST, 'w');
		fwrite($handle, '');
		
		//Changement de statut
		$playerlist = "('".$players[0]."', '".$players[1]."', '".$players[2]."', '".$players[3]."', '".$players[4]."', '".$players[5]."', '".$players[6]."', '".$players[7]."', '".$players[8]."', '".$players[9]."')";
		mysql_query("UPDATE lg_users SET ladder_status = 'busy_vip' WHERE username IN ".$playerlist."");
	}
?>
