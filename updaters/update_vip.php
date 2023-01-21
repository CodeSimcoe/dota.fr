<?php
	include('/home/www/ligue/mysql_connect.php');
	include('/home/www/ligue/laddervip_functions.php');

	//Cloture des games Ladder VIP
	$query = "SELECT * FROM lg_laddervip_games WHERE status = 'playing'";
	$table = mysql_query($query);
	if (mysql_num_rows($table) > 0) {
		while ($line = mysql_fetch_object($table)) {
			//Close - 3h 
			if (time() - $line->opened > 10800) {
				//Reports pour cette game
				$none = 0;
				$se = 0;
				$sc = 0;
				$sreq = "SELECT * FROM lg_laddervip_winnersreports WHERE game_id = '".$line->id."'";
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
?>
