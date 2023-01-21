<?php

	require_once '/home/www/ligue/mysql_connect.php';
	require_once '/home/www/ligue/classes/AdminLog.php';
	
	$temps_debut = microtime(true);

	$count = 0;
	$req = "
		SELECT DISTINCT lg.id AS 'game_id' 
		FROM lg_laddervip_games lg
		LEFT JOIN lg_laddervip_stats ls
		ON lg.id = ls.game_id
		WHERE lg.status = 'closed'
		AND lg.pp1 <> '' AND lg.pp2 <> '' AND lg.pp3 <> '' AND lg.pp4 <> '' AND lg.pp5 <> '' AND lg.pp6 <> '' AND lg.pp7 <> '' AND lg.pp8 <> ''
		AND ls.played IS NULL";
	$res = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($res) != 0) {
		$count = mysql_num_rows($res);
		while ($obj = mysql_fetch_object($res)) {
			$ins_res = mysql_query("
				INSERT INTO lg_laddervip_stats (game_id, opened, `year`, `month`, `day`, username, side, pick, played, closed, win, lose, away, `left`, xp)
				SELECT T.game_id, T.when, YEAR(FROM_UNIXTIME(T.when)), MONTH(FROM_UNIXTIME(T.when)), DAY(FROM_UNIXTIME(T.when)), T.username, T.side, T.pick, T.played, T.closed, 0, 0, 0, 0, 0 
				FROM (
					SELECT id AS 'game_id', opened AS 'when', cap1  AS 'username', 'se' AS 'side', 0 AS 'pick', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddervip_games WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', pp1   AS 'username', 'se' AS 'side', 1 AS 'pick', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddervip_games WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', pp4   AS 'username', 'se' AS 'side', 3 AS 'pick', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddervip_games WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', pp5   AS 'username', 'se' AS 'side', 3 AS 'pick', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddervip_games WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', pp8   AS 'username', 'se' AS 'side', 5 AS 'pick', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddervip_games WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', cap2  AS 'username', 'sc' AS 'side', 0 AS 'pick', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddervip_games WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', pp2   AS 'username', 'sc' AS 'side', 2 AS 'pick', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddervip_games WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', pp3   AS 'username', 'sc' AS 'side', 2 AS 'pick', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddervip_games WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', pp6   AS 'username', 'sc' AS 'side', 4 AS 'pick', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddervip_games WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', pp7   AS 'username', 'sc' AS 'side', 4 AS 'pick', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddervip_games WHERE id = ".$obj->game_id."
				) T
			") or die(mysql_error());
			$upd_res = mysql_query("
				UPDATE lg_laddervip_stats ls, lg_laddervip_follow lf SET
					ls.win = CASE WHEN lf.resultat = 'win' THEN 1 ELSE 0 END,
					ls.lose = CASE WHEN lf.resultat = 'lose' THEN 1 ELSE 0 END,
					ls.`left` = CASE WHEN lf.resultat = 'left' THEN 1 ELSE 0 END,
					ls.away = CASE WHEN lf.resultat = 'away' THEN 1 ELSE 0 END,
					ls.xp = lf.xp
				WHERE lf.game_id = ls.game_id 
				AND lf.player = ls.username
				AND ls.game_id = ".$obj->game_id."
			") or die(mysql_error());
		}
	}
	
	$temps_fin = microtime(true);
	
	/*
	if ($count > 0) {
		$al = new AdminLog('Ladder Stats: '.$count.' games, '.round($temps_fin - $temps_debut, 4), 'LadderGuardian', time());
		$al->save_log();
	}
	*/

?>