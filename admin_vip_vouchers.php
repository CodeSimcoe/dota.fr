<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::VIP_HEADADMIN,
			RightsMode::VIP_ADMIN
		)
	);

	//UPDATE [POST]
	if (isset($_POST['Validate'])) {
		$data = array_combine($_POST['voucher'], $_POST['vouchs']);
		foreach ($data as $voucher => $vouchs) {
			$req  = "UPDATE lg_users SET vouchs = '".(int)$vouchs."' WHERE username = '".mysql_real_escape_string($voucher)."'";
			mysql_query($req);
		}
	}
	
	//REVOKE [GET]
	if (isset($_GET['action'])) {
		if ($_GET['action'] == 'revoke') {
			$user = mysql_real_escape_string(substr($_GET['user'], 0, 25));
			mysql_query("
				UPDATE lg_users SET
					rights = CASE WHEN rights = ".RightsMode::VIP_VOUCHER." THEN 0 ELSE (rights ^ ".RightsMode::VIP_VOUCHER.") END,
					rights_base = CASE WHEN rights_base = ".RightsMode::VIP_VOUCHER." THEN 0 ELSE rights_base END,
					rank = CASE WHEN rank = '".RightsMode::get_rights_label(RightsMode::VIP_VOUCHER)."' THEN '' ELSE rank END,
					vouchs = 0
				WHERE username = '".$user."'
			");
			mysql_query("UPDATE lg_users SET voucher = '0', vouchs = '0' WHERE username = '".$user."'");
			mysql_query("DELETE FROM lg_laddervip_admins WHERE user = '".$user."'");
		}
	}
	
	//AJOUT [POST]
	if (isset($_POST['Add'])) {
		$user = mysql_real_escape_string(substr($_POST['user'], 0, 25));
		mysql_query("
			UPDATE lg_users SET
				rights = CASE WHEN rights = 0 THEN ".RightsMode::VIP_VOUCHER." ELSE (rights | ".RightsMode::VIP_VOUCHER.") END,
				rights_base = CASE WHEN rights_base = 0 THEN ".RightsMode::VIP_VOUCHER." ELSE rights_base END,
				rank = CASE WHEN rank = '' THEN '".RightsMode::get_rights_label(RightsMode::VIP_VOUCHER)."' ELSE rank END,
				vouchs = 0
			WHERE username = '".$user."'
		");
		mysql_query("UPDATE lg_users SET voucher = '1' WHERE username = '".$user."'");
		mysql_query("INSERT INTO lg_laddervip_admins (user) VALUES ('".$user."')");
	}
	
	ArghPanel::begin_tag(Lang::VOUCH_MANAGEMENT);
	echo '<form method="POST" action="?f=admin_vip_vouchers">';
	echo '<table class="listing">';
	echo '<thead>
			<tr>
				<th>'.Lang::USERNAME.'</th>
				<th>'.Lang::VOUCHES.'</th>
				<th>'.Lang::ACTION.'</th>
			</tr>
		</thead>
		<tbdoy>';
	
	//Vouchers
	$req = "SELECT username, vouchs FROM lg_users WHERE (rights & ".RightsMode::VIP_VOUCHER.") = ".RightsMode::VIP_VOUCHER." ORDER BY username ASC";
	$t = mysql_query($req);
	$i = 0;
	while ($l = mysql_fetch_row($t)) {
		echo '<tr'.Alternator::get_alternation($i).'>
				<td>'.$l[0].'</td>
				<td>
					<input type="hidden" name="voucher[]" value="'.$l[0].'" />
					<input type="text" name="vouchs[]" value="'.$l[1].'" size="4" class="num" />
				</td>
				<td><a href="?f=admin_vip_vouchers&action=revoke&user='.$l[0].'"><img src="img/del.png" alt="" /></a></td>
			</tr>';
	}
	
	echo '<tr><td colspan="3">&nbsp;</td></tr>';
	echo '<tr><td colspan="3"><center><input type="submit" value="'.Lang::VALIDATE.'" name="Validate" /></center></td></tr>';
	echo '<tr><td colspan="3">&nbsp;</td></tr>';
	echo '<tr><td colspan="3">&nbsp;</td></tr>';
	echo '<tr><td colspan="3">&nbsp;</td></tr>';
	echo '<tr><td>'.Lang::ADD_VOUCHER.'</td><td colspan="2"><select name="user">';
	
	//Listing Users
	$req = "SELECT username FROM lg_users ORDER BY username ASC";
	$t = mysql_query($req);
	while ($l = mysql_fetch_row($t)) echo '<option value="'.$l[0].'">'.$l[0].'</option>';
	
	echo '</select> <input type="submit" name="Add" value="'.Lang::ADD.'" /></td></tr>';
	echo '</table>';
	echo '</form>';
	ArghPanel::end_tag();

?>