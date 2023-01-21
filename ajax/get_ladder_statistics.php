<?php

	require_once '/home/www/ligue/mysql_connect.php';
	require_once '/home/www/ligue/classes/LadderStatisticsModule.php';
	require_once '/home/www/ligue/classes/Alternator.php';
	require_once '/home/www/ligue/classes/RightsMode.php';
	require_once '/home/www/ligue/classes/GooglePie.php';
	require_once '/home/www/ligue/classes/ArghSession.php';
	require_once '/home/www/ligue/ladder_functions.php';

	ArghSession::begin();
	
	require_once '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';

	function extended_encoding($value) {
		$map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';
		$quotient = floor($value / strlen($map));
		$remain = $value - strlen($map) * $quotient;
		return substr($map, $quotient, 1).substr($map, $remain, 1);
	}
	
	function days_add($timestamp, $days = 1) {
        $date = getdate($timestamp);
        $date['mday'] = $date['mday'] + $days;
        return  mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], $date['year']);
    }

	$player = '';
	if (isset($_GET['player'])) {
		$player = substr($_GET['player'], 0, 25);
	}
	if (isset($_POST['player'])) {
		$player = substr($_POST['player'], 0, 25);
	}

	if ($player != '') {
	
		$mode = 'player';
		if (isset($_GET['mode'])){
			$mode = $_GET['mode'];
		}
		if (isset($_POST['mode'])){
			$mode = $_POST['mode'];
		}

		$allowed = array('player', 'pie', 'chart', 'allies_best', 'allies_worst', 'against_best', 'against_worst', 'qg');
		if (in_array($mode, $allowed)) {

			$run = false;
			if (ArghSession::is_gold()) {
				$run = true;
			} else if (ArghSession::get_username() == $player) {
				if ($mode == 'pie' || $mode == 'player') $run = true;
			} else {
				if ($mode == 'pie') $run = true;
			}
			if ($mode == 'qg' && ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN, RightsMode::VIP_HEADADMIN, RightsMode::VIP_ADMIN))) $run = true;

			if ($run) {

				$day = 0;
				if (isset($_POST['day'])) {
					$day = (int)$_POST['day'];
					if ($day < 1 or $day > 31) {
						$day = 1;
					}
				}

				$month = 0;
				if (isset($_POST['month'])) {
					$month = (int)$_POST['month'];
					if ($month < 1 or $month > 12) {
						$month = 7;
					}
				}

				$year = 0;
				if (isset($_POST['year'])) {
					$year = (int)$_POST['year'];
					if ($year < 2009) {
						$year = 2009;
					}
				}

				$with = '';
				if (isset($_POST['pwith'])) {
					$with = substr($_POST['pwith'], 0, 25);
				}

				$pick = -1;
				if (isset($_POST['pick'])) {
					$pick = (int)$_POST['pick'];
					if ($pick < 0 or $pick > 5) {
						$pick = 0;
					}
				}

				if ($mode == 'qg') {

						echo '<br /><div id="stats">'.LadderStatisticsModule::render_stats_table(
							LadderStatisticsModule::get_player_qg($player, 0, 50), 
							$player,  
							create_function('$r', 'return "<a href=\'?f=ladder_game&id=".$r->game_id."\'>".date("d/m/Y H:i", $r->opened).", #".$r->game_id."</a>";'),
							false
						).'</div>';
					
				} else if ($mode == 'player') {

					if ($year == 0 && $month == 0 && $day == 0) {

						echo '<br /><div id="stats_months">'.LadderStatisticsModule::render_stats_table(
							LadderStatisticsModule::get_player_months($player), 
							Lang::LADDER_LISTING, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\'player\' p=\'".$r->username."\' y=\'".$r->year."\' m=\'".$r->month."\'>".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year."</a>";')
						).'</div>';
						echo '<br /><br /><div id="stats_days"></div>';
						echo '<br /><br /><div id="stats_games"></div>';

					} else if ($year != 0 && $month != 0 && $day == 0) {

						echo LadderStatisticsModule::render_stats_table(
							LadderStatisticsModule::get_player_days($player, $year, $month), 
							Lang::$MONTHS_ARRAY[$month - 1]." ".$year, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\'player\' p=\'".$r->username."\' y=\'".$r->year."\' m=\'".$r->month."\' d=\'".$r->day."\'>".$r->day." ".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $r->month, $r->day, $r->year))]."</a>";')
						);

					} else if ($year != 0 && $month != 0 && $day != 0) {

						echo LadderStatisticsModule::render_games_table(
							LadderStatisticsModule::get_player_games($player, $year, $month, $day), 
							$day." ".Lang::$MONTHS_ARRAY[$month - 1]." ".$year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $month, $day, $year))], 
							create_function('$r', 'return "<a href=\'?f=ladder_game&id=".$r->game_id."\'>".date("H:i", $r->opened).", #".$r->game_id."</a>";')
						);

					}

				} else if ($mode == 'allies_best' || $mode == 'allies_worst') {
				
					$caption = sprintf(Lang::LADDER_STATS_ALLIES_BEST_TITLE, htmlentities($player));
					$order = 'SUM(A.xp) DESC';
					if ($mode == 'allies_worst') {
						$order = 'SUM(A.xp) ASC';
						$caption = sprintf(Lang::LADDER_STATS_ALLIES_WORST_TITLE, htmlentities($player));
					}

					if ($with == '' && $year == 0 && $month == 0 && $day == 0) {

						echo '<br /><div id="stats">'.LadderStatisticsModule::render_stats_table(
							LadderStatisticsModule::get_allies($player, $order, 0, 25), 
							Lang::PLAYER, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\'>".$r->with."</a>";'),
							false,
							$caption
						).'</div>';
						echo '<br /><br /><div id="stats_months"></div>';
						echo '<br /><br /><div id="stats_days"></div>';
						echo '<br /><br /><div id="stats_games"></div>';

					} else if ($with != '' && $year == 0 && $month == 0 && $day == 0) {

						echo LadderStatisticsModule::render_stats_table(
							LadderStatisticsModule::get_allies_months($player, $with), 
							$with, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\' y=\'".$r->year."\' m=\'".$r->month."\'>".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year."</a>";')
						);

					} else if ($with != '' && $year != 0 && $month != 0 && $day == 0) {

						echo LadderStatisticsModule::render_stats_table(
							LadderStatisticsModule::get_allies_days($player, $with, $year, $month), 
							Lang::$MONTHS_ARRAY[$month - 1]." ".$year, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\' y=\'".$r->year."\' m=\'".$r->month."\' d=\'".$r->day."\'>".$r->day." ".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $r->month, $r->day, $r->year))]."</a>";')
						);

					} else if ($with != '' && $year != 0 && $month != 0 && $day != 0) {

						echo LadderStatisticsModule::render_games_table(
							LadderStatisticsModule::get_allies_games($player, $with, $year, $month, $day), 
							$day." ".Lang::$MONTHS_ARRAY[$month - 1]." ".$year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $month, $day, $year))], 
							create_function('$r', 'return "<a href=\'?f=ladder_game&id=".$r->game_id."\'>".date("H:i", $r->opened).", #".$r->game_id."</a>";')
						);

					}

				} else if ($mode == 'against_best' || $mode == 'against_worst') {
				
					$caption = sprintf(Lang::LADDER_STATS_AGAINST_WORST_TITLE, htmlentities($player));
					$order = 'SUM(A.xp) ASC';
					if ($mode == 'against_best') {
						$order = 'SUM(A.xp) DESC';
						$caption = sprintf(Lang::LADDER_STATS_AGAINST_BEST_TITLE, htmlentities($player));
					}

					if ($with == '' && $year == 0 && $month == 0 && $day == 0) {

						echo '<br /><div id="stats">'.LadderStatisticsModule::render_stats_table(
							LadderStatisticsModule::get_againsts($player, $order, 0, 25), 
							Lang::PLAYER, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\'>".$r->with."</a>";'),
							false,
							$caption
						).'</div>';
						echo '<br /><br /><div id="stats_months"></div>';
						echo '<br /><br /><div id="stats_days"></div>';
						echo '<br /><br /><div id="stats_games"></div>';

					} else if ($with != '' && $year == 0 && $month == 0 && $day == 0) {

						echo LadderStatisticsModule::render_stats_table(
							LadderStatisticsModule::get_againsts_months($player, $with), 
							$with, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\' y=\'".$r->year."\' m=\'".$r->month."\'>".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year."</a>";')
						);

					} else if ($with != '' && $year != 0 && $month != 0 && $day == 0) {

						echo LadderStatisticsModule::render_stats_table(
							LadderStatisticsModule::get_againsts_days($player, $with, $year, $month), 
							Lang::$MONTHS_ARRAY[$month - 1]." ".$year, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\' y=\'".$r->year."\' m=\'".$r->month."\' d=\'".$r->day."\'>".$r->day." ".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $r->month, $r->day, $r->year))]."</a>";')
						);

					} else if ($with != '' && $year != 0 && $month != 0 && $day != 0) {

						echo LadderStatisticsModule::render_games_table(
							LadderStatisticsModule::get_againsts_games($player, $with, $year, $month, $day), 
							$day." ".Lang::$MONTHS_ARRAY[$month - 1]." ".$year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $month, $day, $year))], 
							create_function('$r', 'return "<a href=\'?f=ladder_game&id=".$r->game_id."\'>".date("H:i", $r->opened).", #".$r->game_id."</a>";')
						);

					}

				} else if ($mode == 'pie') {

					$req = "
						SELECT 
							rank, 
							player, 
							played, 
							closed, 
							win, 
							lose, 
							away, 
							`left`, 
							xp
						FROM lg_ladder_stats_ranks
						WHERE player = '".mysql_real_escape_string($player)."'";
					$res = mysql_query($req) or die(mysql_error());
					if (mysql_num_rows($res) != 0) {
						$obj = mysql_fetch_object($res);
						$tot = $obj->win + $obj->lose + $obj->away + $obj->left;
						if ($tot > 0) {
							$gp = new GooglePie();
							$gp->set_size(375, 150);
							$gp->add_slice(new PieSlice(Lang::PIE_WINS, round(100 * $obj->win / $tot, 2), '66ff66'));
							$gp->add_slice(new PieSlice(Lang::PIE_LOSSES, round(100 * $obj->lose / $tot, 2), 'ff0000'));
							$gp->add_slice(new PieSlice(Lang::PIE_LEFTS, round(100 * $obj->left / $tot, 2), 'ffff33'));
							$gp->add_slice(new PieSlice(Lang::PIE_AWAYS, round(100 * $obj->away / $tot, 2), '999999'));
							echo '<br />';
							echo '<table class="listing">';
							echo '<colgroup><col width="200" /><col /></colgroup>';
							echo '<thead><tr><th colspan="2">'.Lang::LADDER_STATS.'</th></tr></thead>';
							echo '<tr><td style="text-align: left;" valign="top"><br /><img src="img/xp.gif" alt=""/> '.Lang::XP.':</td>';
							echo '<td>';
							echo '<br /><b>'.XPColorize($obj->xp).' ('.$obj->rank.'<sup>'.($obj->rank == 1 ? 'er' : '&egrave;me').'</sup>)</b>';
							echo '<br /><b><span class="win">'.$obj->win.'</span></b>&nbsp;'.strtolower(Lang::WINS).'&nbsp;<span class="info">('.round(100 * $obj->win / $tot, 2).'%)</span>';
							echo '<br /><b><span class="lose">'.$obj->lose.'</span></b>&nbsp;'.strtolower(Lang::LOSSES).'&nbsp;<span class="info">('.round(100 * $obj->lose / $tot, 2).'%)</span>';
							echo '<br /><b><span class="info">'.$obj->away.'</span></b>&nbsp;'.strtolower(Lang::TIMES_NOT_SHOW_UP).'&nbsp;<span class="info">('.round(100 * $obj->away / $tot, 2).'%)</span>';
							echo '<br /><b><span class="draw">'.$obj->left.'</span></b>&nbsp;'.strtolower(Lang::LEFTS).'&nbsp;<span class="info">('.round(100 * $obj->left / $tot, 2).'%)</span>';
							echo '</td></tr>';
							echo '<tr><td colspan="2" style="text-align: center;">';
							$gp->render();
							echo '</td></tr>';
							echo '</table>';
						}
					}

				} else if ($mode == 'chart') {

					$result = array();
					$req = "
						SELECT 
							`year`, 
							`month`, 
							`day`, 
							SUM(played) AS 'played',  
							SUM(closed) AS 'closed', 
							SUM(win) AS 'win', 
							SUM(lose) AS 'lose', 
							SUM(`left`) AS 'left', 
							SUM(away) AS 'away', 
							SUM(xp) AS 'xp'
						FROM lg_ladder_stats
						WHERE username = '".mysql_real_escape_string($player)."'
						GROUP BY `year`, `month`, `day`
						ORDER BY `year` ASC, `month` ASC, `day` ASC";
					$res = mysql_query($req) or die(mysql_error());
					if (mysql_num_rows($res) != 0) {
						while ($obj = mysql_fetch_object($res)) {
							$result[$obj->year.str_pad($obj->month, 2, '0', STR_PAD_LEFT).str_pad($obj->day, 2, '0', STR_PAD_LEFT)] = $obj->xp;
						}
					}
				
					$start = mktime(0, 0, 0, 7, 12, 2010);
					$today = days_add(time(), 1);
				
					$datas = array();
				
					$xp = 1600;
					$max_xp = 1600;
					$min_xp = 1600;
					
					while (date('Ymd', $start) != date('Ymd', $today)) {
						if (isset($result[date('Ymd', $start)])) $xp += $result[date('Ymd', $start)];
						if ($xp > $max_xp) $max_xp = $xp;
						if ($xp < $min_xp) $min_xp = $xp;
						$datas[date('Ymd', $start)] = $xp;
						$start = days_add($start, 1);
					}
				
					$graph_min = $min_xp - 100;
					$graph_max = $max_xp + 100;
					$graph_diff = $graph_max - $graph_min;
				
					$encoding = '';
					foreach ($datas as $value) {
						$encoding .= extended_encoding(($value - $graph_min) * 4095 / $graph_diff);
					}
				
					$start = mktime(0, 0, 0, 7, 12, 2010);
					
					$url = 'http://chart.apis.google.com/chart';
					$url .= '?cht=lc';
					$url .= '&chs=550x200';
					$url .= '&chf=bg,s,000000';
					$url .= '&chxt=x,y,r';
					$url .= '&chxl=0:|'.date(Lang::DATE_FORMAT_DAY, $start).'|'.date(Lang::DATE_FORMAT_DAY).'|1:|1600|2:|'.$min_xp.'|'.$max_xp;
					$url .= '&chxp=1,'.(((1600 - $graph_min) * 4095 / $graph_diff) * 100 / 4095).'|2,'.((($min_xp - $graph_min) * 4095 / $graph_diff) * 100 / 4095).','.((($max_xp - $graph_min) * 4095 / $graph_diff) * 100 / 4095);
					$url .= '&chxs=0,FFFFFF,11,0|1,FFFFFF,11,1,lt,FFFFFF|2,AA0000,11,-1,lt,AA0000';
					$url .= '&chxtc=1,-500|2,-500';
					$url .= '&chd=e:'.$encoding;
					
					echo '<br />';
					echo '<table class="listing">';
					echo '<thead><tr><th>'.Lang::LADDER_STATS_GRAPH_XP_EVOLUTION_TITLE.'</th></tr></thead>';
					echo '<tr><td style="text-align: center;"><br /><img src="'.$url.'" alt="" /></td></tr>';
					echo '</table>';

				}
			}

		}

	}

?>