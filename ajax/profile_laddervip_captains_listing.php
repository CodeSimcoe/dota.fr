<?php

	include('profile_laddervip_listing_functions.php');

	if ($player != '' && $pwith != '') {
		$twith = '';
		switch ($pwith) {
			case 0: $twith = 'Capitaine'; break;
			case 1: $twith = '1<sup>st</sup>&nbsp;picked'; break;
			case 2: $twith = '2<sup>nd</sup>&nbsp;picked'; break;
			case 3: $twith = '3<sup>rd</sup>&nbsp;picked'; break;
			case 4: $twith = '4<sup>th</sup>&nbsp;picked'; break;
			case 5: $twith = 'Last&nbsp;picked'; break;
		}
		if ($year + $month + $day == 0) {
			$req = "
				SELECT
				 r.year,
				 r.month,
				 COUNT(r.game_id) AS 'games',
				 SUM(r.wins) AS 'wins',
				 SUM(r.loses) AS 'loses',
				 SUM(r.lefts) AS 'lefts',
				 SUM(r.aways) AS 'aways',
				 SUM(r.closed) AS 'closed',
				 SUM(r.balance) as 'balance'
				FROM
				 lg_laddervip_stats_results AS r
				WHERE
				 r.player = '".$player."'
				AND r.iscap = ".$pwith."
				GROUP BY
				 r.year,
				 r.month
				ORDER BY
				 r.year DESC,
				 r.month DESC";
			echo '<br /><br />';
			echo createDatesTable($twith.' - Listing Ladder VIP', $req, 'vip_captains', $pwith, 'M');
			echo '<div id="plsbm"></div>';
			echo '<div id="plsbd"></div>';
		} else if ($isday == 0 AND $year != 0 AND $month != 0) {
			$mon = mktime(0, 0, 0, $month, 1, $year);
			$req = "
				SELECT
				 r.year,
				 r.month,
				 r.day,
				 COUNT(r.game_id) AS 'games',
				 SUM(r.wins) AS 'wins',
				 SUM(r.loses) AS 'loses',
				 SUM(r.lefts) AS 'lefts',
				 SUM(r.aways) AS 'aways',
				 SUM(r.closed) AS 'closed',
				 SUM(r.balance) as 'balance'
				FROM 
				 lg_laddervip_stats_results AS r
				WHERE
				 r.player = '".$player."'
				 AND r.year = '".$year."'
				 AND r.month = '".$month."'
				 AND r.iscap = ".$pwith."
				GROUP BY
				 r.year,
				 r.month,
				 r.day
				ORDER BY
				 r.year DESC,
				 r.month DESC,
				 r.day DESC";
			echo '<br /><br />';
			echo createDatesTable($twith.' - '.date("F Y", $mon), $req, 'vip_captains', $pwith, 'D');
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
				AND r.iscap = ".$pwith."
				ORDER BY
				 g.opened DESC";
			echo '<br /><br />';
			echo createGamesTable($twith.' - '.date("l d F Y", $dday), $req);
		}
	}

?>