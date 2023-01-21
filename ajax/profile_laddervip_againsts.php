<?php

	include('profile_laddervip_listing_functions.php');
	
	if ($player != '') {

		$req = "
			SELECT
			 COUNT(played_against)
			FROM
			 lg_laddervip_stats_againsts
			WHERE
			 player = '".$player."'
			GROUP BY
			 played_against";
		$qry = mysql_query($req) or die(mysql_error());
		$total = mysql_num_rows($qry);
		
		$totalpages = (int)($total / $pagesize);
		$totalpages += ($total % $pagesize == 0) ? 0 : 1;
		
		if ($current > $totalpages - 1) {
			$current = $totalpages - 1;
		}
		
		$req = "
			SELECT
			 played_against AS 'pwith',
			 COUNT(game_id) as 'games',
			 SUM(wins) as 'wins',
			 SUM(loses) as 'loses',
			 SUM(lefts) as 'lefts',
			 SUM(aways) as 'aways',
			 SUM(closed) as 'closed',
			 SUM(balance) as 'balance'
			FROM
			 lg_laddervip_stats_againsts
			WHERE
			 player = '".$player."'
			GROUP BY
			 played_against
			ORDER BY
			 SUM(balance) ASC,
			 COUNT(game_id) ASC,
			 SUM(wins) ASC,
			 SUM(loses) DESC,
			 SUM(lefts) ASC,
			 SUM(aways) ASC,
			 SUM(closed) ASC,
			 played_against ASC
			LIMIT ".($pagesize * $current).", ".$pagesize;
		echo createPagedTable($total.' Adversaires Ladder VIP', $req, $totalpages, $current, 'vip_againsts', 'againsts');
		echo '<div id="plsba"></div>';

	}

?>