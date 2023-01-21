<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::VIP_HEADADMIN
		)
	);

	ArghPanel::begin_tag("Liste des vouchs");
	
	$req = "SELECT u.username, u.ggc, v.rank
			FROM lg_users u, lg_laddervip_vouchlist v
			WHERE u.username = v.username
			ORDER BY v.rank DESC, u.username ASC";
	$res = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($res) != 0) {
		echo '<table class="listing">';
		echo '<colgroup><col width="200" /><col width="200" /><col /></colgroup>';
		echo '<thead><tr><th>'.Lang::USERNAME.'</th><th>'.Lang::GARENA_ACCOUNT.'</th><th>Cap Level</th></tr></thead>';
		$count = 0;
		$last_rank = 2;
		while ($obj = mysql_fetch_object($res)) {
			if ($last_rank != $obj->rank) {
				echo '<tr><td colspan="3">&nbsp;</td></tr>';
			}
			echo '<tr'.Alternator::get_alternation($count).'>';
			echo '<td><a href="?f=player_profile&player='.$obj->username.'">'.$obj->username.'</a></td>';
			echo '<td>'.$obj->ggc.'</td>';
			echo '<td>'.$obj->rank.'</td>';
			echo '</tr>';
			$last_rank = $obj->rank;
		}
		echo '</table>';
	}
	
	ArghPanel::end_tag();

?>