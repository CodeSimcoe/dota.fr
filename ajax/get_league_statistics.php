<?php

	function replay_definition_heroes($definition, $name) {
		foreach ($definition->heroes as $key => $value) {
			if ($value['code'] != $value['base_code']) continue;
			if ($value['hero'] == $name) return $value;
		}
		return null;
	}

	function picks_sort($a, $b) {
		if ($a['count'] == $b['count']) {
			if ($a['wins'] == $b['wins']) {
				if ($a['hero'] == $b['hero']) return 0;
				return ($a['hero'] < $b['hero']) ? -1 : 1;
			}
			return ($a['wins'] < $b['wins']) ? 1 : -1;
		}
		return ($a['count'] < $b['count']) ? 1 : -1;
	}

	function bans_sort($a, $b) {
		if ($a['count'] == $b['count']) {
			if ($a['hero'] == $b['hero']) return 0;
			return ($a['hero'] < $b['hero']) ? -1 : 1;
		}
		return ($a['count'] < $b['count']) ? 1 : -1;
	}

	require_once '/home/www/ligue/mysql_connect.php';
	require_once '/home/www/ligue/classes/LeagueStatisticsModule.php';
	require_once '/home/www/ligue/classes/Alternator.php';
	require_once '/home/www/ligue/classes/CacheManager.php';
	require_once '/home/www/ligue/classes/RightsMode.php';
	require_once '/home/www/ligue/classes/ArghSession.php';
	require_once '/home/www/ligue/classes/ReplayClasses.php';
	require_once '/home/www/ligue/misc.php';

	ArghSession::begin();
	
	require_once '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';

	$skip = 0; $take = 50;
	
	$mode = 'kills_asc';
	if (isset($_GET['mode'])) $mode = $_GET['mode'];
	if (isset($_POST['mode'])) $mode = $_POST['mode'];

	$allowed = array(
		'picks', 'bans',
		'kills_asc', 'kills_desc',
		'deaths_asc', 'deaths_desc',
		'assists_asc', 'assists_desc',
		'creeps_asc', 'creeps_desc',
		'denies_asc', 'denies_desc',
		'neutrals_asc', 'neutrals_desc',
		'towers_asc', 'towers_desc',
		'towers_denies_asc', 'towers_denies_desc'
	);
	if (in_array($mode, $allowed)) {

		$divi = 'all';
		if (isset($_GET['divi'])) $divi = $_GET['divi'];
		if (isset($_POST['divi'])) $divi = $_POST['divi'];

		if (in_array($mode, array('kills_asc', 'kills_desc'))) {
			echo LeagueStatisticsModule::render_replays_stats_table(
				LeagueStatisticsModule::get_replays_stats($divi, 'SUM(kills) '.($mode == 'kills_asc' ? 'ASC' : 'DESC'), $skip, $take),
				$divi,
				'kills',
				($mode == 'kills_asc' ? 'ASC' : 'DESC'),
				'Kills',
				$take
			);
		} else if (in_array($mode, array('deaths_asc', 'deaths_desc'))) {
			echo LeagueStatisticsModule::render_replays_stats_table(
				LeagueStatisticsModule::get_replays_stats($divi, 'SUM(deaths) '.($mode == 'deaths_asc' ? 'ASC' : 'DESC'), $skip, $take),
				$divi,
				'deaths',
				($mode == 'deaths_asc' ? 'ASC' : 'DESC'),
				'Deaths',
				$take
			);
		} else if (in_array($mode, array('assists_asc', 'assists_desc'))) {
			echo LeagueStatisticsModule::render_replays_stats_table(
				LeagueStatisticsModule::get_replays_stats($divi, 'SUM(assists) '.($mode == 'assists_asc' ? 'ASC' : 'DESC'), $skip, $take),
				$divi,
				'assists',
				($mode == 'assists_asc' ? 'ASC' : 'DESC'),
				'Assists',
				$take
			);
		} else if (in_array($mode, array('creeps_asc', 'creeps_desc'))) {
			echo LeagueStatisticsModule::render_replays_stats_table(
				LeagueStatisticsModule::get_replays_stats($divi, 'SUM(creeps) '.($mode == 'creeps_asc' ? 'ASC' : 'DESC'), $skip, $take),
				$divi,
				'creeps',
				($mode == 'creeps_asc' ? 'ASC' : 'DESC'),
				'Creeps kills',
				$take
			);
		} else if (in_array($mode, array('denies_asc', 'denies_desc'))) {
			echo LeagueStatisticsModule::render_replays_stats_table(
				LeagueStatisticsModule::get_replays_stats($divi, 'SUM(denies) '.($mode == 'denies_asc' ? 'ASC' : 'DESC'), $skip, $take),
				$divi,
				'denies',
				($mode == 'denies_asc' ? 'ASC' : 'DESC'),
				'Creeps denies',
				$take
			);
		} else if (in_array($mode, array('neutrals_asc', 'neutrals_desc'))) {
			echo LeagueStatisticsModule::render_replays_stats_table(
				LeagueStatisticsModule::get_replays_stats($divi, 'SUM(neutrals) '.($mode == 'neutrals_asc' ? 'ASC' : 'DESC'), $skip, $take),
				$divi,
				'neutrals',
				($mode == 'neutrals_asc' ? 'ASC' : 'DESC'),
				'Neutrals',
				$take
			);
		} else if (in_array($mode, array('towers_asc', 'towers_desc'))) {
			echo LeagueStatisticsModule::render_replays_stats_table(
				LeagueStatisticsModule::get_replays_stats($divi, 'SUM(towers) '.($mode == 'towers_asc' ? 'ASC' : 'DESC'), $skip, $take),
				$divi,
				'towers',
				($mode == 'towers_asc' ? 'ASC' : 'DESC'),
				'Towers kills',
				$take
			);
		} else if (in_array($mode, array('towers_denies_asc', 'towers_denies_desc'))) {
			echo LeagueStatisticsModule::render_replays_stats_table(
				LeagueStatisticsModule::get_replays_stats($divi, 'SUM(towers_denies) '.($mode == 'towers_denies_asc' ? 'ASC' : 'DESC'), $skip, $take),
				$divi,
				'towers_denies',
				($mode == 'towers_denies_asc' ? 'ASC' : 'DESC'),
				'Towers denies',
				$take
			);
		} else if ($mode == 'picks') {
			$picks = array();
			$req = "SELECT version FROM parser_versions WHERE is_league_version = 1";
			$res = mysql_query($req) or die(mysql_error());
			$row = mysql_fetch_row($res);
			$league_version = $row[0];
			$league_definition = new ReplayDefinition($league_version);
			$req = "SELECT * FROM lg_matchs WHERE etat != 1";
			if ($divi != 'all') $req.=" AND divi = '".$divi."'";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) > 0) {
				while ($match = mysql_fetch_object($res)) {
					if ($match->version1 != $league_version) {
						$match_definition = new ReplayDefinition($league_version);
					} else {
						$match_definition = $league_definition;
					}
					for ($i = 1; $i < 11; $i++) {
						$hero = null;
						eval('if ($match->h'.$i.' != "") $hero = replay_definition_heroes($match_definition, $match->h'.$i.');');
						if ($hero != null) {
							if (isset($picks[$hero['hero']])) {
								$picks[$hero['hero']]['count'] += 1;
							} else {
								$picks[$hero['hero']] = array( 'hero' => $hero['hero'], 'img' => $hero['img'], 'count' => 1, 'wins' => 0 );
							}
							switch ($match->etat) {
								case 4:
								case 6:
								case 7:
									$picks[$hero['hero']]['wins'] += ($i < 6) ? 1 : 0;
									break;
								case 5:
								case 9:
								case 11:
									$picks[$hero['hero']]['wins'] += ($i > 5) ? 1 : 0;
									break;
							}
						}
					}
					if ($match->version2 != $league_version) {
						$match_definition = new ReplayDefinition($league_version);
					} else {
						$match_definition = $league_definition;
					}
					for ($i = 1; $i < 11; $i++) {
						$hero = null;
						eval('if ($match->h'.$i.'r2 != "") $hero = replay_definition_heroes($match_definition, $match->h'.$i.'r2);');
						if ($hero != null) {
							if (isset($picks[$hero['hero']])) {
								$picks[$hero['hero']]['count'] += 1;
							} else {
								$picks[$hero['hero']] = array( 'hero' => $hero['hero'], 'img' => $hero['img'], 'count' => 1, 'wins' => 0 );
							}
							switch ($match->etat) {
								case 5:
								case 6:
								case 10:
									$picks[$hero['hero']]['wins'] += ($i < 6) ? 1 : 0;
									break;
								case 4:
								case 8:
								case 11:
									$picks[$hero['hero']]['wins'] += ($i > 5) ? 1 : 0;
									break;
							}
						}
					}
				}
			}
			$divisions = CacheManager::get_division_cache();
			echo '<br />';
			echo '<select style="width: 200px; margin-right: 20px; margin-bottom: 10px;" onchange="$(this).parents(\'div.ui-tabs-panel:eq(0)\').load(\'ajax/get_league_statistics.php?mode=picks&divi=\' + $(this).val());"">';
			echo '<option'.attr_($divi, 'all').' value="all">Toutes les divisions</option>';
			foreach ($divisions as $div) echo '<option'.attr_($divi, $div).' value="'.$div.'">'.Lang::DIVISION.' '.$div.'</option>';
			echo '</select>';
			echo '<table class="listing parser">';
			echo '<colgroup><col width="30" /><col width="40" /><col /><col width="260" /><col width="100" /></colgroup>';
			echo '<thead><tr><th>#</th><th>&nbsp;</th><th>'.Lang::HERO.'</th><th>'.Lang::NB_PICKS.'</th><th>'.Lang::WIN.'</th></tr></thead>';
			echo '<tbody>';
			uasort($picks, 'picks_sort');
			$max_picks = 0;
			foreach ($picks AS $hero => $stats) $max_picks = ($max_picks < $stats['count']) ? $stats['count'] : $max_picks;
			$rank = 0;
			foreach ($picks AS $hero => $stats) {
				$all_wins = ($stats['wins'] == $stats['count']);
				$all_loses = ($stats['wins'] == 0);
				$percent = round(100 * $stats['wins'] / $stats['count'], 2);
				echo '<tr'.Alternator::get_alternation($rank).'>';
				echo '<td>'.$rank.'</td>';
				echo '<td><img src="/ligue/parser/Images/'.$stats['img'].'.png" width="32" alt="" title="'.$stats['hero'].'" /></td>';
				echo '<td>'.$stats['hero'].'</td>';
				echo '<td>';
				if ($all_wins) {
					echo '<img src="/ligue/img/bars/l_w.png" alt="" align="absmiddle" />';
					echo '<img src="/ligue/img/bars/m_w.png" alt="" align="absmiddle" style="height: 10px; width: '.round((($stats['count'] / $max_picks) * 200) + 10).'px;" />';
					echo '<img src="/ligue/img/bars/r_w.png" alt="" align="absmiddle" />';
				} else if ($all_loses) {
					echo '<img src="/ligue/img/bars/l_l.png" alt="" align="absmiddle" />';
					echo '<img src="/ligue/img/bars/m_l.png" alt="" align="absmiddle" style="height: 10px; width: '.round((($stats['count'] / $max_picks) * 200) + 10).'px;" />';
					echo '<img src="/ligue/img/bars/r_l.png" alt="" align="absmiddle" />';
				} else {
					echo '<img src="/ligue/img/bars/l_l.png" alt="" align="absmiddle" />';
					echo '<img src="/ligue/img/bars/m_l.png" alt="" align="absmiddle" style="height: 10px; width: '.round((($stats['count'] - $stats['wins']) / $max_picks) * 200).'px;" />';
					echo '<img src="/ligue/img/bars/m_w_l.png" alt="" align="absmiddle" />';
					echo '<img src="/ligue/img/bars/m_w.png" alt="" align="absmiddle" style="height: 10px; width: '.round(($stats['wins'] / $max_picks) * 200).'px;" />';
					echo '<img src="/ligue/img/bars/r_w.png" alt="" align="absmiddle" />';
				}
				echo '&nbsp;'.$stats['count'];
				echo '</td>';
				echo '<td>'.$stats['wins'].'/'.$stats['count'].' ('.$percent.'%)</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		} else if ($mode == 'bans') {
			$bans = array();
			$req = "SELECT version FROM parser_versions WHERE is_league_version = 1";
			$res = mysql_query($req) or die(mysql_error());
			$row = mysql_fetch_row($res);
			$league_version = $row[0];
			$league_definition = new ReplayDefinition($league_version);
			$req = "SELECT * FROM lg_matchs WHERE etat != 1";
			if ($divi != 'all') $req.=" AND divi = '".$divi."'";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) > 0) {
				while ($match = mysql_fetch_object($res)) {
					if ($match->version1 != $league_version) {
						$match_definition = new ReplayDefinition($league_version);
					} else {
						$match_definition = $league_definition;
					}
					for ($i = 1; $i < 9; $i++) {
						$hero = null;
						eval('if ($match->ban'.$i.' != "") $hero = replay_definition_heroes($match_definition, $match->ban'.$i.');');
						if ($hero != null) {
							if (isset($bans[$hero['hero']])) {
								$bans[$hero['hero']]['count'] += 1;
							} else {
								$bans[$hero['hero']] = array( 'hero' => $hero['hero'], 'img' => $hero['img'], 'count' => 1 );
							}
						}
					}
					if ($match->version2 != $league_version) {
						$match_definition = new ReplayDefinition($league_version);
					} else {
						$match_definition = $league_definition;
					}
					for ($i = 1; $i < 9; $i++) {
						$hero = null;
						eval('if ($match->ban'.$i.'r2 != "") $hero = replay_definition_heroes($match_definition, $match->ban'.$i.'r2);');
						if ($hero != null) {
							if (isset($bans[$hero['hero']])) {
								$bans[$hero['hero']]['count'] += 1;
							} else {
								$bans[$hero['hero']] = array( 'hero' => $hero['hero'], 'img' => $hero['img'], 'count' => 1 );
							}
						}
					}
				}
			}
			$divisions = CacheManager::get_division_cache();
			echo '<br />';
			echo '<select style="width: 200px; margin-right: 20px; margin-bottom: 10px;" onchange="$(this).parents(\'div.ui-tabs-panel:eq(0)\').load(\'ajax/get_league_statistics.php?mode=bans&divi=\' + $(this).val());"">';
			echo '<option'.attr_($divi, 'all').' value="all">Toutes les divisions</option>';
			foreach ($divisions as $div) echo '<option'.attr_($divi, $div).' value="'.$div.'">'.Lang::DIVISION.' '.$div.'</option>';
			echo '</select>';
			echo '<table class="listing parser">';
			echo '<colgroup><col width="30" /><col width="40" /><col /><col width="300" /></colgroup>';
			echo '<thead><tr><th>#</th><th>&nbsp;</th><th>'.Lang::HERO.'</th><th>'.Lang::NB_BANS.'</th></tr></thead>';
			echo '<tbody>';
			uasort($bans, 'bans_sort');
			$max_bans = 0;
			foreach ($bans AS $hero => $stats) $max_bans = ($max_bans < $stats['count']) ? $stats['count'] : $max_bans;
			$rank = 0;
			foreach ($bans AS $hero => $stats) {
				echo '<tr'.Alternator::get_alternation($rank).'>';
				echo '<td>'.$rank.'</td>';
				echo '<td><img src="/ligue/parser/Images/'.$stats['img'].'.png" width="32" alt="" title="'.$stats['hero'].'" /></td>';
				echo '<td>'.$stats['hero'].'</td>';
				echo '<td>';
				echo '<img src="/ligue/img/bars/l_b.png" alt="" align="absmiddle" />';
				echo '<img src="/ligue/img/bars/m_b.png" alt="" align="absmiddle" style="height: 10px; width: '.round(($stats['count'] / $max_bans) * 240).'px;" />';
				echo '<img src="/ligue/img/bars/r_b.png" alt="" align="absmiddle" />';
				echo '&nbsp;'.$stats['count'];
				echo '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		}
	}

?>