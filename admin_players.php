<?php
	ArghSession::exit_if_not_rights(
		RightsMode::WEBMASTER
	);
?>
<script type="text/javascript" src="/ligue/javascript/ui.datepicker.js"></script>
<script language="javascript">
	/*
	$(document).ready(function() {
		$('#datepicker').datepicker();
		$('#datepicker').datepicker('option', {dateFormat: 'yy-mm-dd'});
	});
	*/

	function countUsers(pseudo, champ) {
		if (pseudo != '') {
			$.get('ajax/countusers.php',
				{
					user: pseudo,
					field: champ,
				},
				function (data) {
					if (data > 0) {
						$("#results").html('<b> ' + data + '</b> <?php Lang::USER_OR_USERS; ?>');
					} else {
						$("#results").html('<?php echo Lang::NO_USER; ?>');
					}
				}
		}
	}

</script>

<?php
	//Champs de recherche
	$allowedFields = array(
		'username' => Lang::USERNAME,
		'bnet' => Lang::BNET_ACCOUNT,
		'ggc' => Lang::GARENA_ACCOUNT
	);
	
	ArghPanel::begin_tag(Lang::ADMIN_USER_MANAGEMENT);
?>
<form method="post" action="?f=admin_players">
<table class="simple">
	<tr><td><?php echo Lang::LOOK_FOR; ?>
	<select name="research" id="research" onChange="countUsers(document.getElementById('search').value, this.value);">
<?php
	foreach ($allowedFields as $key => $val) {
		echo '<option value="'.$key.'">'.$val.'</option>';
	}
?>
	</select>
	<?php echo Lang::CONTAINING; ?>
	<input type="text" size="20" name="search" id="search" maxlength="20" onKeyUp="countUsers(this.value, document.getElementById('research').value);" /> <input type="submit" name="go" value="<?php echo Lang::LOOK_FOR; ?>" style="width: 140px;" /> <div id="results"></div>
	</td></tr>
</table>
</form>
<?php
	ArghPanel::end_tag();
	
	//Liste des joueurs filtrée
	if (isset($_POST['go'])) {
		//Verif POST
		if (!array_key_exists($_POST['research'], $allowedFields)) {
			exit();
		}
	
		$req = "SELECT username, ggc
				FROM lg_users
				WHERE ".$_POST['research']." LIKE '%".mysql_real_escape_string($_POST['search'])."%'
				ORDER BY ".$_POST['research']." ASC
				LIMIT 50";
		$t = mysql_query($req);
		
		if (mysql_num_rows($t)) {
			
			ArghPanel::begin_tag(Lang::RESULTS);
			
			echo '<table class="listing">
				<colgroup>
					<col width="40%" />
					<col width="40%" />
					<col width="20%" />
				</colgroup>
				<tr>
					<th>'.Lang::USERNAME.'</th>
					<th>'.Lang::GARENA.'</th>
					<th>'.Lang::ACTION.'</th>
				</tr>
				<tr><td colspan="3" class="line"></td></tr>';
			$i = 0;
			while ($l = mysql_fetch_row($t)) {
				echo '<tr'.Alternator::get_alternation($i).'>
					<td><a href="?f=player_profile&player='.htmlentities($l[0]).'">'.htmlentities($l[0]).'</a></td>
					<td>'.htmlentities($l[1]).'</td>
					<td align="center">
						<form method="POST" action="?f=admin_players">
						<input type="hidden" name="username_" value='.htmlentities($l[0]).'>
						<input type="submit" value="'.Lang::EDIT.'" name="go2" />
					</td></tr></form>';
			}
			echo '</table>';
			
			ArghPanel::end_tag();
		}
	}
	
	//Modifications effectives
	if (isset($_POST['go3'])) {
		
		$user = mysql_real_escape_string(substr($_POST['username_'], 0, 25));

		ArghPanel::begin_tag(sprintf(Lang::ADMIN_USER_UPDATING_PROFILE, $user));
		
		echo '<table class="simple">';
			
			// Modification nouveau moteur de droits
			// Gestion des droits via rights, access deprecated !
			
			//if (ArghSession::get_access() < $access) {
			//	echo '<tr><td><center><span class="lose">'.sprintf(Lang::ADMIN_USER_CANT_GIVE_ACCESS, ArghSession::get_access()).'</span></center></td></tr>';
			//} else {
				$req = "UPDATE lg_users
						SET bnet = '".mysql_real_escape_string($_POST['bnet'])."',
							ggc = '".mysql_real_escape_string($_POST['ggc'])."',
							mail = '".mysql_real_escape_string($_POST['mail'])."',
							avatar = '".mysql_real_escape_string($_POST['avatar'])."',
							clan = '".(int)$_POST['team']."',
							crank = '".(int)$_POST['crank']."',
							ladder_status = '".mysql_real_escape_string($_POST['ladder_status'])."',
							active = '".(int)$_POST['active']."',
							is_gold = '".(int)$_POST['gold']."',
							gold_expire = '".(int)$_POST['gold_length']."'";
				
				if (strlen($_POST['passw']) > 0) {
					$req .= ", password = '".passHash($_POST['passw'])."'";
				}
				
				$req .= "WHERE username='".$user."'";
				mysql_query($req);
				
				echo '<tr><td><center><span class="win">'.Lang::ADMIN_INFORMATION_UPDATED.'</span></center></td></tr>';
				
				// Modification nouveau moteur de droits
				// Gestion des droits via rights, lg_ladderadmins deprecated !
			
				// Ladder
				//$del = "DELETE FROM lg_ladderadmins WHERE user = '".$user."'";
				//mysql_query($del);
				//if ($_POST['admin_ladder'] == 1) {
				//	mysql_query("INSERT INTO lg_ladderadmins (user) VALUES ('".$user."')");
				//}
				
				//Admin Log
				$al = new AdminLog(sprintf(Lang::ADMIN_LOG_USER_PROFILE_EDITED, $user), AdminLog::TYPE_ADMIN);
				$al->save_log();
			
			//}
			
		echo '</table>';
			
		ArghPanel::end_tag();
	}
	
	//Profil du joueur
	if (isset($_POST['go2']) or isset($_GET['get_player'])) {
		
		if (isset($_GET['get_player'])) {
			$user = mysql_real_escape_string(substr($_GET['get_player'], 0, 25));
		} else {
			$user = mysql_real_escape_string(substr($_POST['username_'], 0, 25));
		}
		ArghPanel::begin_tag(Lang::ADMIN_USER_MANAGEMENT);
		
		echo '<table class="simple">
			<tr><td colspan="2"><span class="info">'.Lang::ADMIN_USER_AREA.'</span></td></tr>
			<tr><td colspan="2" class="line"></td></tr>
			<form method="POST" action="?f=admin_players">';

		$req = "SELECT * FROM lg_users WHERE username = '".$user."' LIMIT 1";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);

		//Username
		echo '<tr><td><b>'.Lang::USERNAME.'</b></td><td><input size="25" maxlength="25" type="text" value="'.$user.'" name="new_username" READONLY></td></tr>';
		
		//BNet
		echo '<tr><td><b>'.Lang::BNET_ACCOUNT.'</b></td><td><input size="25" maxlength="25" type="text" value="'.$l->bnet.'" name="bnet" /></td></tr>';
		
		//GG-C
		echo '<tr><td><b>'.Lang::GARENA_ACCOUNT.'</b></td><td><input size="25" maxlength="30" type="text" value="'.$l->ggc.'" name="ggc" /></td></tr>';
		
		//Pass
		echo '<tr><td><b>'.Lang::ADMIN_USER_NEW_PASSWORD.'</b></td><td><input size="25" maxlength="50" type="text" value="" name="passw" /></td></tr>';
		
		//Mail
		echo '<tr><td><b>'.Lang::EMAIL.'</b></td><td><input size="35" maxlength="50" type="text" value="'.$l->mail.'" name="mail" /></td></tr>';
		
		//Avatar
		echo '<tr><td><b>'.Lang::AVATAR.'</b></td><td><input size="35" maxlength="250" type="text" value="'.$l->avatar.'" name="avatar" /></td></tr>';

		//Team
		echo '<tr><td><b>'.Lang::TEAM.'</b></td><td><select name="team"><option value="0">'.Lang::NO_TEAM.'</option>';
		$sreq = "SELECT * FROM lg_clans ORDER BY name ASC";
		$st = mysql_query($sreq);
		while ($sl = mysql_fetch_object($st)) {
			echo '<option value="'.$sl->id.'"'.attr_($sl->id, $l->clan).'>'.$sl->name.'</option>';
		}
		echo '</select></td></tr>';
		
		//cRank
		echo '<tr><td><b>'.Lang::CLAN_RANK.'</b></td><td><select name="crank">';

		echo '<option value="'.ClanRanks::TAUREN.'"'.attr_($l->crank, ClanRanks::TAUREN).'>'.Lang::TAUREN.'</option>';
		echo '<option value="'.ClanRanks::SHAMAN.'"'.attr_($l->crank, ClanRanks::SHAMAN).'>'.Lang::SHAMAN.'</option>';
		echo '<option value="'.ClanRanks::GRUNT.'"'.attr_($l->crank, ClanRanks::GRUNT).'>'.Lang::GRUNT.'</option>';
		echo '<option value="'.ClanRanks::PEON.'"'.attr_($l->crank, ClanRanks::PEON).'>'.Lang::PEON.'</option>';
		echo '</select></td></tr>';
		
		//Ladder Status
		echo '<tr><td><b>'.Lang::LADDER_STATUS.'</b></td><td><select name="ladder_status">';
		echo '<option value="'.LadderStates::READY.'"'.attr_($l->ladder_status, LadderStates::READY).'>'.Lang::LADDER_STATUS_READY.'</option>';
		echo '<option value="'.LadderStates::IN_NORMAL_GAME.'"'.attr_($l->ladder_status, LadderStates::IN_NORMAL_GAME).'>'.Lang::LADDER_STATUS_IN_NORMAL.'</option>';
		echo '<option value="'.LadderStates::IN_VIP_GAME.'"'.attr_($l->ladder_status, LadderStates::IN_VIP_GAME).'>'.Lang::LADDER_STATUS_IN_VIP.'</option>';
		echo '</select></td></tr>';
		

		// Modification nouveau moteur de droits
		// Gestion des droits via rights, rank deprecated !

		//Rank
		//echo '<tr><td><b>'.Lang::RANK.'</b></td><td><select name="rank" id="rank" onChange="javascript:setDefaultAccess();">
		//<option value="">'.Lang::NONE.'</option>
		//<option'.attr_($l->rank, Lang::ACCESS_WEBMASTER).'>'.Lang::ACCESS_WEBMASTER.'</option>
		//<option'.attr_($l->rank, Lang::ACCESS_ADMIN).'>'.Lang::ACCESS_ADMIN.'</option>
		//<option'.attr_($l->rank, Lang::ACCESS_REFEREE).'>'.Lang::ACCESS_REFEREE.'</option>
		//<option'.attr_($l->rank, Lang::ACCESS_ADMIN_NEWS).'>'.Lang::ACCESS_ADMIN_NEWS.'</option>
		//<option'.attr_($l->rank, Lang::ACCESS_NEWSER).'>'.Lang::ACCESS_NEWSER.'</option>
		//<option'.attr_($l->rank, Lang::ACCESS_LAN_ORGA).'>'.Lang::ACCESS_LAN_ORGA.'</option>
		//</select></td></tr>';
		
		// Modification nouveau moteur de droits
		// Gestion des droits via rights, lg_ladderadmins deprecated !

		//Admin Ladder
		//echo '<tr><td><b>'.Lang::LADDER_ADMIN.'</b></td><td><select name="admin_ladder">';
		//$req = "SELECT * FROM lg_ladderadmins WHERE user = '".$user."'";
		//$t = mysql_query($req);
		//if (mysql_num_rows($t) > 0) {
		//	echo '<option value="0">'.Lang::NO.'</option>';
		//	echo '<option value="1" selected="selected">'.Lang::YES.'</option>';
		//} else {
		//	echo '<option value="0" selected="selected">'.Lang::NO.'</option>';
		//	echo '<option value="1">'.Lang::YES.'</option>';
		//}
		//echo '</select></td></tr>';

		//Active ?
		echo '<tr><td><b>'.Lang::ADMIN_USER_ACCOUNT_ACTIVATED_INTERR.'</b></td><td><select name="active">
			<option value="1"'.attr_(1, $l->active).'>'.Lang::YES.'</option>
			<option value="0"'.attr_(0, $l->active).'>'.Lang::NO.'</option>
		</select></td></tr>';
		
		//Gold
		$one_month_later = time() + 31 * 24 * 3600;
		echo '<tr>
			<td><b>'.Lang::GOLD_ACCOUNT.'</b></td>
			<td>
				<input type="checkbox" value="1" name="gold" '.($l->is_gold == 1 ? 'checked="checked"' : '').' />
				<input type="text" id="datepicker" name="gold_length" value="'.$l->gold_expire.'" />'.($one_month_later).'<br />
				Expiration : '.($l->gold_expire == 0 ? '-' : date(Lang::DATE_FORMAT_DAY, $l->gold_expire)).'
			</td>
		</tr>';
		
		//Accès
		//echo '<tr><td><b>'.ucfirst(Lang::ACCESS).'</b></td><td><input size="4" type="text" value="'.$l->access.'" name="access" id="access" /></td></tr>';
		//echo '<tr><td colspan="2"><span class="info">'.Lang::ADMIN_USER_SITE_RANKS.'</span></td></tr>';
		
		
		echo '</table>';
		echo '<input type="hidden" value="'.$user.'" name="username_" />';
?>
		<br />
		<center>
			<input type="submit" value="<?php echo Lang::VALIDATE; ?>" name="go3" />
			<input type="hidden" value="1" name="go2" />
		</center>
		</form>
<?php
	ArghPanel::end_tag();
	}
?>
