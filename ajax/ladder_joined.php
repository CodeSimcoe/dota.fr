<?php
	//Session
	require_once '/home/www/dota/classes/RightsMode.php';
	require_once '/home/www/dota/classes/ArghSession.php';
	
	ArghSession::begin();
	ArghSession::exit_if_not_logged();
	
	//Includes
	require_once '/home/www/dota/mysql_connect.php';
	require_once '/home/www/dota/refresh.php';
	require_once '/home/www/dota/ladder_functions.php';
	require_once '/home/www/dota/classes/LadderStates.php';
	require_once '/home/www/dota/classes/CacheManager.php';
	require_once '/home/www/dota/classes/LadderManager.php';
	
	if (isBanned(ArghSession::get_username())
		|| canJoinDet() != 2
		|| getStatus(ArghSession::get_username()) != LadderStates::READY
		|| !ArghSession::has_credits()
		|| ArghSession::get_mobile() == true
		) {
		exit;
	}
	
	/*****************
	*** Friendlist ***
	*****************/
	$LOW_FRIEND_WEIGHT = 8;
	$HIGH_FRIEND_WEIGHT = 25;
	
	function get_friends($username) {
	
		$friends = array();
	
		$username = mysql_real_escape_string($username);
		$query = "SELECT is_gold, rights FROM lg_users WHERE username = '".$username."'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		
		if ($row[0] == 1 || $row[1] != 0) {
			//GOLD
			$query = "SELECT friend FROM lg_friendlist WHERE username = '".$username."'";
			$result = mysql_query($query);
			while($row = mysql_fetch_row($result)) {
				$friends[] = $row[0];
			}
		}
		
		return $friends;
	}
	
	/*$req = "SELECT pts FROM lg_users WHERE username = '".ArghSession::get_username()."'";
	$t = mysql_query($req);
	$l = mysql_fetch_row($t);
	$xp = $l[0];*/
	
	$xp = getPts(ArghSession::get_username());

	LadderManager::AddPlayerToCache(
		CacheManager::LADDER_PLAYERLIST,
		ArghSession::get_username(),
		$xp,
		ArghSession::get_garena(),
		ArghSession::get_rights_base()
	);

	//Credits
	LadderManager::RemovePlayerCredit(ArghSession::get_username());
	
	$cplayers = LadderManager::GetPlayersFromCache(CacheManager::LADDER_PLAYERLIST);
	
	if (ArghSession::is_rights(RightsMode::WEBMASTER)) {
	//if (count($cplayers) == 10) {
		$players = array();
		foreach ($cplayers as $player) $players = array_merge($players, array($player[0]));
		$pts = array();
		$cpts = LadderManager::GetPointsFromDatabase($players);
		foreach ($cpts as $player) $pts[$player[0]] = $player[1];
		$friends = array();
		$cfriends = LadderManager::GetFriendsFromDatabase($players);
		
		//echo print_r($pts);
	//}
	}
	
	//Ouverture fichier
	$file = CacheManager::LADDER_PLAYERLIST;
	//$handle = fopen($file, 'a');
	
	//Contenu à écrire
	//$content = ArghSession::get_username().';'.$xp.';'.ArghSession::get_garena().';'.time().';'.ArghSession::get_rights_base()."\n";
	/*
	if (ArghSession::is_rights(RightsMode::VIP_VOUCHER)) {
		$content .= '1';
	} else {
		$content .= '0';
	}
	$content .= "\n";
	*/
	
	//Ecriture
	//fwrite($handle, $content);
	//fclose($handle);
	
	//Comptage du nombre de joueurs
	//$players = array();
	
	//Récup données
	//$content = file($file);
	
	//foreach ($content as $val) {
	//	$line = explode(';', $val);
	//	if (count($line) == 5) {
	//		$players[] = $line[0];
	//	}
	//}
	
	//Credits
	//$query = "UPDATE lg_users SET daily_games = daily_games - 1 WHERE username = '".ArghSession::get_username()."'";
	//mysql_query($query);
	
	//10 joueurs => lancement
	if (count($cplayers) == 10) {
	
		//Comptage du nombre de joueurs
		//$players = array();
		
		//Récup données
		//$content = file($file);
		
		//foreach ($content as $val) {
		//	$line = explode(';', $val);
		//	if (count($line) == 5) {
		//		$players[] = $line[0];
		//	}
		//}

		$players = array();
		foreach ($cplayers as $player) $players = array_merge($players, array($player[0]));
	
		$pts = array();
		//Récupération XP la + récente
		foreach ($players as $val) {
			$pts[$val] = getPts($val);
			$friends[$val] = get_friends($val);
		}
		
		//Lancement
		shuffle($players);
		
		//Récup Id
		$prev = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM lg_laddergames"));
		$prev = $prev[0];
		
		$players1 = array();
		$players2 = array();
		
		//Répartition
		$m = 10000000;
		$total = array_sum($pts);
		
		for ($a = 1; $a <= 6; $a++) {
			for ($b = $a + 1; $b <= 7; $b++) {
				for ($c = $b + 1; $c <= 8; $c++) {
					for ($d = $c + 1; $d <= 9; $d++) {
					
						$team1 = array($players[0], $players[$a], $players[$b], $players[$c], $players[$d]);
						$team2 = array();
						for ($i = 1; $i <= 9; $i++) {
							if ($i != $a && $i != $b && $i != $c && $i != $d) {
								$team2[] = $players[$i];
							}
						}
					
						$m1 = $pts[$players[0]] + $pts[$players[$a]] + $pts[$players[$b]] + $pts[$players[$c]] + $pts[$players[$d]];
						$m1 = abs($total - 2 * $m1);
						
						$happyness = 0;
						
						//Friends
						foreach ($team1 as $player) {
							foreach ($friends[$player] as $friend) {
								if (in_array($friend, $team1)) {
									//Mutual ?
									if (in_array($friend, $friends[$player])) {
										$happyness += $HIGH_FRIEND_WEIGHT;
									} else {
										$happyness += $LOW_FRIEND_WEIGHT;
									}
								}
							}
						}
						
						//Friends
						foreach ($team2 as $player) {
							foreach ($friends[$player] as $friend) {
								if (in_array($friend, $team2)) {
									//Mutual ?
									if (in_array($friend, $friends[$player])) {
										$happyness += $HIGH_FRIEND_WEIGHT;
									} else {
										$happyness += $LOW_FRIEND_WEIGHT;
									}
								}
							}
						}
						
						/*
						foreach ($team2 as $player) {
							foreach ($friends[$player] as $friend) {
								if (in_array($friend, $team2)) {
									$happyness += $FRIEND_WEIGHT;
								}
							}
						}
						*/
						
						$m1 -= $happyness;
						
						if ($m1 < $m) {
							$m = $m1;
							$players1[0] = $players[0];
							$players1[1] = $players[$a];
							$players1[2] = $players[$b];
							$players1[3] = $players[$c];
							$players1[4] = $players[$d];
							$p = 0;
							for ($i = 1; $i <= 9; $i++) {
								if ($i != $a && $i != $b && $i != $c && $i != $d) {
									$players2[$p] = $players[$i];
									$p++;
								}
							}
						}
					}
				}
			}
		}
		
		
		/*
		$req = "SELECT user FROM lg_hack";
		$t = mysql_query($req);
		$l1 = @mysql_fetch_row($t);
		$l2 = @mysql_fetch_row($t);
		$nick_1 = $l1[0];
		$nick_2 = $l2[0];

		$player_1 = -1;
		$player_2 = -1;
		
		foreach($players as $key => $value) {
			if ($value == $nick_1) {
				$player_1 = $key;
			}
			if ($value == $nick_2) {
				$player_2 = $key;
			}
		}
		
		if ($player_1 >= 0 and $player_2 >= 0) {
			//Hack !
			if ($player_2 != 0) {
				$tamp = $players[0];
				$players[0] = $nick_1;
				$players[$player_1] = $tamp;
				$tamp = $players[1];
				$players[1] = $nick_2;
				$players[$player_2] = $tamp;
			} else {
				$tamp = $players[1];
				$players[1] = $nick_1;
				$players[$player_1] = $tamp;
			}
			
			//print_r($players);
		
			//Répartition
			$m = 1000000;
			$total = array_sum($pts);
			
			for ($b = 2; $b <= 7; $b++) {
				for ($c = $b + 1; $c <= 8; $c++) {
					for ($d = $c + 1; $d <= 9; $d++) {
						$m1 = $pts[$players[0]] + $pts[$players[1]] + $pts[$players[$b]] + $pts[$players[$c]] + $pts[$players[$d]];
						$m1 = abs($total - 2*$m1);
						if ($m1 < $m) {
							$m = $m1;
							$players1[0] = $players[0];
							$players1[1] = $players[1];
							$players1[2] = $players[$b];
							$players1[3] = $players[$c];
							$players1[4] = $players[$d];
							$p = 0;
							for ($i = 2; $i <= 9; $i++) {
								if ($i != $b and $i != $c and $i != $d) {
									$players2[$p] = $players[$i];
									$p++;
								}
							}
						}
					}
				}
			}
			
			shuffle($players1);
			shuffle($players2);
		
		} else {
			//Répartition
			$m = 10000000;
			$total = array_sum($pts);
			
			for ($a = 1; $a <= 6; $a++) {
				for ($b = $a + 1; $b <= 7; $b++) {
					for ($c = $b + 1; $c <= 8; $c++) {
						for ($d = $c + 1; $d <= 9; $d++) {
							$m1 = $pts[$players[0]] + $pts[$players[$a]] + $pts[$players[$b]] + $pts[$players[$c]] + $pts[$players[$d]];
							$m1 = abs($total - 2*$m1);
							if ($m1 < $m) {
								$m = $m1;
								$players1[0] = $players[0];
								$players1[1] = $players[$a];
								$players1[2] = $players[$b];
								$players1[3] = $players[$c];
								$players1[4] = $players[$d];
								$p = 0;
								for ($i = 1; $i <= 9; $i++) {
									if ($i != $a and $i != $b and $i != $c and $i != $d) {
										$players2[$p] = $players[$i];
										$p++;
									}
								}
							}
						}
					}
				}
			}
		}
		*/
		
		if (rand(0,1)  == 0) {
			$se = $players1;
			$sc = $players2;
		} else {
			$se = $players2;
			$sc = $players1;
		}
		
		//Insertion Table
		$upd = "
			UPDATE lg_laddergames
			SET opened = '".time()."',
				status = '".LadderStates::PLAYING."',
				p1 = '".$se[0]."',
				p2 = '".$se[1]."',
				p3 = '".$se[2]."',
				p4 = '".$se[3]."',
				p5 = '".$se[4]."',
				p6 = '".$sc[0]."',
				p7 = '".$sc[1]."',
				p8 = '".$sc[2]."',
				p9 = '".$sc[3]."',
				p10 = '".$sc[4]."',
				xp1 = '".$pts[$se[0]]."',
				xp2 = '".$pts[$se[1]]."',
				xp3 = '".$pts[$se[2]]."',
				xp4 = '".$pts[$se[3]]."',
				xp5 = '".$pts[$se[4]]."',
				xp6 = '".$pts[$sc[0]]."',
				xp7 = '".$pts[$sc[1]]."',
				xp8 = '".$pts[$sc[2]]."',
				xp9 = '".$pts[$sc[3]]."',
				xp10 = '".$pts[$sc[4]]."'
			WHERE id = '".$prev."'
		";
		mysql_query($upd);
		
		// TEMP AURELIEN GHOST++
		//$ghost = mysql_connect("localhost", "root", "n51VqK8X") or die('Erreur de connection à la BDD');
		//mysql_select_db("ghost", $ghost);
		//$ins = mysql_query("INSERT INTO laddergames (game_name, game_date, is_hosted) VALUES ('argh.".$prev."', ".time().", 0)", $ghost);
		
		//Ouverture Nouvelle Partie
		$ins = "INSERT INTO lg_laddergames (status) VALUES ('".LadderStates::OPENED."')";
		mysql_query($ins);
		
		//Vidage cache
		LadderManager::ClearCache(CacheManager::LADDER_PLAYERLIST);
		//$handle = fopen($file, 'w');
		//fwrite($handle, '');
		
		//Changement de statut
		LadderManager::UpdatePlayersStatus($players, LadderStates::IN_NORMAL_GAME);
		//$playerlist = "('".$players[0]."', '".$players[1]."', '".$players[2]."', '".$players[3]."', '".$players[4]."', '".$players[5]."', '".$players[6]."', '".$players[7]."', '".$players[8]."', '".$players[9]."')";
		//mysql_query("UPDATE lg_users SET ladder_status = '".LadderStates::IN_NORMAL_GAME."' WHERE username IN ".$playerlist."");
	}
?>