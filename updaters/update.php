<?php
	include('/home/www/ligue/classes/ArghSession.php');
	include('/home/www/ligue/classes/LadderStates.php');
	include('/home/www/ligue/classes/AdminLog.php');
	include('/home/www/ligue/classes/ClanRanks.php');
	include('/home/www/ligue/mysql_connect.php');
	include('/home/www/ligue/misc.php');
	include('/home/www/ligue/ladder_functions.php');
	
	//7jours
	//$days = time() - (7*86400);
	$days = time() - 604800;
	
	$req0 = "SELECT * FROM lg_users WHERE clan != 0 AND crank = '".ClanRanks::PEON."' AND jclan < $days";
	$nb = mysql_num_rows(mysql_query($req0));

	$req1 = "UPDATE lg_users SET crank = ".ClanRanks::PEON." WHERE clan='0'";
	mysql_query($req1);
	
	$req2 = "UPDATE lg_users SET crank = ".ClanRanks::GRUNT." WHERE crank = ".ClanRanks::PEON." AND clan != 0 AND jclan < $days";
	mysql_query($req2);
	
	//Admin Log
	$al = new AdminLog('Rank update ('.$nb.' peons => grunt)', AdminLog::TYPE_ROUTINES, 'LadderGuardian');
	$al->save_log();
	/*
	$admin_req="INSERT INTO lg_adminlog (qui, quand, quoi) VALUES ('LeagueGuardian', '".time()."', 'Rank update (".$nb." peons => grunt)')";
	mysql_query($admin_req);
	*/
	
	//Unban
	$req2 = "SELECT * FROM lg_ladderbans";
	$t2 = mysql_query($req2);
	while ($l2 = @mysql_fetch_object($t2)) {
		if (isFinished($l2->quand, $l2->duree)) {
			mysql_query("DELETE FROM lg_ladderbans WHERE id='".$l2->id."'");
			//Admin Log
			$al = new AdminLog('Unban of '.$l2->qui, AdminLog::TYPE_ROUTINES, 'LadderGuardian');
			$al->save_log();
			
			/*
			$admin_req="INSERT INTO lg_adminlog (qui, quand, quoi) VALUES ('LadderGuardian', '".time()."', 'Unban ".$l2->qui."')";
			mysql_query($admin_req);
			*/
		}
	}
	
	/*
	//Cloture des games Ladder
	$query = "SELECT * FROM lg_laddergames WHERE status = 'playing'";
	$table = mysql_query($query);
	if (mysql_num_rows($table) > 0) {
		while ($line = mysql_fetch_object($table)) {
			//Close - 3h 
			if (time() - $line->opened > 10800) {
				//Reports pour cette game
				$none = 0;
				$se = 0;
				$sc = 0;
				$sreq = "SELECT * FROM lg_winnersreports WHERE game_id = '".$line->id."'";
				$st = mysql_query($sreq);
				while ($sl = mysql_fetch_object($st)) {
					switch ($sl->winner) {
						case 'none':
							$none++;
							break;
						case 'se':
							$se++;
							break;
						case 'sc':
							$sc++;
							break;
						default:
							break;
					}
				}
				$total = $none + $se + $sc;
				//echo $total;
				if ($none == max($none, $se, $sc)) {
					GameReporter::report($line->id, GameReporter::NO_WINNER);
				} elseif ($se == max($none, $se, $sc)) {
					GameReporter::report($line->id, GameReporter::SENTINEL);
				} elseif ($sc = max($none, $se, $sc)) {
					GameReporter::report($line->id, GameReporter::SCOURGE);
				}
			}
		}
	}
	*/
	
	/*
	//Cloture des games Ladder Fun
	$query = "SELECT * FROM lg_ladderfun_games WHERE status = 'playing'";
	$table = mysql_query($query);
	if (mysql_num_rows($table) > 0) {
		while ($line = mysql_fetch_object($table)) {
			//Close - 3h
			if (time() - $line->opened > 10800) {
				//Reports pour cette game
				$none = 0;
				$se = 0;
				$sc = 0;
				$sreq = "SELECT * FROM lg_ladderfun_winnersreports WHERE game_id = '".$line->id."'";
				$st = mysql_query($sreq);
				while ($sl = mysql_fetch_object($st)) {
					switch ($sl->winner) {
						case 'none':
							$none++;
							break;
						case 'se':
							$se++;
							break;
						case 'sc':
							$sc++;
							break;
						default:
							break;
					}
				}
				$total = $none + $se + $sc;
				//echo $total;
				if ($none == max($none, $se, $sc)) {
					reportGame($line->id, 'none');
				} elseif ($se == max($none, $se, $sc)) {
					reportGame($line->id, 'se');
				} elseif ($sc = max($none, $se, $sc)) {
					reportGame($line->id, 'sc');
				}
			}
		}
	}
	*/
	
	// CALCUL DES STATS LADDER
	$req ="
		SELECT DISTINCT 
		 id, 
		 opened
		FROM
		 lg_laddergames
		WHERE
		 id NOT IN (
		  SELECT
		   game_id
		  FROM
		   lg_ladder_stats_games
		 )
		AND status = 'closed'";
	$c = 0;
	$qry = mysql_query($req) or die(mysql_error());
	while ($obj = mysql_fetch_object($qry)) {
		$tdate = getdate($obj->opened);
		if ($tdate["year"] >= 2008) {
			$c = $c + 1;
			$ins = "
				INSERT INTO lg_ladder_stats_games 
				 (game_id, year, month, day, new) 
				VALUES 
				 ('".$obj->id."', '".$tdate["year"]."', '".$tdate["mon"]."', '".$tdate["mday"]."', 1)";
			mysql_query($ins);
		}
	}
	
