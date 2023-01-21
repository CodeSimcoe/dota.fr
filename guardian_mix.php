
<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::GUARDIAN_ADMIN
		)
	);

	$mix = '';
	if (isset($_GET['mix'])) {
		$mix = mysql_real_escape_string(substr($_GET['mix'], 0, 25));
	}
	
	$with = '';
	if (isset($_GET['with'])) {
		$with = mysql_real_escape_string(substr($_GET['with'], 0, 25));
	}
		
?>
<link type="text/css" rel="stylesheet" href="guardian.css" />
<?php ArghPanel::begin_tag("Ladder Guardian - Mix"); ?>
<div class="lg-content">
	<table border="0" cellpadding="2" cellspacing="0" style="width: 100%; table-layout: fixed;">
		<colgroup><col width="25" /><col width="15" /><col /></colgroup>
		<tr>
			<td colspan="3"><strong>Légende</strong></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="gl_none">&nbsp;</td>
			<td>&nbsp;&nbsp;Joué sur le ladder</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="gl_ban">&nbsp;</td>
			<td>&nbsp;&nbsp;Ban en cours</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="gl_life">&nbsp;</td>
			<td>&nbsp;&nbsp;Banlife</td>
		</tr>
	</table>
	<br />
<?php

	if ($mix != '' && $with != '') {
	
		$start = mktime(0, 0, 0, 7, 1, 2010);
		$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$dates = Array();
		while ($start != $today) {
			$mkey = date("Ym", $start);
			$dkey = date("Ymd", $start);
			if (!isset($dates[$mkey])) { $dates[$mkey] = Array(); $dates[$mkey]['month'] = date("F Y", $start); }
			if (!isset($dates[$mkey][$dkey])) $dates[$mkey][$dkey] = Array();
			$dates[$mkey][$dkey]['date'] = $start;
			$dates[$mkey][$dkey][$mix] = '';
			$dates[$mkey][$dkey][$with] = '';
			$astart = getdate($start);
			$start = mktime(0, 0, 0, $astart["mon"], $astart["mday"] + 1, $astart["year"]);
		}
		$mkey = date("Ym", $start);
		$dkey = date("Ymd", $start);
			if (!isset($dates[$mkey])) { $dates[$mkey] = Array(); $dates[$mkey]['month'] = date("F Y", $start); }
		if (!isset($dates[$mkey][$dkey])) $dates[$mkey][$dkey] = Array();
		$dates[$mkey][$dkey]['date'] = $start;
		$dates[$mkey][$dkey][$mix] = '';
		$dates[$mkey][$dkey][$with] = '';
		
		$req = "
			SELECT DISTINCT year, month, day FROM lg_ladder_stats_players WHERE player = '".$mix."'";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			while ($obj = mysql_fetch_object($res)) {
				$when = mktime(0, 0, 0, $obj->month, $obj->day, $obj->year);
				$mkey = date("Ym", $when);
				$dkey = date("Ymd", $when);
				if (isset($dates[$mkey])) {
					if (isset($dates[$mkey][$dkey])) {
						$dates[$mkey][$dkey][$mix] = 'none';
					}
				}
			}
		}
		
		$req = "
			SELECT DISTINCT year, month, day FROM lg_ladder_stats_players WHERE player = '".$with."'";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			while ($obj = mysql_fetch_object($res)) {
				$when = mktime(0, 0, 0, $obj->month, $obj->day, $obj->year);
				$mkey = date("Ym", $when);
				$dkey = date("Ymd", $when);
				if (isset($dates[$mkey])) {
					if (isset($dates[$mkey][$dkey])) {
						$dates[$mkey][$dkey][$with] = 'none';
					}
				}
			}
		}
		
		$req = "
			SELECT quand, `force` FROM lg_ladderbans_follow WHERE `type` = 'ban' AND username = '".$mix."' AND `force` > 0 AND afficher = 1";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			while ($obj = mysql_fetch_object($res)) {
				$when = $obj->quand;
				for ($i = 0; $i < $obj->force; $i++) {
					$mkey = date("Ym", $when);
					$dkey = date("Ymd", $when);
					if (isset($dates[$mkey])) {
						if (isset($dates[$mkey][$dkey])) {
							$dates[$mkey][$dkey][$mix] = 'ban';
						}
					}
					$awhen = getdate($when);
					$when = mktime(0, 0, 0, $awhen["mon"], $awhen["mday"] + 1, $awhen["year"]);
				}
			}
		}

		$req = "
			SELECT quand, `force` FROM lg_ladderbans_follow WHERE `type` = 'ban' AND username = '".$with."' AND `force` > 0 AND afficher = 1";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			while ($obj = mysql_fetch_object($res)) {
				$when = $obj->quand;
				for ($i = 0; $i < $obj->force; $i++) {
					$mkey = date("Ym", $when);
					$dkey = date("Ymd", $when);
					if (isset($dates[$mkey])) {
						if (isset($dates[$mkey][$dkey])) {
							$dates[$mkey][$dkey][$with] = 'ban';
						}
					}
					$awhen = getdate($when);
					$when = mktime(0, 0, 0, $awhen["mon"], $awhen["mday"] + 1, $awhen["year"]);
				}
			}
		}
		
		$req = "
			SELECT quand FROM lg_ladderbans WHERE qui = '".$mix."' AND duree = 0";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			while ($obj = mysql_fetch_object($res)) {
				$when = $obj->quand;
				while ($when != $today) {
					$mkey = date("Ym", $when);
					$dkey = date("Ymd", $when);
					if (isset($dates[$mkey])) {
						if (isset($dates[$mkey][$dkey])) {
							$dates[$mkey][$dkey][$mix] = 'life';
						}
					}
					$awhen = getdate($when);
					$when = mktime(0, 0, 0, $awhen["mon"], $awhen["mday"] + 1, $awhen["year"]);
				}
				$mkey = date("Ym", $when);
				$dkey = date("Ymd", $when);
				if (isset($dates[$mkey])) {
					if (isset($dates[$mkey][$dkey])) {
						$dates[$mkey][$dkey][$mix] = 'life';
					}
				}
			}
		}
		
		$req = "
			SELECT quand FROM lg_ladderbans WHERE qui = '".$with."' AND duree = 0";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			while ($obj = mysql_fetch_object($res)) {
				$when = $obj->quand;
				while ($when != $today) {
					$mkey = date("Ym", $when);
					$dkey = date("Ymd", $when);
					if (isset($dates[$mkey])) {
						if (isset($dates[$mkey][$dkey])) {
							$dates[$mkey][$dkey][$with] = 'life';
						}
					}
					$awhen = getdate($when);
					$when = mktime(0, 0, 0, $awhen["mon"], $awhen["mday"] + 1, $awhen["year"]);
				}
				$mkey = date("Ym", $when);
				$dkey = date("Ymd", $when);
				if (isset($dates[$mkey])) {
					if (isset($dates[$mkey][$dkey])) {
						$dates[$mkey][$dkey][$with] = 'life';
					}
				}
			}
		}

		krsort($dates);
		
		echo '<strong>Première ligne:</strong>&nbsp;&nbsp;<a href="http://www.dota.fr/ligue/?f=guardian_players&player='.$mix.'">'.$mix.'</a><br /><br />';
		echo '<strong>Deuxième ligne:</strong>&nbsp;&nbsp;<a href="http://www.dota.fr/ligue/?f=guardian_players&player='.$with.'">'.$with.'</a><br /><br />';
		echo '<hr /><br />';
		
		foreach ($dates as $mkey => $mvalue) {
			ksort($mvalue);
			echo '<strong>'.$mvalue['month'].'</strong><br /><br />';
			echo '<table border="1" cellpadding="0" cellspacing="0" style="table-layout: fixed; border-collapse: collapse;">';
			$head = ''; $rmix = ''; $rwith = '';
			foreach ($mvalue as $dkey => $dvalue) {
				if ($dkey != 'month') {
					$head .= '<td style="width: 16px; height: 16px; overflow: hidden; text-align: center; font-size: 6pt;">'.date("j", $dvalue['date']).'</td>';
					if ($dvalue[$mix] == '') {
						$rmix .= '<td style="width: 16px; height: 16px; overflow: hidden; font-size: 0pt;">&nbsp;</td>';
					} else {
						$rmix .= '<td class="gl_'.$dvalue[$mix].'" style="width: 16px; height: 16px;">&nbsp;</td>';
					}
					if ($dvalue[$with] == '') {
						$rwith .= '<td style="width: 16px; height: 16px; overflow: hidden; font-size: 0pt;">&nbsp;</td>';
					} else {
						$rwith .= '<td class="gl_'.$dvalue[$with].'" style="width: 16px; height: 16px;">&nbsp;</td>';
					}
				}
			}
			echo '<tr>'.$head.'</tr>';
			echo '<tr>'.$rmix.'</tr>';
			echo '<tr>'.$rwith.'</tr>';
			echo '</table>';
			echo '<br /><hr /><br />';
		}
		
		
		echo '<strong>Games communes</strong><br /><br />';
		$req = "
			SELECT T2.opened, T2.id
			FROM lg_ladder_stats_games AS T1
			INNER JOIN lg_laddergames AS T2 ON T1.game_id = T2.id
			WHERE T1.new = 0
			AND (T2.p1 = '".$mix."' OR T2.p2 = '".$mix."' OR T2.p3 = '".$mix."' OR T2.p4 = '".$mix."' OR T2.p5 = '".$mix."' OR T2.p6 = '".$mix."' OR T2.p7 = '".$mix."' OR T2.p8 = '".$mix."' OR T2.p9 = '".$mix."' OR T2.p10 = '".$mix."')
			AND (T2.p1 = '".$with."' OR T2.p2 = '".$with."' OR T2.p3 = '".$with."' OR T2.p4 = '".$with."' OR T2.p5 = '".$with."' OR T2.p6 = '".$with."' OR T2.p7 = '".$with."' OR T2.p8 = '".$with."' OR T2.p9 = '".$with."' OR T2.p10 = '".$with."')
			ORDER BY T2.opened DESC";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%; table-layout: fixed; border-collapse: collapse;">';
			echo '<colgroup><col width="150" /><col /></colgroup>';
			$count = 0;
			while ($obj = mysql_fetch_object($res)) {
				$css = ($count++ % 2 == 0) ? " class='alternate'" : "";
				echo '<tr>';
				echo '<td'.$css.'>'.date("d/m/Y H:i:s", $obj->opened).'</td>';
				echo '<td'.$css.'><a href="?f=ladder_game&id='.$obj->id.'">Game #'.$obj->id.'</a></td>';
				echo '</tr>';
			}
			echo '</table>';
		} else {
			echo 'Aucune partie en commun';
		}
		
	}
?>
</div>
<?php ArghPanel::end_tag(ArghPanelMode::NORMAL); ?>