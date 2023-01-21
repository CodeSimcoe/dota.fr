<?php

	include('profile_laddervip_listing_functions.php');

	if ($player != '') {
	
		$req = "
			SELECT
			 player,
			 iscap,
			 CASE WHEN iscap = 0 THEN 'Capitaine'
			 ELSE
			  CASE WHEN iscap = 1 THEN '1<sup>st</sup>&nbsp;picked'
			  ELSE
 			   CASE WHEN iscap = 2 THEN '2<sup>nd</sup>&nbsp;picked'
			   ELSE
 			    CASE WHEN iscap = 3 THEN '3<sup>rd</sup>&nbsp;picked'
			    ELSE
 			     CASE WHEN iscap = 4 THEN '4<sup>th</sup>&nbsp;picked'
			     ELSE 'Last&nbsp;picked'
			     END
			    END
			   END
			  END
			 END AS 'iscaplib',
			 COUNT(game_id) as 'games',
			 SUM(wins) as 'wins',
			 SUM(loses) as 'loses',
			 SUM(lefts) as 'lefts',
			 SUM(aways) as 'aways',
			 SUM(closed) as 'closed',
			 SUM(balance) as 'balance'
			FROM
			 lg_laddervip_stats_results
			WHERE
			 player = '".$player."'
			GROUP BY
			 player,
			 iscap,
			 iscaplib
			ORDER BY
			 iscap ASC";
		echo createCaptainTable('Picks Order Ladder VIP', $req, 'vip_captains');
		echo '<div id="plsba"></div>';

	}

?>