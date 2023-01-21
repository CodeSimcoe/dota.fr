<?php
	if (!ArghSession::is_gold()) exit;

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
	if (($day == 0 AND $isday == 1) OR $month == 0 OR $year == 0) {
		$req = "SELECT year, month, day FROM lg_ladder_stats_days ORDER BY year DESC, month DESC, day DESC LIMIT 1";
		$qry = mysql_query($req) or die(mysql_error());
		$obj = mysql_fetch_object($qry);
		$day = $obj->day;
		$month = $obj->month;
		$year = $obj->year;
	}
	
	$monthlib = date("F", mktime(0, 0, 0, $month, 1, $year));
	
	$mreq = "SELECT year, month FROM lg_ladder_stats_months ORDER BY year DESC, month DESC";
	$mqry = mysql_query($mreq) or die(mysql_error());
	
	$dreq = "SELECT DISTINCTROW year, month, day FROM lg_ladder_stats_days WHERE year = '".$year."' AND month = '".$month."' ORDER BY year DESC, month DESC, day DESC";
	$dqry = mysql_query($dreq) or die(mysql_error());

	if ($isday == 1) {
		$cmreq = "SELECT year, month, day, games, players FROM lg_ladder_stats_days WHERE year = '".$year."' AND month = '".$month."' AND day = '".$day."'";
		$cmqry = mysql_query($cmreq) or die(mysql_error());
	} else {
		$cmreq = "SELECT year, month, games, players FROM lg_ladder_stats_months WHERE year = '".$year."' AND month = '".$month."'";
		$cmqry = mysql_query($cmreq) or die(mysql_error());
	}
	
	$basereq = "
		SELECT
		 player,
		 SUM(games) AS 'games',
		 SUM(wins) AS 'wins',
		 SUM(loses) AS 'loses',
		 SUM(lefts) AS 'lefts',
		 SUM(aways) AS 'aways',
		 SUM(balance) AS 'balance'
		FROM
		 lg_ladder_stats_players";
	
	function month_TXP($b, $y, $m) {
		$r = $b." WHERE year = '".$y."' AND month = '".$m."'";
		$r .= " GROUP BY player";
		$r = surround_limit($r, "s.games > 0", "s.balance DESC, s.games ASC, s.wins ASC, s.loses ASC", 5);
		return $r;
	}
	function month_TGA($b, $y, $m) {
		$r = $b." WHERE year = '".$y."' AND month = '".$m."'";
		$r .= " GROUP BY player";
		$r = surround_limit($r, "s.games > 0", "s.games DESC, s.balance DESC, s.wins DESC, s.loses DESC", 5);
		return $r;
	}
	function month_WXP($b, $y, $m) {
		$r = $b." WHERE year = '".$y."' AND month = '".$m."'";
		$r .= " GROUP BY player";
		$r = surround_limit($r, "s.games > 0", "s.balance ASC, s.games ASC, s.wins ASC, s.loses ASC", 5);
		return $r;
	}
	function month_TLE($b, $y, $m) {
		$r = $b." WHERE year = '".$y."' AND month = '".$m."'";
		$r .= " GROUP BY player";
		$r = surround_limit($r, "s.games > 0 AND s.lefts > 0", "s.lefts DESC, s.games ASC, s.wins ASC, s.loses ASC", 5);
		return $r;
	}
	function month_TAW($b, $y, $m) {
		$r = $b." WHERE year = '".$y."' AND month = '".$m."'";
		$r .= " GROUP BY player";
		$r = surround_limit($r, "s.games > 0 AND s.aways > 0", "s.aways DESC, s.games ASC, s.wins ASC, s.loses ASC", 5);
		return $r;
	}
	
	function day_TXP($b, $y, $m, $d) {
		$r = $b." WHERE year = '".$y."' AND month = '".$m."' AND day = '".$d."'";
		$r .= " GROUP BY player";
		$r = surround_limit($r, "s.games > 0", "s.balance DESC, s.games ASC, s.wins ASC, s.loses ASC", 5);
		return $r;
	}
	function day_TGA($b, $y, $m, $d) {
		$r = $b." WHERE year = '".$y."' AND month = '".$m."' AND day = '".$d."'";
		$r .= " GROUP BY player";
		$r = surround_limit($r, "s.games > 0", "s.games DESC, s.balance DESC, s.wins DESC, s.loses DESC", 5);
		return $r;
	}
	function day_WXP($b, $y, $m, $d) {
		$r = $b." WHERE year = '".$y."' AND month = '".$m."' AND day = '".$d."'";
		$r .= " GROUP BY player";
		$r = surround_limit($r, "s.games > 0", "s.balance ASC, s.games ASC, s.wins ASC, s.loses ASC", 5);
		return $r;
	}
	function day_TLE($b, $y, $m, $d) {
		$r = $b." WHERE year = '".$y."' AND month = '".$m."' AND day = '".$d."'";
		$r .= " GROUP BY player";
		$r = surround_limit($r, "s.games > 0 AND s.lefts > 0", "s.lefts DESC, s.games ASC, s.wins ASC, s.loses ASC", 5);
		return $r;
	}
	function day_TAW($b, $y, $m, $d) {
		$r = $b." WHERE year = '".$y."' AND month = '".$m."' AND day = '".$d."'";
		$r .= " GROUP BY player";
		$r = surround_limit($r, "s.games > 0 AND s.aways > 0", "s.aways DESC, s.games ASC, s.wins ASC, s.loses ASC", 5);
		return $r;
	}
	
	function surround_limit($r, $w, $o, $l) {
		$r = "SELECT s.player, s.games, s.wins, s.loses, s.lefts, s.aways, s.balance FROM (".$r.") AS s";
		$r .= " WHERE ".$w;
		$r .= " ORDER BY ".$o;
		$r .= " LIMIT ".$l;
		return $r;
	}
	function createStatsTable($t, $r) {
		$q = mysql_query($r) or die(mysql_error());
		$table = '<table style="width: 100%;">';
		$table .= '<colgroup><col /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="50" /></colgroup>';
		$table .= '<tr><td align="right" colspan="7"><strong>'.$t.'</strong></th></tr>';
		$table .= '<tr><td class="line" colspan="7"></td></tr>';
		$m = 0;
		while ($o = mysql_fetch_object($q)) {
			$m = $m + 1;
			$c = (($m % 2 == 0) ? ' class="alternate"' : '');
			$table .= '<tr>';
			$table .= '<td style="text-align:left;"'.$c.'>'.$m.'.&nbsp;<a href="?f=player_profile&player='.$o->player.'">'.$o->player.'</a></td>';
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
		 
	
	ArghPanel::begin_tag(Lang::LADDER_STATS);
?>
<table class="simple">
	<tr><td colspan="7">
	<table style="width: 100%;">
		<tr>
			<td valign="top" style="width: 25%; padding: 5px;">
				<table style="width: 100%;">
					<tr>
						<th>Archives</th>
					</tr>
					<tr><td class="line"></td></tr>
					<?php
					$mod = 0;
					while ($mon = mysql_fetch_object($mqry)) {
						$mod = $mod + 1;
						$cell = (($mod % 2 == 0) ? ' class="alternate"' : '');
						echo '<tr><td'.$cell.'><a href="?f=ladder_stats&year='.$mon->year.'&month='.$mon->month.'">'.date("F Y", mktime(0, 0, 0, $mon->month, 1, $mon->year)).'</a></td></tr>';
					}
					?>
				</table>
				<br />
				<table style="width: 100%;">
					<tr>
						<th><?php echo $monthlib.' '.$year ?></th>
					</tr>
					<tr><td class="line"></td></tr>
					<?php
					$mod = 0;
					while ($da = mysql_fetch_object($dqry)) {
						$mod = $mod + 1;
						$cell = (($mod % 2 == 0) ? ' class="alternate"' : '');
						echo '<tr><td'.$cell.'><a href="?f=ladder_stats&year='.$da->year.'&month='.$da->month.'&day='.$da->day.'">'.date("j F", mktime(0, 0, 0, $da->month, $da->day, $da->year)).'</a></td></tr>';
					}
					?>
				</table>
			</td>
			<td valign="top" style="width: 75%; padding: 5px;">
			<?php
			if (mysql_num_rows($cmqry) != 0) {
				$cm = mysql_fetch_object($cmqry);
				if ($isday == 1) {
					$dtitle = date("j F Y", mktime(0, 0, 0, $cm->month, $cm->day, $cm->year));
				} else {
					$dtitle = date("F Y", mktime(0, 0, 0, $cm->month, 1, $cm->year));
				}
				echo '<h4 style="margin: 0px 0px 5px 0px; padding: 0px 0px 2px 0px; text-align: right; color: #2C99DB; border-bottom: solid 1px #2C99DB;">'.$dtitle.'</h4>';
				echo '<p style="margin: 0px 0px 17px 0px; padding: 0px; text-align: right;">'.$cm->games.' parties jouée(s) par '.$cm->players.' joueurs différents</p>';
				echo '<p style="margin: 0px; padding: 0px; text-align: center;">';
				if ($isday == 0) {
					echo "<br />";
					echo createStatsTable("TOP XP", month_TXP($basereq, $year, $month));
					echo "<br />";
					echo createStatsTable("TOP GAMES", month_TGA($basereq, $year, $month));
				} else {
					echo "<br />";
					echo createStatsTable("TOP XP", day_TXP($basereq, $year, $month, $day));
					echo "<br />";
					echo createStatsTable("TOP GAMES", day_TGA($basereq, $year, $month, $day));
				}
				if (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN))) {
					if ($isday == 0) {
						echo "<br />";
						echo createStatsTable("WORST XP", month_WXP($basereq, $year, $month));
						echo "<br />";
						echo createStatsTable("TOP LEAVERS", month_TLE($basereq, $year, $month));
						echo "<br />";
						echo createStatsTable("TOP AWAYS", month_TAW($basereq, $year, $month));
					} else {
						echo "<br />";
						echo createStatsTable("WORST XP", day_WXP($basereq, $year, $month, $day));
						echo "<br />";
						echo createStatsTable("TOP LEAVERS", day_TLE($basereq, $year, $month, $day));
						echo "<br />";
						echo createStatsTable("TOP AWAYS", day_TAW($basereq, $year, $month, $day));
					}
				}
				echo '</p>';
			}
			?>
			</td>
		</tr>
	</table>
	</td></tr>
</table>
<?php
	ArghPanel::end_tag();
?>