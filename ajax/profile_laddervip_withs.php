<?php

	include('profile_laddervip_listing_functions.php');

	if ($player != '') {
	
		$req = "
			SELECT
			 COUNT(played_with)
			FROM
			 lg_laddervip_stats_withs
			WHERE
			 player = '".$player."'
			GROUP BY
			 played_with";
		$qry = mysql_query($req) or die(mysql_error());
		$total = mysql_num_rows($qry);
		
		$totalpages = (int)($total / $pagesize);
		$totalpages += ($total % $pagesize == 0) ? 0 : 1;
		
		if ($current > $totalpages - 1) {
			$current = $totalpages - 1;
		}
		
		$req = "
			SELECT
			 played_with AS 'pwith',
			 COUNT(game_id) as 'games',
			 SUM(wins) as 'wins',
			 SUM(loses) as 'loses',
			 SUM(lefts) as 'lefts',
			 SUM(aways) as 'aways',
			 SUM(closed) as 'closed',
			 SUM(balance) as 'balance'
			FROM
			 lg_laddervip_stats_withs
			WHERE
			 player = '".$player."'
			GROUP BY
			 played_with
			ORDER BY
			 SUM(balance) DESC,
			 COUNT(game_id) DESC,
			 SUM(wins) DESC,
			 SUM(loses) ASC,
			 SUM(lefts) DESC,
			 SUM(aways) DESC,
			 SUM(closed) DESC,
			 played_with ASC
			LIMIT ".($pagesize * $current).", ".$pagesize;
		echo createPagedTable($total.' Alli&eacute;s Ladder VIP', $req, $totalpages, $current, 'vip_withs', 'withs');
		echo '<div id="plsba"></div>';

	}

?>