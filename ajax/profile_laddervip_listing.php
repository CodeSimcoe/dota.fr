<?php

	include('profile_laddervip_listing_functions.php');

	if ($player != '') {
		if ($year + $month + $day == 0) {
			$req = "
				SELECT
				 year,
				 month,
				 SUM(games) AS 'games',
				 SUM(wins) AS 'wins',
				 SUM(loses) AS 'loses',
				 SUM(lefts) AS 'lefts',
				 SUM(aways) AS 'aways',
				 SUM(closed) AS 'closed',
				 SUM(balance) as 'balance'
				FROM
				 lg_laddervip_stats_players
				WHERE
				 player = '".$player."'
				GROUP BY
				 year,
				 month
				ORDER BY
				 year DESC,
				 month DESC";
			echo createDatesTable('Listing Ladder VIP', $req, 'vip', '', 'M');
			echo '<div id="plsbm"></div>';
			echo '<div id="plsbd"></div>';
		} else if ($isday == 0 AND $year != 0 AND $month != 0) {
			$mon = mktime(0, 0, 0, $month, 1, $year);
			$req = "
				SELECT
				 year,
				 month,
				 day,
				 SUM(games) AS 'games',
				 SUM(wins) AS 'wins',
				 SUM(loses) AS 'loses',
				 SUM(lefts) AS 'lefts',
				 SUM(aways) AS 'aways',
				 SUM(closed) AS 'closed',
				 SUM(balance) as 'balance'
				FROM
				 lg_laddervip_stats_players
				WHERE
				 player = '".$player."'
				AND year = '".$year."'
				AND month = '".$month."'
				GROUP BY
				 year,
				 month,
				 day
				ORDER BY
				 year DESC,
				 month DESC,
				 day DESC";
			echo '<br /><br />';
			echo createDatesTable(date("F Y", $mon), $req, 'vip', '', 'D');
		} else if ($isday == 1 AND $year != 0 AND $month != 0 AND $day != 0) {
			$dday = mktime(0, 0, 0, $month, $day, $year);
			$req = "
				SELECT DISTINCTROW 
				 r.game_id AS 'id',
				 g.opened,
				 r.player,
				 CASE 
				  WHEN lefts = 1 THEN 'left'
				  ELSE
				   CASE 
					WHEN aways = 1 THEN 'away'
					ELSE ''
				   END
				 END AS 'resultat',
				 r.balance AS 'xp'
				FROM
				 lg_laddervip_stats_results AS r
				INNER JOIN
				 lg_laddervip_games AS g
				ON
				 g.id = r.game_id
				WHERE
				 r.year = '".$year."'
				AND r.month = '".$month."'
				AND r.day = '".$day."'
				AND r.player = '".$player."'
				ORDER BY
				 g.opened DESC";
			echo '<br /><br />';
			echo createGamesTable(date("l d F Y", $dday), $req);
		}
	}

?>