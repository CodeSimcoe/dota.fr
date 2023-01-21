<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::VIP_HEADADMIN
		)
	);

	ArghPanel::begin_tag("Liste des vouchs en attente");
	
	$req = "SELECT v.* 
			FROM lg_vouchs v
			LEFT JOIN lg_laddervip_vouchlist vl
			ON v.qui = vl.username
			WHERE vl.username IS NULL
			ORDER BY qui ASC";
	$res = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($res) != 0) {
		echo '<table class="listing">';
		echo '<colgroup><col /><col width="200" /><col width="100" /></colgroup>';
		echo '<thead><tr><th>'.Lang::USERNAME.'</th><th>'.Lang::VOUCHER_VIP.'</th><th>'.Lang::DATE.'</th></tr></thead>';
		$count = 0;
		while ($obj = mysql_fetch_object($res)) {
			echo '<tr'.Alternator::get_alternation($count).'>';
			echo '<td><a href="?f=player_profile&player='.$obj->qui.'">'.$obj->qui.'</a></td>';
			echo '<td><a href="?f=player_profile&player='.$obj->voucher.'">'.$obj->voucher.'</a></td>';
			echo '<td>'.date("d/m/Y", $obj->date_vouch).'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
	
	ArghPanel::end_tag();

?>