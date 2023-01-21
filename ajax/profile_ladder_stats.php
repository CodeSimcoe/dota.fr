<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/var/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'classes/LadderStates.php';
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';
	
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
		$table .= '<colgroup><col /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="50" /></colgroup>';
		$table .= '<tr><td align="right" colspan="7"><strong>'.$t.'</strong></td></tr>';
		$table .= '<tr><td class="line" colspan="7"></td></tr>';
		$m = 0;
		while ($o = mysql_fetch_object($q)) {
			$m = $m + 1;
			$c = (($m % 2 == 0) ? ' class="alternate"' : '');
			$mkt = mktime(0, 0, 0, $o->month, 1, $o->year);
			$table .= '<tr>';
			$table .= '<td style="text-align:left;"'.$c.'>&nbsp;<a href="javascript:void(0);" onclick="statsMonthGet(\'\', '.date("Y", $mkt).', '.date("n", $mkt).')">'.date("F Y", $mkt).'</a></td>';
			$table .= '<td title="Nombre de parties" style="text-align:right;"'.$c.'>'.$o->games.'</td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="win" title="Victoires">'.$o->wins.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="lose" title="D&eacute;faites">'.$o->loses.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="draw" title="Parties quitt&eacute;es">'.$o->lefts.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="info" title="Non venu">'.$o->aways.'</span></td>';
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
		$table .= '<colgroup><col /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="50" /></colgroup>';
		$table .= '<tr><td align="right" colspan="7"><strong>'.$t.'</strong></td></tr>';
		$table .= '<tr><td class="line" colspan="7"></td></tr>';
		$m = 0;
		while ($o = mysql_fetch_object($q)) {
			$m = $m + 1;
			$c = (($m % 2 == 0) ? ' class="alternate"' : '');
			$mkt = mktime(0, 0, 0, $o->month, $o->day, $o->year);
			$table .= '<tr>';
			$table .= '<td style="text-align:left;"'.$c.'>&nbsp;<a href="javascript:void(0);" onclick="statsDayGet(\'\', '.date("Y", $mkt).', '.date("n", $mkt).', '.date("j", $mkt).')">'.date("d F Y, l", $mkt).'</a></td>';
			$table .= '<td title="Nombre de parties" style="text-align:right;"'.$c.'>'.$o->games.'</td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="win" title="Victoires">'.$o->wins.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="lose" title="D&eacute;faites">'.$o->loses.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="draw" title="Parties quitt&eacute;es">'.$o->lefts.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'><span class="info" title="Non venu">'.$o->aways.'</span></td>';
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
			
			if ($o->resultat == 'win') {
				$txt = 'Victoire';
				$css = ' class="win"';
			} elseif ($o->resultat == 'lose') {
				$txt = 'Perdue';
				$css = ' class="lose"';
			} elseif ($o->resultat == 'left') {
				$txt = 'Quitt&eacute;e';
				$css = ' class="draw"';
			} elseif ($o->resultat == 'away') {
				$txt = 'Pas venu';
				$css = ' class="info"';
			} else {
				$txt = 'Ferm&eacute;e';
				$css = '';
			}
			$table .= '<tr>';
			$table .= '<td style="text-align:left;"'.$c.'>&nbsp;<a href="?f=ladder_game&id='.$o->id.'">'.date("H:i", $o->opened).', Game #'.$o->id.'</a></td>';
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
				 SUM(balance) as 'balance'
				FROM
				 lg_ladder_stats_players
				WHERE
				 player = '".$player."'
				AND games > 0
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
				 SUM(balance) as 'balance'
				FROM
				 lg_ladder_stats_players
				WHERE
				 player = '".$player."'
				AND year = '".$year."'
				AND month = '".$month."'
				AND games > 0
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
				SELECT
				 l.id,
				 l.opened,
				 f.player,
				 f.resultat,
				 f.xp
				FROM (
					SELECT
					 game_id
					FROM
					 lg_ladder_stats_games
					WHERE
					 year = '".$year."'
					AND month = '".$month."'
					AND day = '".$day."'
				) AS g
				INNER JOIN lg_laddergames AS l ON l.id = g.game_id
				LEFT JOIN lg_ladderfollow AS f ON f.game_id = l.id
				WHERE
				 f.player = '".$player."'
				AND f.xp != 0
				ORDER BY
				 l.opened DESC";
			echo createGamesTable(date("l d F Y", $dday), $req);
		}
	}

?>