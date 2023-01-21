<?php
	ArghPanel::begin_tag(Lang::LEAGUE_WARNINGS);
?>
	<table class="listing">
		<colgroup>
			<col width="10%" />
			<col width="10%" />
			<col width="8%" />
			<col width="40%" />
			<col width="20%" />
			<col width="14%" />
		</colgroup>
		<tr>
			<th><?php echo Lang::DIVISION; ?></th>
			<th><?php echo Lang::TEAM; ?></th>
			<th><?php echo Lang::VALUE; ?></th>
			<th><?php echo Lang::REASON; ?></th>
			<th><?php echo Lang::ADMIN; ?></th>
			<th><?php echo Lang::DATE; ?></th>
		</tr>
		<tr><td colspan="6" class="line"></td></tr>
<?php
	$req = "SELECT c.tag, w.qui_warn, w.date_warn, w.valeur, w.motif, w.team, c.divi
			FROM lg_warns w, lg_clans c
			WHERE w.team = c.id
			AND c.divi != 0
			ORDER BY divi ASC";
	$t = mysql_query($req);
	$k = 0;
	while ($l = mysql_fetch_row($t)) {
		echo '<tr'.Alternator::get_alternation($k).'>
			<td align="center"><a href="?f=league_division&div='.$l[6].'">'.$l[6].'</a></td>
			<td align="center"><a href="?f=team_profile&id='.$l[5].'">'.$l[0].'</a></td>
			<td align="center">'.$l[3].'</td>
			<td style="padding: 8px 0 8px 0;">'.$l[4].'</td>
			<td><a href="?f=userprofile&username='.$l[1].'">'.$l[1].'</a></td>
			<td>'.date(Lang::DATE_FORMAT_DAY, $l[2]).'</td>
		</tr>';
	}
	
	echo '</table>';
	ArghPanel::end_tag();
?>