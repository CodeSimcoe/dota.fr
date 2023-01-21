<?php

	require_once '/home/www/ligue/mysql_connect.php';
	require_once '/home/www/ligue/classes/AdminLog.php';
	
	$temps_debut = microtime(true);

	$count = 0;
	$req = "
		SELECT DISTINCT lg.id AS 'game_id' 
		FROM lg_laddergames lg
		LEFT JOIN lg_ladder_stats ls
		ON lg.id = ls.game_id
		WHERE lg.status = 'closed'
		AND ls.played IS NULL";
	$res = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($res) != 0) {
		$count = mysql_num_rows($res);
		while ($obj = mysql_fetch_object($res)) {
			$ins_res = mysql_query("
				INSERT INTO lg_ladder_stats (game_id, opened, `year`, `month`, `day`, username, side, played, closed, win, lose, away, `left`, xp)
				SELECT T.game_id, T.when, YEAR(FROM_UNIXTIME(T.when)), MONTH(FROM_UNIXTIME(T.when)), DAY(FROM_UNIXTIME(T.when)), T.username, T.side, T.played, T.closed, 0, 0, 0, 0, 0 
				FROM (
					SELECT id AS 'game_id', opened AS 'when', p1  AS 'username', 'se' AS 'side', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddergames WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', p2  AS 'username', 'se' AS 'side', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddergames WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', p3  AS 'username', 'se' AS 'side', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddergames WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', p4  AS 'username', 'se' AS 'side', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddergames WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', p5  AS 'username', 'se' AS 'side', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddergames WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', p6  AS 'username', 'sc' AS 'side', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddergames WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', p7  AS 'username', 'sc' AS 'side', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddergames WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', p8  AS 'username', 'sc' AS 'side', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddergames WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', p9  AS 'username', 'sc' AS 'side', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddergames WHERE id = ".$obj->game_id."
					UNION ALL
					SELECT id AS 'game_id', opened AS 'when', p10 AS 'username', 'sc' AS 'side', CASE WHEN winner <> 'none' THEN 1 ELSE 0 END as 'played', CASE WHEN winner = 'none' THEN 1 ELSE 0 END as 'closed' FROM lg_laddergames WHERE id = ".$obj->game_id."
				) T
			") or die(mysql_error());
			$upd_res = mysql_query("
				UPDATE lg_ladder_stats ls, lg_ladderfollow lf SET
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
	
	$truncate_res = mysql_query("
		TRUNCATE TABLE lg_ladder_stats_ranks
	") or die(mysql_error());
	
	$ranks_res = mysql_query("
		INSERT INTO lg_ladder_stats_ranks (player, played, closed, win, lose, away, `left`, xp)
		SELECT 
			username,
			SUM(played),
			SUM(closed),
			SUM(win),
			SUM(lose),
			SUM(away),
			SUM(`left`),
			1600 + SUM(xp)
		FROM `lg_ladder_stats`
		GROUP BY username
		HAVING SUM(played) > 1
		ORDER BY SUM(xp) DESC
	") or die(mysql_error());
	
	$temps_fin = microtime(true);
	
	/*
	if ($count > 0) {
		$al = new AdminLog('Ladder Stats: '.$count.' games, '.round($temps_fin - $temps_debut, 4), 'LadderGuardian', time());
		$al->save_log();
	}
	*/

?>