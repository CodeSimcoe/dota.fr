<?php

	require_once '/home/www/ligue/mysql_connect.php';
	require_once '/home/www/ligue/classes/LadderStatisticsModule.php';
	require_once '/home/www/ligue/classes/Alternator.php';
	require_once '/home/www/ligue/classes/RightsMode.php';
	require_once '/home/www/ligue/classes/GooglePie.php';
	require_once '/home/www/ligue/classes/Color.php';
	require_once '/home/www/ligue/classes/ArghSession.php';
	require_once '/home/www/ligue/misc.php';
	require_once '/home/www/ligue/laddervip_functions.php';

	ArghSession::begin();
	
	require_once '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';

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

		$allowed = array('player', 'pie', 'pick', 'allies_best', 'allies_worst', 'against_best', 'against_worst', 'piepick');
		if (in_array($mode, $allowed)) {

			$run = false;
			if (ArghSession::is_gold()) {
				$run = true;
			} else if (ArghSession::get_username() == $player) {
				if ($mode == 'pie' || $mode == 'player') $run = true;
			} else {
				if ($mode == 'pie') $run = true;
			}

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

				if ($mode == 'player') {

					if ($year == 0 && $month == 0 && $day == 0) {

						echo '<br /><div id="stats_months">'.LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_player_months($player), 
							Lang::LADDER_VIP_LISTING, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\'player\' p=\'".$r->username."\' y=\'".$r->year."\' m=\'".$r->month."\'>".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year."</a>";')
						).'</div>';
						echo '<br /><br /><div id="stats_days"></div>';
						echo '<br /><br /><div id="stats_games"></div>';

					} else if ($year != 0 && $month != 0 && $day == 0) {

						echo LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_player_days($player, $year, $month), 
							Lang::$MONTHS_ARRAY[$month - 1]." ".$year, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\'player\' p=\'".$r->username."\' y=\'".$r->year."\' m=\'".$r->month."\' d=\'".$r->day."\'>".$r->day." ".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $r->month, $r->day, $r->year))]."</a>";')
						);

					} else if ($year != 0 && $month != 0 && $day != 0) {

						echo LadderStatisticsModule::render_vip_games_table(
							LadderStatisticsModule::get_vip_player_games($player, $year, $month, $day), 
							$day." ".Lang::$MONTHS_ARRAY[$month - 1]." ".$year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $month, $day, $year))], 
							create_function('$r', 'return "<a href=\'?f=laddervip_game&id=".$r->game_id."\'>".date("H:i", $r->opened).", #".$r->game_id."</a>";')
						);

					}

				} else if ($mode == 'pick') {

					if ($pick == -1 && $year == 0 && $month == 0 && $day == 0) {

						echo '<br /><div id="stats_pick">'.LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_picks($player), 
							Lang::LADDER_VIP_LISTING_PICKS, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\'pick\' p=\'".$r->username."\' o=\'".$r->pick."\'>".Lang::$LADDER_VIP_PICKS_ARRAY[$r->pick]."</a>";')
						).'</div>';
						echo '<br /><br /><div id="stats_months"></div>';
						echo '<br /><br /><div id="stats_days"></div>';
						echo '<br /><br /><div id="stats_games"></div>';

					} else if ($pick != -1 && $year == 0 && $month == 0 && $day == 0) {

						echo LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_picks_months($player, $pick), 
							Lang::$LADDER_VIP_PICKS_ARRAY[$pick], 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\'pick\' p=\'".$r->username."\' o=\'".$r->pick."\' y=\'".$r->year."\' m=\'".$r->month."\'>".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year."</a>";')
						);

					} else if ($pick != -1 && $year != 0 && $month != 0 && $day == 0) {

						echo LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_picks_days($player, $pick, $year, $month), 
							Lang::$MONTHS_ARRAY[$month - 1]." ".$year, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\'pick\' p=\'".$r->username."\' o=\'".$r->pick."\' y=\'".$r->year."\' m=\'".$r->month."\' d=\'".$r->day."\'>".$r->day." ".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $r->month, $r->day, $r->year))]."</a>";')
						);

					} else if ($pick != -1 && $year != 0 && $month != 0 && $day != 0) {

						echo LadderStatisticsModule::render_vip_games_table(
							LadderStatisticsModule::get_vip_picks_games($player, $pick, $year, $month, $day), 
							$day." ".Lang::$MONTHS_ARRAY[$month - 1]." ".$year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $month, $day, $year))], 
							create_function('$r', 'return "<a href=\'?f=laddervip_game&id=".$r->game_id."\'>".date("H:i", $r->opened).", #".$r->game_id."</a>";')
						);

					}

				} else if ($mode == 'allies_best' || $mode == 'allies_worst') {
				
					$caption = sprintf(Lang::LADDERVIP_STATS_ALLIES_BEST_TITLE, htmlentities($player));
					$order = 'SUM(A.xp) DESC';
					if ($mode == 'allies_worst') {
						$order = 'SUM(A.xp) ASC';
						$caption = sprintf(Lang::LADDERVIP_STATS_ALLIES_WORST_TITLE, htmlentities($player));
					}

					if ($with == '' && $year == 0 && $month == 0 && $day == 0) {

						echo '<br /><div id="stats">'.LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_allies($player, $order, 0, 25), 
							Lang::PLAYER, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\'>".$r->with."</a>";'),
							false,
							$caption
						).'</div>';
						echo '<br /><br /><div id="stats_months"></div>';
						echo '<br /><br /><div id="stats_days"></div>';
						echo '<br /><br /><div id="stats_games"></div>';

					} else if ($with != '' && $year == 0 && $month == 0 && $day == 0) {

						echo LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_allies_months($player, $with), 
							$with, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\' y=\'".$r->year."\' m=\'".$r->month."\'>".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year."</a>";')
						);

					} else if ($with != '' && $year != 0 && $month != 0 && $day == 0) {

						echo LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_allies_days($player, $with, $year, $month), 
							Lang::$MONTHS_ARRAY[$month - 1]." ".$year, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\' y=\'".$r->year."\' m=\'".$r->month."\' d=\'".$r->day."\'>".$r->day." ".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $r->month, $r->day, $r->year))]."</a>";')
						);

					} else if ($with != '' && $year != 0 && $month != 0 && $day != 0) {

						echo LadderStatisticsModule::render_vip_games_table(
							LadderStatisticsModule::get_vip_allies_games($player, $with, $year, $month, $day), 
							$day." ".Lang::$MONTHS_ARRAY[$month - 1]." ".$year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $month, $day, $year))], 
							create_function('$r', 'return "<a href=\'?f=laddervip_game&id=".$r->game_id."\'>".date("H:i", $r->opened).", #".$r->game_id."</a>";')
						);

					}

				} else if ($mode == 'against_best' || $mode == 'against_worst') {
				
				
					$caption = sprintf(Lang::LADDERVIP_STATS_AGAINST_WORST_TITLE, htmlentities($player));
					$order = 'SUM(A.xp) ASC';
					if ($mode == 'against_best') {
						$order = 'SUM(A.xp) DESC';
						$caption = sprintf(Lang::LADDERVIP_STATS_AGAINST_BEST_TITLE, htmlentities($player));
					}

					if ($with == '' && $year == 0 && $month == 0 && $day == 0) {

						echo '<br /><div id="stats">'.LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_againsts($player, $order, 0, 25), 
							Lang::PLAYER, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\'>".$r->with."</a>";'),
							false,
							$caption
						).'</div>';
						echo '<br /><br /><div id="stats_months"></div>';
						echo '<br /><br /><div id="stats_days"></div>';
						echo '<br /><br /><div id="stats_games"></div>';

					} else if ($with != '' && $year == 0 && $month == 0 && $day == 0) {

						echo LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_againsts_months($player, $with), 
							$with, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\' y=\'".$r->year."\' m=\'".$r->month."\'>".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year."</a>";')
						);

					} else if ($with != '' && $year != 0 && $month != 0 && $day == 0) {

						echo LadderStatisticsModule::render_vip_stats_table(
							LadderStatisticsModule::get_vip_againsts_days($player, $with, $year, $month), 
							Lang::$MONTHS_ARRAY[$month - 1]." ".$year, 
							create_function('$r', 'return "<a href=\'javascript:void(0);\' t=\''.$mode.'\' p=\'".$r->username."\' w=\'".$r->with."\' y=\'".$r->year."\' m=\'".$r->month."\' d=\'".$r->day."\'>".$r->day." ".Lang::$MONTHS_ARRAY[$r->month - 1]." ".$r->year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $r->month, $r->day, $r->year))]."</a>";')
						);

					} else if ($with != '' && $year != 0 && $month != 0 && $day != 0) {

						echo LadderStatisticsModule::render_vip_games_table(
							LadderStatisticsModule::get_vip_againsts_games($player, $with, $year, $month, $day), 
							$day." ".Lang::$MONTHS_ARRAY[$month - 1]." ".$year.", ".Lang::$DAYS_ARRAY[jddayofweek(cal_to_jd(CAL_GREGORIAN, $month, $day, $year))], 
							create_function('$r', 'return "<a href=\'?f=laddervip_game&id=".$r->game_id."\'>".date("H:i", $r->opened).", #".$r->game_id."</a>";')
						);

					}

				} else if ($mode == 'pie') {

					$req = "
						SELECT 
							username,
							SUM(win) AS 'win', 
							SUM(lose) AS 'lose', 
							SUM(away) AS 'away', 
							SUM(`left`) AS 'left',
							SUM(xp) AS 'xp'
						FROM lg_laddervip_stats
						WHERE username = '".mysql_real_escape_string($player)."'";
					$res = mysql_query($req) or die(mysql_error());
					if (mysql_num_rows($res) != 0) {
						$obj = mysql_fetch_object($res);
						$tot = $obj->win + $obj->lose + $obj->away + $obj->left;
						if ($tot > 0) {
							$gp = new GooglePie();
							$gp->set_size(375, 150);
							$gp->add_slice(new PieSlice(Lang::PIE_WINS, round(100 * $obj->win / $tot, 2), Color::GREEN_WIN));
							$gp->add_slice(new PieSlice(Lang::PIE_LOSSES, round(100 * $obj->lose / $tot, 2), Color::RED));
							$gp->add_slice(new PieSlice(Lang::PIE_LEFTS, round(100 * $obj->left / $tot, 2), Color::YELLOW));
							$gp->add_slice(new PieSlice(Lang::PIE_AWAYS, round(100 * $obj->away / $tot, 2), Color::GRAY_AWAY));
							echo '<br />';
							echo '<table class="listing">';
							echo '<colgroup><col width="200" /><col /></colgroup>';
							echo '<thead><tr><th colspan="2">'.Lang::LADDERVIP_STATS.'</th></tr></thead>';
							echo '<tr><td style="text-align: left;" valign="top"><br /><img src="img/xp.gif" alt=""/> '.Lang::XP.':</td>';
							echo '<td>';
							echo '<br /><b>'.XPColorize(1600 + $obj->xp).'</b>';
							// $rank = getLadderRank($player, true);
							// echo '<br /><b>'.$rank.'<sup>'.($rank == 1 ? 'er' : '&egrave;me').'</sup></b>';
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

				} else if ($mode == 'piepick') {

					/*
					echo '<script type="text/javascript" src="/ligue/ofc/js/swfobject.js"></script>
					<script type="text/javascript">

					swfobject.embedSWF(
					"/ligue/charts/open-flash-chart.swf", "my_chart",
					"300", "300", "9.0.0", "expressInstall.swf",
					{"data-file":"/ligue/charts/pie-chart.php"} );

					</script>
					<div id="my_chart"></div>';
					*/

				
					
					$data = array(0, 0, 0, 0, 0, 0);
					
					$req = "
						SELECT
							pick,
							COUNT(pick) as tot
						FROM
							lg_laddervip_stats
						WHERE
							username = '".mysql_real_escape_string($player)."'
						GROUP BY
							pick";
						
					$res = mysql_query($req) or die(mysql_error());
					if (mysql_num_rows($res) != 0) {
					
						$tot = 0;
						while ($obj = mysql_fetch_object($res)) {
							$data[$obj->pick] = $obj->tot;
							$tot += $obj->tot;
						}
					
						$gp = new GooglePie();
						$gp->set_size(375, 150);
						$gp->_main_color = Color::ORANGE;
						
						foreach ($data as $key => $value) {
							$gp->add_slice(new PieSlice($key, round(100 * $value / $tot, 2)));
						}
						
						echo '<br />';
						echo '<table class="listing">';
						echo '<colgroup><col width="200" /><col /></colgroup>';
						echo '<thead><tr><th colspan="2">'.Lang::LADDERVIP_STATS.'</th></tr></thead>';
						echo '<tr><td style="text-align: left;" valign="top"><br /><img src="img/xp.gif" alt="" /> '.Lang::LADDER_PICKS_PIE_TITLE.'</td>';
						echo '<td>';
						

						foreach ($data as $key => $value) {
							echo '<br /><b>'.Lang::$LADDER_VIP_PICKS_ARRAY[$key].'</b>&nbsp;&nbsp;<span class="info">('.round(100 * $value / $tot, 2).'%)</span>';
						}
						echo '</td></tr>';
						echo '<tr><td colspan="2" style="text-align: center;">';
						$gp->render();
						echo '</td></tr>';
						echo '</table>';
						
					}
				}
			}

		}

	}

?>