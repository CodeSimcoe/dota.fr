<link rel="stylesheet" href="themes/default/parser.css" type="text/css">
<?php

	require_once '/home/www/ligue/classes/ReplayClasses.php';
	
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

	if (empty($_POST['division'])) {
		$division = 'all';
	} else {
		$division = substr($_POST['division'], 0, 3);
	}
	
	ArghPanel::begin_tag(Lang::DIVISION_CHOICE);
?>
<form method="post" action="?f=league_stats">
	<select name="division" style="width: 200px; margin-right: 20px;">
	<?php
		$divisions = CacheManager::get_division_cache();
		
		echo '<option'.attr_($division, 'all').' value="all">'.Lang::ALL_DIVISIONS.'</option>';
		
		foreach ($divisions as $div) {
			echo '<option'.attr_($division, $div).' value="'.$div.'">'.Lang::DIVISION.' '.$div.'</option>';
		}

	?>
	</select>
	<input type="submit" value="<?php echo Lang::VALIDATE; ?>" style="padding: 0px 20px;" />
	</form>
<?php
	ArghPanel::end_tag();

	$picks = array();
	$bans = array();

	$req = "SELECT version FROM parser_versions WHERE is_league_version = 1";
	$res = mysql_query($req) or die(mysql_error());
	$row = mysql_fetch_row($res);
	$league_version = $row[0];
	$league_definition = new ReplayDefinition($league_version);

	$req = "SELECT * FROM lg_matchs WHERE etat != 1";
	if ($division != 'all') $req.=" AND divi = '".$division."'";
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
				eval('if ($match->ban'.$i.' != "") $hero = replay_definition_heroes($match_definition, $match->ban'.$i.');');
				if ($hero != null) {
					if (isset($bans[$hero['hero']])) {
						$bans[$hero['hero']]['count'] += 1;
					} else {
						$bans[$hero['hero']] = array( 'hero' => $hero['hero'], 'img' => $hero['img'], 'count' => 1 );
					}
				}
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
				eval('if ($match->ban'.$i.'r2 != "") $hero = replay_definition_heroes($match_definition, $match->ban'.$i.'r2);');
				if ($hero != null) {
					if (isset($bans[$hero['hero']])) {
						$bans[$hero['hero']]['count'] += 1;
					} else {
						$bans[$hero['hero']] = array( 'hero' => $hero['hero'], 'img' => $hero['img'], 'count' => 1 );
					}
				}
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

	ArghPanel::begin_tag(Lang::PICK_STATISTICS);
?>
<table class="listing parser center">
	<colgroup>
		<col width="30" />
		<col width="40" />
		<col />
		<col width="260" />
		<col width="100" />
	</colgroup>
	<thead>
		<tr>
			<th>#</th>
			<th>&nbsp;</th>
			<th><?php echo Lang::HERO; ?></th>
			<th><?php echo Lang::NB_PICKS; ?></th>
			<th><?php echo Lang::WIN; ?></th>
		</tr>
	</thead>
	<tbody>
<?php

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

?>
</tbody>
</table>

<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::BAN_STATISTICS);
?>
<table class="listing parser center">
	<colgroup>
		<col width="30" />
		<col width="40" />
		<col />
		<col width="300" />
	</colgroup>
	<thead>
		<tr>
			<th>#</th>
			<th>&nbsp;</th>
			<th><?php echo Lang::HERO; ?></th>
			<th><?php echo Lang::NB_BANS; ?></th>
		</tr>
	</thead>
	<tbody>
<?php

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
	
?>
	</tbody>
</table>
<?php
	ArghPanel::end_tag();
?>