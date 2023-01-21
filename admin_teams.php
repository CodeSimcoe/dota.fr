<?php

	ArghSession::exit_if_not_rights(
		RightsMode::LEAGUE_HEADADMIN
	);

	$go = false;
	
	ArghPanel::begin_tag(Lang::ADMIN_TEAM);
	
	if (isset($_POST['clan_id'])) {
		$clan_id = (int)$_POST['clan_id'];
	}
	
	//Kick de division
	if (isset($_POST['kickfromdiv'])) {
		mysql_query("UPDATE lg_clans SET divi = 0 WHERE id = ".$clan_id);
		
		//On masque juste les matchs de la division pour avoir la possibilité de les restaurer.
		mysql_query("UPDATE lg_matchs SET etat = ".MatchStates::MASKED." WHERE team1 = ".$clan_id." OR team2 = ".$clan_id);
		
		//Admin Log
		$al = new AdminLog(sprintf(Lang::ADMIN_LOG_DIVISION_KICKED, $clan_id, $clan_id), AdminLog::TYPE_LEAGUE);
		$al->save_log();
		
		$go = true;
	}
	//Edition d'un clan
	if (isset($_POST['edit']) or $go == true) {
		$req = "SELECT * FROM lg_clans WHERE id = '".$clan_id."'";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);
		echo '<form method="POST" action="?f=admin_teams"><input type="hidden" name="clan_id" value="'.$l->id.'" />';
		echo '<table><tr><td><b>ID: </b></td><td colspan="2">'.$l->id.'</td></tr>';
		//Supression de logo
		if (!empty($l->logo)) {
			echo '<tr><td colspan="3">&nbsp;</td></tr>';
			echo '<tr><td><b>'.Lang::LOGO.': </b></td><td colspan="2"><img src="'.$l->logo.'" alt="" /></td></tr>';
			echo '<tr><td></td><td colspan="2"><input type="submit" name="delete_logo" value="'.Lang::ADMIN_DELETE_TEAM_LOGO.'" /></td></tr>';
			echo '<tr><td colspan="3">&nbsp;</td></tr>';
		} else {
			echo '<tr><td colspan="3">&nbsp;</td></tr>';
			echo '<tr><td><b>'.Lang::LOGO.': </b></td><td colspan="2"><img src="nologo.jpg" alt="" /></td></tr>';
			echo '<tr><td colspan=3>&nbsp;</td></tr>';
		}
		echo '<tr><td><b>'.Lang::NAME.': </b></td><td colspan="2"><input type="text" name="clan_name" value="'.$l->name.'" maxlength="30" size="30" /></td></tr>';
		echo '<tr><td><b>'.Lang::TAG.': </b></td><td colspan="2"><input type="text" name="clan_tag" value="'.$l->tag.'" maxlength="4" size="4" /></td></tr>';
		echo '<tr><td><b>'.Lang::PASSWORD.': </b></td><td colspan="2"><input type="text" name="clan_pass" value="'.$l->pass.'" maxlength="30" size="30" /></td></tr>';
		echo '<tr><td><b>'.Lang::WEBSITE.': </b></td><td colspan="2"><input type="text" name="clan_website" value="'.$l->website.'" maxlength="400" size="50" /></td></tr>';
		$sreq = "SELECT username FROM lg_users WHERE crank = '1' AND clan = '".$l->id."' LIMIT 1";
		$st = mysql_query($sreq);
		$sl = mysql_fetch_row($st);
		echo '<tr><td><b>'.Lang::TEAM_LEADER.': </b></td><td colspan="2"><input type="hidden" name="old_leader" value="'.$sl[0].'"><select name="new_leader"><option value="'.$sl[0].'">'.$sl[0].'</option>';
		$ssreq = "SELECT username FROM lg_users WHERE clan = '".$l->id."' ORDER BY username ASC";
		$sst = mysql_query($ssreq);
		while ($ssl = mysql_fetch_row($sst)) {
			echo '<option value="'.$ssl[0].'">'.$ssl[0].'</option>';
		}
		echo'</select></td></tr>';
		echo '<tr><td><b>'.Lang::DIVISION.': </b></td><td colspan="2">'.$l->divi.' <input type="submit" value="'.Lang::ADMIN_KICK_FROM_DIVISION.'" name="kickfromdiv" /></td></tr>';
		echo '<tr><td colspan="3">&nbsp;</td></tr>';
		echo '<tr><td></td><td colspan="2"><input type="submit" name="update_clan" value="'.Lang::ADMIN_UPDATE_INFORMATION.'"></td></tr>
		</table></form>';
	}
	
	//Suppression de logo
	if (isset($_POST['delete_logo'])) {
		$req = "UPDATE lg_clans SET logo = '' WHERE id = '".$clan_id."'";
		mysql_query($req);
		
		//Admin Log
		$sentence = sprintf(Lang::ADMIN_LOG_LOGO_REMOVED, $clan_id, $clan_id);
		$al = new AdminLog($sentence, AdminLog::TYPE_ADMIN);
		$al->save_log();
		
		echo '<tr><td colspan="3"><span class="info">'.Lang::ADMIN_LOGO_REMOVED.'</span></td></tr>';
		echo '<tr><td colspan="3">&nbsp;</td></tr>';
		
	} elseif (isset($_POST['update_clan'])) {
		
		//Edition de données du clan
		$req = "UPDATE lg_clans
				SET name = '".mysql_real_escape_string($_POST['clan_name'])."',
					tag = '".substr($_POST['clan_tag'], 0, 4)."',
					pass = '".mysql_real_escape_string($_POST['clan_pass'])."',
					website = '".mysql_real_escape_string($_POST['clan_website'])."'
				WHERE id='".$clan_id."'";
		mysql_query($req);
		
		//Admin Log
		$sentence = sprintf(Lang::ADMIN_LOG_TEAM_EDITED, $clan_id, $clan_id);
		$al = new AdminLog($sentence, AdminLog::TYPE_ADMIN);
		
		mysql_query("UPDATE lg_users SET crank = '".ClanRanks::TAUREN."' WHERE username = '".mysql_real_escape_string($_POST['new_leader'])."'");
		
		//Ancien leader -> Shaman
		if ($_POST['new_leader'] != $_POST['old_leader']) {
			mysql_query("UPDATE lg_users SET crank = '".ClanRanks::SHAMAN."' WHERE username = '".mysql_real_escape_string($_POST['old_leader'])."'");
		}
		echo '<tr><td colspan="3"><span class="info">'.Lang::ADMIN_INFORMATION_UPDATED.'</span></td></tr>';
		echo '<tr><td colspan="3">&nbsp;</td></tr>';
	}
	
	//Affichage des clans
	if (isset($_POST['search'])) {
		
		echo '<table class="listing">
			<tr><td><b>'.Lang::TEAM.'</b></td><td><b>'.Lang::TAG.'</b></td><td><b>'.Lang::ACTION.'</b></td></tr>
			<tr><td colspan="3" class="line"></td></tr>';
		
		$req = "SELECT *
				FROM lg_clans
				WHERE `".mysql_real_escape_string($_POST['research'])."`
				LIKE '%".mysql_real_escape_string($_POST['search'])."%'";
		$t = mysql_query($req);
		$i = 0;
		while ($l = mysql_fetch_object($t)) {
			$authorized = false;
			echo '<tr'.Alternator::get_alternation($i).'><td>'.$l->name.'</td><td>'.$l->tag.'</td><td>';
			
			// Modification nouveau moteur de droits
			// Seulement LEAGUE_HEADADMIN en acces => confiance
			$authorized = true;
			//Vérification que l'user est autorisé
			//if (ArghSession::get_access() > 76) {
			//	$authorized = true;
			//} else {
			//	$sreq = "SELECT admin FROM lg_divisions WHERE nom = '".$l->divi."'";
			//	$st = mysql_query($sreq);
			//	$sl = mysql_fetch_object($st);
			//	if (mysql_num_rows($st)) {
			//		$authorized = true;
			//	}
			//}
			if ($authorized) {
				echo '<form method="POST" action="?f=admin_teams"><input type="hidden" name="clan_id" value="'.$l->id.'" /><input type="submit" name="edit" value="'.Lang::EDIT.'"></form>';
			}
			echo '</td></tr>';
		}
		echo '<tr><td colspan="3">&nbsp;</td></tr>';
	}
?>
	<tr>
	<td colspan="3"><form method="post" action="?f=admin_teams"><?php echo Lang::FIND; ?>:
	<select name="research">
		<option value="name"><?php echo Lang::NAME; ?></option>
		<option value="tag"><?php echo Lang::TAG; ?></option>
	</select>
	<?php echo Lang::CONTAINING; ?>
	<input type="text" size="20" name="search" maxlength="25" /><input type="submit" name="submit" value="<?php echo Lang::LOOK_FOR; ?>"></form></td>
	</tr>
<?php
	echo '</table>';
	ArghPanel::end_tag();
?>