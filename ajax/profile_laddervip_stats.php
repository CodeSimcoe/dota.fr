<?php
	exit;
	
	require('mysql_connect.php');
	
	$month = 0;
	if (isset($_GET['month'])) {
		$month = (int)$_GET['month'];
		if ($month < 1 or $month > 12) {
			$month = 0;
		}
	}
	
	$year = 0;
	if (isset($_GET['year'])) {
		$year = (int)$_GET['year'];
		if ($year < 2008) {
			$year = 0;
		}
	}
	
	$isday = 1;
	$day = 0;
	if (isset($_GET['day'])) {
		$day = (int)$_GET['day'];
		if ($day < 1 or $day > 31) {
			$day = 0;
		}
	} else {
		$isday = 0;
	}
	
	$player = '';
	if (isset($_GET['player'])) {
		$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
	}
		
	function createMonthsTable($t, $r) {
		$q = mysql_query($r) or die(mysql_error());
		$table = '<table style="width: 100%;">';
		$table .= '<colgroup><col /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="50" /></colgroup>';
		$table .= '<tr><td align="right" colspan="8"><strong>'.$t.'</strong></td></tr>';
		$table .= '<tr><td class="line" colspan="8"></td></tr>';
		$m = 0;
		while ($o = mysql_fetch_object($q)) {
			$m = $m + 1;
			$c = (($m % 2 == 0) ? ' class="alternate"' : '');
			$mkt = mktime(0, 0, 0, $o->month, 1, $o->year);
			$table .= '<tr>';
			$table .= '<td style="text-align:left;"'.$c.'>&nbsp;<a href="javascript:void(0);" onclick="statsMonthGet(\'vip\', '.date("Y", $mkt).', '.date("n", $mkt).')">'.date("F Y", $mkt).'</a></td>';
			$table .= '<td title="Nombre de parties" style="text-align:right;"'.$c.'>'.$o->games.'</td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="win" title="Victoires">'.$o->wins.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="lose" title="D&eacute;faites">'.$o->loses.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="draw" title="Parties quitt&eacute;es">'.$o->lefts.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="info" title="Non venu">'.$o->aways.'</span></td>';
			$table .= '<td title="Ferm&eacute;e" style="text-align:right;"'.$c.'>'.$o->closed.'</td>';
			$table .= '<td title="Total XP" style="text-align:right;"'.$c.'>'.(($o->balance > 0) ? '+'.$o->balance : $o->balance).'</td>';
			$table .= '</tr>';
		}
		$table .= '</table>';
		$table .= '<div id="plsbm"></div>';
		$table .= '<div id="plsbd"></div>';
		return $table;
	}
	
	function createMonthTable($t, $r) {
		$q = mysql_query($r) or die(mysql_error());
		$table = '<br /><br /><table style="width: 100%;">';
		$table .= '<colgroup><col /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="50" /></colgroup>';
		$table .= '<tr><td align="right" colspan="8"><strong>'.$t.'</strong></td></tr>';
		$table .= '<tr><td class="line" colspan="8"></td></tr>';
		$m = 0;
		while ($o = mysql_fetch_object($q)) {
			$m = $m + 1;
			$c = (($m % 2 == 0) ? ' class="alternate"' : '');
			$mkt = mktime(0, 0, 0, $o->month, $o->day, $o->year);
			$table .= '<tr>';
			$table .= '<td style="text-align:left;"'.$c.'>&nbsp;<a href="javascript:void(0);" onclick="statsDayGet(\'vip\', '.date("Y", $mkt).', '.date("n", $mkt).', '.date("j", $mkt).')">'.date("d F Y, l", $mkt).'</a></td>';
			$table .= '<td title="Nombre de parties" style="text-align:right;"'.$c.'>'.$o->games.'</td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="win" title="Victoires">'.$o->wins.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="lose" title="D&eacute;faites">'.$o->loses.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="draw" title="Parties quitt&eacute;es">'.$o->lefts.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="info" title="Non venu">'.$o->aways.'</span></td>';
			$table .= '<td title="Ferm&eacute;e" style="text-align:right;"'.$c.'>'.$o->closed.'</td>';
			$table .= '<td title="Total XP" style="text-align:right;"'.$c.'>'.(($o->balance > 0) ? '+'.$o->balance : $o->balance).'</td>';
			$table .= '</tr>';
		}
		$table .= '</table>';
		return $table;
	}

	function createGamesTable($t, $r) {
		$q = mysql_query($r) or die(mysql_error());
		$table = '<br /><br /><table style="width: 100%;">';
		$table .= '<colgroup><col /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="50" /></colgroup>';
		$table .= '<tr><td align="right" colspan="7"><strong>'.$t.'</strong></td></tr>';
		$table .= '<tr><td class="line" colspan="7"></td></tr>';
		$m = 0;
		while ($o = mysql_fetch_object($q)) {
			$m = $m + 1;
			$c = (($m % 2 == 0) ? ' class="alternate"' : '');
			
			if ($o->xp > 0) {
				$txt = 'Victoire';
				$css = ' class="win"';
			} elseif ($o->xp == 0) {
				$txt = 'Ferm&eacute;e';
				$css = '';
			} elseif ($o->xp < 0 and $o->resultat == 'left') {
				$txt = 'Quitt&eacute;e';
				$css = ' class="draw"';
			} elseif ($o->xp < 0 and $o->resultat == 'away') {
				$txt = 'Pas venu';
				$css = ' class="info"';
			} else {
				$txt = 'Perdue';
				$css = ' class="lose"';
			}
			$table .= '<tr>';
			$table .= '<td style="text-align:left;"'.$c.'>&nbsp;<a href="?f=laddervip_game&id='.$o->id.'">'.date("H:i", $o->opened).', Game #'.$o->id.'</a></td>';
			$table .= '<td style="text-align:right;"'.$c.'>&nbsp;</td>';
			$table .= '<td colspan="3" style="text-align:right;"'.$c.'><span'.$css.'>'.$txt.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'>&nbsp;</td>';
			$table .= '<td style="text-align:right;"'.$c.'><span'.$css.'>'.(($o->xp > 0) ? '+'.$o->xp : $o->xp).'</span></td>';
			$table .= '</tr>';
		}
		$table .= '</table>';
		return $table;
	}

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
			echo createMonthsTable('Statistiques Ladder', $req);
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
			echo createMonthTable(date("F Y", $mon), $req);
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
			echo createGamesTable(date("l d F Y", $dday), $req);
		}
	}

?>