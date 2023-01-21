<?php

	require_once '/home/www/ligue/mysql_connect.php';
	
	ini_set("memory_limit","100M");

	$gain_fixe = 36;

	$malus_away = 40;
	$malus_left = 20;
	$malus_close = 50;
	
	// Nombre de jours pour declencher XP Decay (60 * 60 * 24) * N
	$decay_days = (60 * 60 * 24) * 1;
	// XP perdue par jour de decay
	$decay_xp = 5;

	function mysql_fetch_rowsarr($result, $numass = MYSQL_BOTH) {
		$got = array();
		if (mysql_num_rows($result) == 0) return $got;
		mysql_data_seek($result, 0);
		while ($row = mysql_fetch_array($result, $numass)) array_push($got, $row);
		return $got;
	}
	function in_arrayr($needle, $haystack) {
		$it = new RecursiveIteratorIterator(new RecursiveArrayIterator($haystack));
		foreach($it AS $element) {
			if($element == $needle) {
				return true;
			}
		}
		return false;
	}

	// On reset lg_laddervip_follow
	$res = mysql_query("
		TRUNCATE TABLE lg_laddervip_follow
	") or die(mysql_error());

	// On reset tous les scores
	$res = mysql_query("
		UPDATE lg_laddervip_players
		SET games = 0, played = 0, closed = 0, wins = 0, loses = 0, aways = 0, leaves = 0, xp = 1600
	") or die(mysql_error());

	// On ajoute les nouveaux joueurs
	$res = mysql_query("
		INSERT INTO lg_laddervip_players (username, played, closed, wins, loses, aways, leaves, xp)
		SELECT DISTINCT 
			P.player, 0, 0, 0, 0, 0, 0, 1600
		FROM (
			SELECT DISTINCT cap1 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT cap2 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT pp1 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT pp2 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT pp3 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT pp4 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT pp5 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT pp6 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT pp7 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT pp8 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
		) P
		LEFT JOIN lg_laddervip_players ON P.player = username
		WHERE P.player <> '' AND username IS NULL
	") or die(mysql_error());
	
	$res = mysql_query("
		INSERT INTO lg_laddervip_players (username, played, closed, wins, loses, aways, leaves, xp)
		SELECT DISTINCT 
			P.player, 0, 0, 0, 0, 0, 0, 1600
		FROM (
			SELECT DISTINCT p1 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT p2 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT p3 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT p4 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT p5 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT p6 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT p7 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
			UNION ALL
			SELECT DISTINCT p8 AS 'player' FROM lg_laddervip_games WHERE status = 'closed'
		) P
		LEFT JOIN lg_laddervip_players ON P.player = username
		WHERE P.player <> '' AND username IS NULL
	") or die(mysql_error());

	// On recupère les away / left
	// 1 => LEFT, 2 => AWAY
	$follows = array();
	$res = mysql_query("
		SELECT game_id, pour_qui, info, COUNT(*) AS 'votes'
		FROM lg_laddervip_playersreports
		GROUP BY game_id, pour_qui, info 
		HAVING COUNT(*) >= 5
	") or die(mysql_error());
	if (mysql_num_rows($res) != 0) {
		while ($obj = mysql_fetch_object($res)) {
			if (!isset($follows[$obj->game_id])) $follows[$obj->game_id] = array();
			array_push($follows[$obj->game_id], array( "username" => $obj->pour_qui, "result" => ($obj->info == 1) ? 'left' : 'away', "votes" => $obj->votes ));
		}
	}

	// On recupère les games closed
	$games = array();
	$res = mysql_query("
		SELECT id, opened, winner, cap1, cap2, p1, p2, p3, p4, p5, p6, p7, p8, pp1, pp2, pp3, pp4, pp5, pp6, pp7, pp8
		FROM lg_laddervip_games
		WHERE status = 'closed'
		ORDER BY opened ASC
	") or die(mysql_error());
	if (mysql_num_rows($res) != 0) $games = mysql_fetch_rowsarr($res);
	
	// On batch les games !
	if (count($games) > 0) {
		$dates = array();
		foreach ($games AS $game) {
			// Variables
			$players = array();
			$xps = array();
			// On recupere XP avant la game
			$res = mysql_query("
				SELECT username, xp
				FROM lg_laddervip_players
				WHERE username IN (
					'".$game["cap1"]."',
					'".$game["cap2"]."',
					'".$game["pp1"]."',
					'".$game["pp2"]."',
					'".$game["pp3"]."',
					'".$game["pp4"]."',
					'".$game["pp5"]."',
					'".$game["pp6"]."',
					'".$game["pp7"]."',
					'".$game["pp8"]."'
				)
			") or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				while ($obj = mysql_fetch_object($res)) $xps[$obj->username] = $obj->xp;
			}
			// On traite en fonction du résultat none / else
			if ($game["winner"] != "none") {

				// On initialise le tableau de picks
				$g1 = 1; $g2 = 2; $g3 = 2; $g4 = 3;
				$g5 = 3; $g6 = 4; $g7 = 4; $g8 = 5;
				for ($i = 1; $i < 9; $i++) {
					$players["pick_".$i] = array();
					$players["pick_".$i]["slqfield"] = "pp".$i;
					$players["pick_".$i]["username"] = $game["pp".$i];
					$players["pick_".$i]["group"] = ${"g".$i};
					$players["pick_".$i]["wins"] = 0;
					$players["pick_".$i]["loses"] = 0;
					$players["pick_".$i]["aways"] = 0;
					$players["pick_".$i]["leaves"] = 0;
					$players["pick_".$i]["gain"] = 0;
					$players["pick_".$i]["follow"] = 'none';
					if (isset($xps[$players["pick_".$i]["username"]])) {
						$players["pick_".$i]["realxp"] = $xps[$players["pick_".$i]["username"]];
						$players["pick_".$i]["pickxp"] = $xps[$players["pick_".$i]["username"]];
					} else {
						$players["pick_".$i]["realxp"] = 1600;
						$players["pick_".$i]["pickxp"] = 1600;
					}
				}

				// On reaffecte XP en fonction des ordres de pick
				for ($i = 1; $i < 8; $i++) {
					for ($j = $i + 1; $j < 9; $j++) {
						if ($players["pick_".$i]["group"] < $players["pick_".$j]["group"]) {
							if ($players["pick_".$i]["realxp"] < $players["pick_".$j]["realxp"]) {
								$diff_xp = $players["pick_".$j]["realxp"] - $players["pick_".$i]["realxp"];
								$diff_group = $players["pick_".$j]["group"] - $players["pick_".$i]["group"];
								$players["pick_".$i]["pickxp"] += ($diff_xp * (1 + 1 * $diff_group) / 100);
								$players["pick_".$j]["pickxp"] -= ($diff_xp * (1 + 1 * $diff_group) / 100);
							}
						}
					}
				}

				// On recupere XP des caps
				$players["cap1"] = array();
				$players["cap1"]["sqlfield"] = "cap1";
				$players["cap1"]["username"] = $game["cap1"];
				$players["cap1"]["group"] = 0;
				$players["cap1"]["realxp"] = isset($xps[$game["cap1"]]) ? $xps[$game["cap1"]] : 1600;
				$players["cap1"]["pickxp"] = isset($xps[$game["cap1"]]) ? $xps[$game["cap1"]] : 1600;
				$players["cap1"]["wins"] = 0;
				$players["cap1"]["loses"] = 0;
				$players["cap1"]["aways"] = 0;
				$players["cap1"]["leaves"] = 0;
				$players["cap1"]["gain"] = 0;
				$players["cap1"]["follow"] = 'none';

				$players["cap2"] = array();
				$players["cap2"]["sqlfield"] = "cap2";
				$players["cap2"]["username"] = $game["cap2"];
				$players["cap2"]["group"] = 0;
				$players["cap2"]["realxp"] = isset($xps[$game["cap2"]]) ? $xps[$game["cap2"]] : 1600;
				$players["cap2"]["pickxp"] = isset($xps[$game["cap2"]]) ? $xps[$game["cap2"]] : 1600;
				$players["cap2"]["wins"] = 0;
				$players["cap2"]["loses"] = 0;
				$players["cap2"]["aways"] = 0;
				$players["cap2"]["leaves"] = 0;
				$players["cap2"]["gain"] = 0;
				$players["cap2"]["follow"] = 'none';

				// On reparti les teams
				$sentinels = array(); $scourges = array();
				array_push($sentinels, $players["cap1"], $players["pick_1"], $players["pick_4"], $players["pick_5"], $players["pick_8"]);
				array_push($scourges, $players["cap2"], $players["pick_2"], $players["pick_3"], $players["pick_6"], $players["pick_7"]);

				// On calcul la moyenne XP par equipe
				$exp_se = ($players["cap1"]["pickxp"] + $players["pick_1"]["pickxp"] + $players["pick_4"]["pickxp"] + $players["pick_5"]["pickxp"] + $players["pick_8"]["pickxp"]) / 5;
				$exp_sc = ($players["cap2"]["pickxp"] + $players["pick_2"]["pickxp"] + $players["pick_3"]["pickxp"] + $players["pick_6"]["pickxp"] + $players["pick_7"]["pickxp"]) / 5;

				// Calcul du delta
				$delta = $exp_sc - $exp_se;
				$idelta = -$delta;

				// Calcul des points en fonction du vainqueur
				if ($game["winner"] == "se")
				{

					$proba = 1 / (1 + exp(-$delta / 300));
					$puntos = $gain_fixe * sqrt($proba / (1 - $proba));

					// On gère les puntos
					array_walk($sentinels, create_function('&$a, $k, $p', '$a["gain"] = $p;'), $puntos);
					array_walk($scourges, create_function('&$a, $k, $p', '$a["gain"] = $p;'), -$puntos);

					// On affecte les stats
					array_walk($sentinels, create_function('&$a, $k', '$a["wins"] = 1; $a["loses"] = 0; $a["aways"] = 0; $a["leaves"] = 0; $a["follow"] = "win";'));
					array_walk($scourges, create_function('&$a, $k', '$a["wins"] = 0; $a["loses"] = 1; $a["aways"] = 0; $a["leaves"] = 0; $a["follow"] = "lose";'));

					// On gère les leavers
					if (isset($follows[$game["id"]])) {
						$leaves_sentinels = 0; $leaves_scourges = 0;
						array_walk($sentinels, create_function('&$a, $k, $f', 'if (in_arrayr($a["username"], $f)) { $a["wins"] = 0; $a["leaves"] = 1; $a["follow"] = "left"; }'), $follows[$game["id"]]);
						array_walk($scourges, create_function('&$a, $k, $f', 'if (in_arrayr($a["username"], $f)) { $a["loses"] = 0; $a["leaves"] = 1; $a["follow"] = "left"; }'), $follows[$game["id"]]);
						array_walk($sentinels, create_function('&$a, $k, $c', '$c[0] += $a["leaves"];'), array(&$leaves_sentinels));
						array_walk($scourges, create_function('&$a, $k, $c', '$c[0] += $a["leaves"];'), array(&$leaves_scourges));
						$total_leavers = $leaves_sentinels + $leaves_scourges;
						switch ($total_leavers) {
							case 1:
								if ($leaves_sentinels == 1) {
									// sentinel win a 4v5
									array_walk($sentinels, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] *= 1.25; else $a["gain"] -= '.$malus_left.';'));
								} else {
									// sentinel gagne a 5v4
									array_walk($scourges, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] *= 0.33; else $a["gain"] -= '.$malus_left.';'));
								}
								break;
							case 2:
								if ($leaves_sentinels == $leaves_scourges) {
									array_walk($sentinels, create_function('&$a, $k', 'if ($a["leaves"] == 1) $a["gain"] -= '.$malus_left.';'));
									array_walk($scourges, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] *= 0.5; else $a["gain"] -= '.$malus_left.';'));
								} else {
									// 3v5 ou 5v3
									array_walk($sentinels, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] = 0; else $a["gain"] -= '.$malus_close.';'));
									array_walk($scourges, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] = 0; else $a["gain"] -= '.$malus_close.';'));
								}
								break;
							default:
								array_walk($sentinels, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] = 0; else $a["gain"] -= '.$malus_close.';'));
								array_walk($scourges, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] = 0; else $a["gain"] -= '.$malus_close.';'));
								break;
						}
					}

				}
				else
				{

					$proba = 1 / (1 + exp(-$idelta / 300));
					$puntos = $gain_fixe * sqrt($proba / (1 - $proba));

					// On gère les puntos
					array_walk($sentinels, create_function('&$a, $k, $p', '$a["gain"] = $p;'), -$puntos);
					array_walk($scourges, create_function('&$a, $k, $p', '$a["gain"] = $p;'), $puntos);

					// On affecte les stats
					array_walk($sentinels, create_function('&$a, $k', '$a["wins"] = 0; $a["loses"] = 1; $a["aways"] = 0; $a["leaves"] = 0; $a["follow"] = "lose";'));
					array_walk($scourges, create_function('&$a, $k', '$a["wins"] = 1; $a["loses"] = 0; $a["aways"] = 0; $a["leaves"] = 0; $a["follow"] = "win";'));

					// On gère les leavers
					if (isset($follows[$game["id"]])) {
						$leaves_sentinels = 0; $leaves_scourges = 0;
						array_walk($sentinels, create_function('&$a, $k, $f', 'if (in_arrayr($a["username"], $f)) { $a["loses"] = 0; $a["leaves"] = 1; $a["follow"] = "left"; }'), $follows[$game["id"]]);
						array_walk($scourges, create_function('&$a, $k, $f', 'if (in_arrayr($a["username"], $f)) { $a["wins"] = 0; $a["leaves"] = 1; $a["follow"] = "left"; }'), $follows[$game["id"]]);
						array_walk($sentinels, create_function('&$a, $k, $c', '$c[0] += $a["leaves"];'), array(&$leaves_sentinels));
						array_walk($scourges, create_function('&$a, $k, $c', '$c[0] += $a["leaves"];'), array(&$leaves_scourges));
						$total_leavers = $leaves_sentinels + $leaves_scourges;
						switch ($total_leavers) {
							case 1:
								if ($leaves_scourges == 1) {
									// scourge win a 4v5
									array_walk($scourges, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] *= 1.25; else $a["gain"] -= '.$malus_left.';'));
								} else {
									// scourge gagne a 5v4
									array_walk($sentinels, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] *= 0.33; else $a["gain"] -= '.$malus_left.';'));
								}
								break;
							case 2:
								if ($leaves_sentinels == $leaves_scourges) {
									array_walk($sentinels, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] *= 0.5; else $a["gain"] -= '.$malus_left.';'));
									array_walk($scourges, create_function('&$a, $k', 'if ($a["leaves"] == 1) $a["gain"] -= '.$malus_left.';'));
								} else {
									// 3v5 ou 5v3
									array_walk($sentinels, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] = 0; else $a["gain"] -= '.$malus_close.';'));
									array_walk($scourges, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] = 0; else $a["gain"] -= '.$malus_close.';'));
								}
								break;
							default:
								array_walk($sentinels, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] = 0; else $a["gain"] -= '.$malus_close.';'));
								array_walk($scourges, create_function('&$a, $k', 'if ($a["leaves"] == 0) $a["gain"] = 0; else $a["gain"] -= '.$malus_close.';'));
								break;
						}
					}

				}

				// Calcul de XP
				array_walk($sentinels, create_function('&$a, $k', '$a["pickxp"] += $a["gain"];'));
				array_walk($scourges, create_function('&$a, $k', '$a["pickxp"] += $a["gain"];'));

				// Mise a jour XP en BDD
				$qry_xp = "";
				$qry_wins = "";
				$qry_loses = "";
				$qry_aways = "";
				$qry_leaves = "";
				
				foreach ($sentinels AS $player) {
					$qry_xp .= " WHEN username = '".$player["username"]."' THEN ".$player["pickxp"];
					if ($player["wins"] == 1) $qry_wins .= " WHEN username = '".$player["username"]."' THEN wins + 1";
					if ($player["loses"] == 1) $qry_loses .= " WHEN username = '".$player["username"]."' THEN loses + 1";
					if ($player["aways"] == 1) $qry_aways .= " WHEN username = '".$player["username"]."' THEN aways + 1";
					if ($player["leaves"] == 1) $qry_leaves .= " WHEN username = '".$player["username"]."' THEN leaves + 1";
					$res = mysql_query("
						INSERT INTO lg_laddervip_follow (player, game_id, resultat, xp)
						VALUES ('".$player["username"]."', ".$game["id"].", '".$player["follow"]."', ".$player["gain"].")
					") or die(mysql_error());
				}
				foreach ($scourges AS $player) {
					$qry_xp .= " WHEN username = '".$player["username"]."' THEN ".$player["pickxp"];
					if ($player["wins"] == 1) $qry_wins .= " WHEN username = '".$player["username"]."' THEN wins + 1";
					if ($player["loses"] == 1) $qry_loses .= " WHEN username = '".$player["username"]."' THEN loses + 1";
					if ($player["aways"] == 1) $qry_aways .= " WHEN username = '".$player["username"]."' THEN aways + 1";
					if ($player["leaves"] == 1) $qry_leaves .= " WHEN username = '".$player["username"]."' THEN leaves + 1";
					$res = mysql_query("
						INSERT INTO lg_laddervip_follow (player, game_id, resultat, xp)
						VALUES ('".$player["username"]."', ".$game["id"].", '".$player["follow"]."', ".$player["gain"].")
					") or die(mysql_error());
				}

				$qry = "UPDATE lg_laddervip_players SET";
				$qry .= " games = games + 1";
				$qry .= " , played = played + 1";
				if ($qry_xp != "") $qry .= " , xp = CASE ".$qry_xp." ELSE xp END";
				if ($qry_wins != "") $qry .= " , wins = CASE ".$qry_wins." ELSE wins END";
				if ($qry_loses != "") $qry .= " , loses = CASE ".$qry_loses." ELSE loses END";
				if ($qry_aways != "") $qry .= " , aways = CASE ".$qry_aways." ELSE aways END";
				if ($qry_leaves != "") $qry .= " , leaves = CASE ".$qry_leaves." ELSE leaves END";
				$qry .= " WHERE username IN (
					'".$game["cap1"]."',
					'".$game["cap2"]."',
					'".$game["pp1"]."',
					'".$game["pp2"]."',
					'".$game["pp3"]."',
					'".$game["pp4"]."',
					'".$game["pp5"]."',
					'".$game["pp6"]."',
					'".$game["pp7"]."',
					'".$game["pp8"]."'
				)";
				$upd = mysql_query($qry) or die(mysql_error());

			} else {
				$upd = mysql_query("
					INSERT INTO lg_laddervip_follow (player, game_id, resultat, xp)
					SELECT username, ".$game["id"].", 'none', 0
					FROM lg_laddervip_players
					WHERE username IN (
						'".$game["cap1"]."',
						'".$game["cap2"]."',
						'".($game["pp1"] == '' ? $game["p1"] : $game["pp1"])."',
						'".($game["pp2"] == '' ? $game["p2"] : $game["pp2"])."',
						'".($game["pp3"] == '' ? $game["p3"] : $game["pp3"])."',
						'".($game["pp4"] == '' ? $game["p4"] : $game["pp4"])."',
						'".($game["pp5"] == '' ? $game["p5"] : $game["pp5"])."',
						'".($game["pp6"] == '' ? $game["p6"] : $game["pp6"])."',
						'".($game["pp7"] == '' ? $game["p7"] : $game["pp7"])."',
						'".($game["pp8"] == '' ? $game["p8"] : $game["pp8"])."'
					)
				") or die(mysql_error());
				if (isset($follows[$game["id"]])) {
					$qry_xp = "";
					$qry_left = "";
					$qry_away = "";
					foreach ($follows[$game["id"]] AS $follow) {
						if ($follow["result"] == "left") {
							$qry_xp .= " WHEN username = '".$follow["username"]."' THEN xp - ".$malus_close;
							$qry_left .= " WHEN username = '".$follow["username"]."' THEN leaves + 1";
							$upd = mysql_query("
								UPDATE lg_laddervip_follow
								SET resultat = 'left', xp = -".$malus_close."
								WHERE player = '".$follow["username"]."' AND game_id = ".$game["id"]."
							") or die(mysql_error());
						} else if ($follow["result"] == "away") {
							$qry_xp .= " WHEN username = '".$follow["username"]."' THEN xp - ".$malus_away;
							$qry_away .= " WHEN username = '".$follow["username"]."' THEN aways + 1";
							$upd = mysql_query("
								UPDATE lg_laddervip_follow
								SET resultat = 'away', xp = -".$malus_away."
								WHERE player = '".$follow["username"]."' AND game_id = ".$game["id"]."
							") or die(mysql_error());
						}
					}
					$qry = "UPDATE lg_laddervip_players SET";
					$qry .= " games = games + 1";
					$qry .= " , closed = closed + 1";
					if ($qry_xp != "") $qry .= " , xp = CASE ".$qry_xp." ELSE xp END";
					if ($qry_left != "") $qry .= " , leaves = CASE ".$qry_left." ELSE leaves END";
					if ($qry_away != "") $qry .= " , aways = CASE ".$qry_away." ELSE aways END";
					$qry .= "
						WHERE username IN (
							'".$game["cap1"]."',
							'".$game["cap2"]."',
							'".($game["pp1"] == '' ? $game["p1"] : $game["pp1"])."',
							'".($game["pp2"] == '' ? $game["p2"] : $game["pp2"])."',
							'".($game["pp3"] == '' ? $game["p3"] : $game["pp3"])."',
							'".($game["pp4"] == '' ? $game["p4"] : $game["pp4"])."',
							'".($game["pp5"] == '' ? $game["p5"] : $game["pp5"])."',
							'".($game["pp6"] == '' ? $game["p6"] : $game["pp6"])."',
							'".($game["pp7"] == '' ? $game["p7"] : $game["pp7"])."',
							'".($game["pp8"] == '' ? $game["p8"] : $game["pp8"])."'
						)";
					$upd = mysql_query($qry) or die(mysql_error());
				} else {
					$upd = mysql_query("
						UPDATE lg_laddervip_players SET
							games = games + 1,
							closed = closed + 1
						WHERE username IN (
							'".$game["cap1"]."',
							'".$game["cap2"]."',
							'".($game["pp1"] == '' ? $game["p1"] : $game["pp1"])."',
							'".($game["pp2"] == '' ? $game["p2"] : $game["pp2"])."',
							'".($game["pp3"] == '' ? $game["p3"] : $game["pp3"])."',
							'".($game["pp4"] == '' ? $game["p4"] : $game["pp4"])."',
							'".($game["pp5"] == '' ? $game["p5"] : $game["pp5"])."',
							'".($game["pp6"] == '' ? $game["p6"] : $game["pp6"])."',
							'".($game["pp7"] == '' ? $game["p7"] : $game["pp7"])."',
							'".($game["pp8"] == '' ? $game["p8"] : $game["pp8"])."'
						)
					") or die(mysql_error());
				}
			}
		}
	}

	$res = mysql_query("
		DELETE FROM lg_laddervip_players 
		WHERE username NOT IN (
			SELECT DISTINCT username
			FROM lg_laddervip_vouchlist
		)
	") or die(mysql_error());

?>
