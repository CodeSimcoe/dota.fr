<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::LADDER_ADMIN
		)
	);
	
	function is_active($i) {
		return '<img src="img/icons/'.($i == 1 ? 'accept' : 'delete').'.png" alt="" />';
	}
	
	ArghPanel::begin_tag(Lang::LAST_REGISTERED);
?>
<table class="listing">
	<colgroup>
		<col width="20%" />
		<col width="8%" />
		<col width="36%" />
		<col width="36%" />
	</colgroup>
	<thead>
	<tr>
		<th><?php echo Lang::DATE; ?></th>
		<th><?php echo Lang::STATUS; ?></th>
		<th><?php echo Lang::USERNAME; ?></th>
		<th><?php echo Lang::GARENA; ?></th>
	</tr>
	</thead>
	<tbody>
<?php
	$mod = 0;
	$req = "SELECT u.username, u.ggc, u.mail, u.joined, u.active
			FROM lg_users u
			ORDER BY u.joined DESC
			LIMIT 100";
	$t = mysql_query($req);
	while ($l = mysql_fetch_row($t)) {
		$alt = Alternator::get_alternation($mod);
		echo '<tr'.$alt.'>';
		if (ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::GUARDIAN_ADMIN))) {
			echo '<td><a href="?f=guardian_players&player='.$l[0].'">'.date(Lang::DATE_FORMAT_HOUR, $l[3]).'</a></td>';
		} else {
			echo '<td>'.date(Lang::DATE_FORMAT_HOUR, $l[3]).'</td>';
		}
		echo '<td>'.is_active($l[4]).'</td>
			<td><a href="?f=player_profile&player='.$l[0].'">'.$l[0].'</a></td>
			<td>'.$l[1].'</td>
		</tr>
		<tr'.$alt.'>
			<td colspan="2"></td>
			<td colspan="2">'.$l[2].'</td>
		</tr>';
	}

?>
	</tbody>
</table>
<?php
	ArghPanel::end_tag();
?>