//				CASE WHEN f.xp > 0 THEN 1 ELSE 0 END AS 'Wins', 
//				CASE WHEN f.xp < 0 AND f.resultat NOT IN ('left', 'away') THEN 1 ELSE 0 END AS 'Loses', 
//				CASE WHEN f.xp < 0 AND f.resultat = 'left' THEN 1 ELSE 0 END AS 'Lefts', 
//				CASE WHEN f.xp < 0 AND f.resultat = 'away' THEN 1 ELSE 0 END AS 'Aways', 

	$req = "
		SELECT
		 DISTINCTROW year, month, day
		FROM
		 lg_ladder_stats_games
		WHERE
		 new = 1
		ORDER BY year, month, day";
	$qry = mysql_query($req) or die(mysql_error());
	while ($obj = mysql_fetch_object($qry)) {
	
		$del = "DELETE FROM lg_ladder_stats_players WHERE year = '".$obj->year."' AND month = '".$obj->month."' AND day = '".$obj->day."'";
		mysql_query($del);
		$ins = "
			INSERT INTO lg_ladder_stats_players 
			 (year, month, day, player, games, wins, loses, lefts, aways, balance) 
			SELECT
			 year,
			 month,
			 day,
			 player,
			 NbGames,
			 Wins,
			 Loses,
			 Lefts,
			 Aways,
			 Balance
			FROM (
			 SELECT
			  year,
			  month,
			  day,
			  player,
			  SUM(Wins + Loses + Lefts + Aways) AS 'NbGames',
			  SUM(Wins) AS 'Wins', 
			  SUM(Loses) AS 'Loses', 
			  SUM(Lefts) AS 'Lefts', 
			  SUM(Aways) AS 'Aways', 
			  SUM(xp) AS 'Balance' 
			  FROM (
			   SELECT 
				l.year,
				l.month,
				l.day,
				f.player, 
				CASE WHEN f.resultat = 'win' THEN 1 ELSE 0 END AS 'Wins', 
				CASE WHEN f.resultat = 'lose' THEN 1 ELSE 0 END AS 'Loses', 
				CASE WHEN f.resultat = 'left' THEN 1 ELSE 0 END AS 'Lefts', 
				CASE WHEN f.resultat = 'away' THEN 1 ELSE 0 END AS 'Aways', 
				f.xp
			   FROM
				lg_ladder_stats_games l, lg_ladderfollow f 
			   WHERE
				l.game_id = f.game_id
			   AND l.year = '".$obj->year."'
			   AND l.month = '".$obj->month."'
			   AND l.day = '".$obj->day."'
			 ) AS Totals
			 GROUP BY
			  Totals.year,
			  Totals.month,
			  Totals.day,
			  Totals.player
			) AS Filter
			WHERE 
			 NbGames > 0";
		mysql_query($ins);
		
		$del = "DELETE FROM lg_ladder_stats_days WHERE year = '".$obj->year."' AND month = '".$obj->month."' AND day = '".$obj->day."'";
		mysql_query($del);
		$day = "
			INSERT INTO lg_ladder_stats_days
			 (year, month, day, games, players)
			SELECT
			 Players.year, 
			 Players.month, 
			 Players.day, 
			 COUNT(g.game_id), 
			 Players.players
			FROM (
			 SELECT
			  year, 
			  month, 
			  day, 
			  COUNT(player) AS 'players'
			 FROM
			  lg_ladder_stats_players
			 WHERE
			  year = '".$obj->year."' 
			 AND month = '".$obj->month."' 
			 AND day = '".$obj->day."'
			 GROUP BY
			  year, 
			  month, 
			  day
			) AS Players
			INNER JOIN
			 lg_ladder_stats_games AS g
			ON 
			 g.year = Players.year 
			AND g.month = Players.month 
			AND g.day = Players.day
			GROUP BY
			 Players.year,
			 Players.month,
			 Players.day,
			 Players.players";
		mysql_query($day);

		$del = "DELETE FROM lg_ladder_stats_months WHERE year = '".$obj->year."' AND month = '".$obj->month."'";
		mysql_query($del);
		$mon = "
			INSERT INTO lg_ladder_stats_months
			 (year, month, games, players)
			SELECT
			 Games.year, 
			 Games.month, 
			 Games.games,
			 COUNT(DISTINCT p.player) 
			FROM (
			 SELECT
			  year,
			  month,
			  COUNT(game_id) AS 'games'
			 FROM
			  lg_ladder_stats_games
			 WHERE
			  year = '".$obj->year."'
			 AND month = '".$obj->month."'
			 GROUP BY
			  year,
			  month
			) AS Games
			INNER JOIN
			 lg_ladder_stats_players AS p
			ON 
			 p.year = Games.year 
			AND p.month = Games.month 
			GROUP BY
			 Games.year,
			 Games.month,
			 Games.games";
		mysql_query($mon);

	}

	/*
	mysql_query("TRUNCATE TABLE lg_ladder_stats_ranks");
	mysql_query("
		INSERT INTO lg_ladder_stats_ranks (player, games, wins, loses, lefts, aways, balance)
		SELECT
		 player,
		 SUM(games),
		 SUM(wins),
		 SUM(loses),
		 SUM(lefts),
		 SUM(aways),
		 1600 + SUM(balance)
		FROM
		 lg_ladder_stats_players
		GROUP BY
		 player
		ORDER BY
		 1600 + SUM(balance) DESC
	");
	*/
	
	$req = "
		SELECT
		 DISTINCTROW game_id
		FROM
		 lg_ladder_stats_games
		WHERE
		 new = 1
		ORDER BY game_id";
	$qry = mysql_query($req) or die(mysql_error());
	while ($obj = mysql_fetch_object($qry)) {
	
		$del = "DELETE FROM lg_ladder_stats_reports WHERE game_id = '".$obj->game_id."'";
		mysql_query($del);
		$ins = "
			INSERT INTO lg_ladder_stats_reports (game_id, player, game_winner, player_report)
			SELECT 
			 T1.id,
			 T1.username,
			 T1.winner,
			 T2.winner
			FROM (
			 SELECT id, winner, p1 AS 'username' FROM lg_laddergames WHERE status = 'closed' AND id = '".$obj->game_id."'
			 UNION
			 SELECT id, winner, p2 AS 'username' FROM lg_laddergames WHERE status = 'closed' AND id = '".$obj->game_id."'
			 UNION
			 SELECT id, winner, p3 AS 'username' FROM lg_laddergames WHERE status = 'closed' AND id = '".$obj->game_id."'
			 UNION
			 SELECT id, winner, p4 AS 'username' FROM lg_laddergames WHERE status = 'closed' AND id = '".$obj->game_id."'
			 UNION
			 SELECT id, winner, p5 AS 'username' FROM lg_laddergames WHERE status = 'closed' AND id = '".$obj->game_id."'
			 UNION
			 SELECT id, winner, p6 AS 'username' FROM lg_laddergames WHERE status = 'closed' AND id = '".$obj->game_id."'
			 UNION
			 SELECT id, winner, p7 AS 'username' FROM lg_laddergames WHERE status = 'closed' AND id = '".$obj->game_id."'
			 UNION
			 SELECT id, winner, p8 AS 'username' FROM lg_laddergames WHERE status = 'closed' AND id = '".$obj->game_id."'
			 UNION
			 SELECT id, winner, p9 AS 'username' FROM lg_laddergames WHERE status = 'closed' AND id = '".$obj->game_id."'
			 UNION
			 SELECT id, winner, p10 AS 'username' FROM lg_laddergames WHERE status = 'closed' AND id = '".$obj->game_id."'
			) T1
			LEFT JOIN lg_winnersreports AS T2 
			ON ( T2.game_id = T1.id AND T2.qui = T1.username )
			LEFT JOIN lg_ladderfollow AS T3
			ON ( T3.game_id = T1.id AND ( T3.player = T1.username OR T3.player = 'Admin' ) )
			WHERE T2.game_id IS NOT NULL
			AND T2.winner <> T1.winner
			AND CONCAT(T2.winner, T3.resultat) <> 'noneleft'";
		mysql_query($ins);
		
	}

	$upd = "UPDATE lg_ladder_stats_games SET new = 0";
	mysql_query($upd);
	
	$admins = array("LadderGuardian");
	
	foreach ($admins AS $admin) {
		$req = "SELECT COUNT(*) AS 'banlife' FROM lg_ladderbans WHERE par_qui = '".$admin."' AND duree = 0";
		$res = mysql_query($req) or die(mysql_error());
		$obj = mysql_fetch_object($res);
		$text = $obj->banlife." BANLIFE";
		
		$req = "SELECT admin, COUNT(*) AS 'bans', SUM(`force`) AS 'jours' FROM lg_ladderbans_follow WHERE admin = '".$admin."' AND afficher = 1 AND `force` > 0 AND `type` = 'ban' GROUP BY admin";
		$res = mysql_query($req) or die(mysql_error());
		$obj = mysql_fetch_object($res);
		$text = $obj->bans." BANS, ".$obj->jours." JOURS, ".$text;
		
		$img = @imagecreatefrompng("/home/www/ligue/guardian_sign/base_sign_ladder_admin.png");
		
		$black = imagecolorallocate($img, 0, 0, 0);
		$red = imagecolorallocate($img, 255, 0, 0);
		$white = imagecolorallocate($img, 255, 255, 255);
		
		$font = "/home/www/ligue/guardian_sign/visitor1.ttf";
		$fontsize = 10;
		
		imagettftext($img, $fontsize, 0, 5, 14, $black, $font, $admin);	
		imagettftext($img, $fontsize, 0, 6, 13, $white, $font, $admin);
		
		$box = imagettfbbox($fontsize, 0, $font, $text);
		$size = $box[2] - $box[0];

		imagettftext($img, $fontsize, 0, 464 - $size, 14, $red, $font, $text);
		imagettftext($img, $fontsize, 0, 465 - $size, 13, $white, $font, $text);
		
		imagepng($img, "/home/www/ligue/guardian_sign/".$admin.".png");
		imagedestroy($img);
	}

	/*
	if ($c > 0) {
		//Admin Log
		$admin_req="INSERT INTO lg_adminlog (qui, quand, quoi) VALUES ('CronTask', '".time()."', 'Mise à Jour des stats ladders (".$c." games ajoutées)')";
		mysql_query($admin_req);
	}
	*/
?>
