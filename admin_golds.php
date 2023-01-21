<?php
	ArghSession::exit_if_not_rights(
		RightsMode::WEBMASTER
	);
	
	ArghPanel::begin_tag();
	
	$query = "SELECT * FROM lg_users WHERE is_gold = 1 AND gold_expire != 0 ORDER BY gold_expire DESC";
	$result = mysql_query($query);
	
	echo '<table class="listing">
		<colgroup>
		</colgroup>
		<thead>
			<tr>
				<th>'.Lang::USERNAME.'</th>
				<th>'.mysql_num_rows($result).' '.Lang::MENU_PLAYERS.'</th>
			</tr>
		</thead>
		<tbody>';
	
	$i = 0;
	while ($sql_user = mysql_fetch_object($result)) {
		echo '<tr'.Alternator::get_alternation($i).'>
				<td><a href="?f=player_profile&player='.$sql_user->username.'">'.$sql_user->username.'</a></td>
				<td>'.($sql_user->gold_expire == 0 ? '<img src="img/infini.gif" alt="" />' : round(($sql_user->gold_expire - time()) / 86400)).'</td>
			</tr>';
	}
	
	echo '</tbody>
		</table>';
	
	ArghPanel::end_tag();
?>