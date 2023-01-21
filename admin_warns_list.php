<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LEAGUE_HEADADMIN,
			RightsMode::LEAGUE_ADMIN
		)
	);

	ArghPanel::begin_tag(Lang::LEAGUE_WARNING_MANAGEMENT);

?>
<table class="listing">
	<colgroup>
		<col width="15%" />
		<col width="10%" />
		<col width="30%" />
		<col width="20%" />
		<col width="15%" />
		<col width="10%" />
	</colgroup>
	<thead>
		<tr>
			<th><?php echo Lang::TEAM; ?></th>
			<th><?php echo Lang::LEAGUE_WARN_VALUE; ?></th>
			<th><?php echo Lang::REASON; ?></th>
			<th><?php echo Lang::ADMIN; ?></th>
			<th><?php echo Lang::DATE; ?></th>
			<th><?php echo Lang::ACTION; ?></th>
		</tr>
	</thead>
	<tbody>
		<tr><td class="line" colspan="6"></td></tr>
<?php
	//Accès de l'admin (restreint à ses divisions)
	if (ArghSession::is_rights(RightsMode::LEAGUE_ADMIN)
	    && !ArghSession::is_rights(RightsMode::LEAGUE_HEADADMIN)) {
		//On récupère la division dont il est admin
		$req0 = "SELECT nom FROM lg_divisions WHERE admin = '".ArghSession::get_username()."'";
		$t0 = mysql_query($req0);
		while ($l0 = mysql_fetch_row($t0)) {
			//On selectionne les teams warnées qui sont dans cette division
			$req = "SELECT c.name, w.qui_warn, w.date_warn, w.valeur, w.motif, w.id
					FROM lg_warns w, lg_clans c
					WHERE w.team = c.id
					AND c.divi = '".$l0[0]."'";
			$t = mysql_query($req);
			//Affichage
			while ($l = mysql_fetch_row($t)) {
				echo '<tr><td>'.$l[0].'</td>
					<td><center>'.$l[3].'</center></td>
					<td>'.stripslashes($l[4]).'</td>
					<td>'.$l[1].'</td>
					<td>'.date(Lang::DATE_FORMAT_DAY, $l[2]).'</td>
					<td>
						<form method="POST" action="?f=admin_warns_delete">
							<input type="hidden" value="'.$l[5].'" name="id" />
							<input type="submit" value="'.Lang::DELETE.'" />
						</form>
					</td></tr>';
			}
		}
	} else {
		//Accès à tout
		$req = "SELECT c.name, w.qui_warn, w.date_warn, w.valeur, w.motif, w.id
				FROM lg_warns w, lg_clans c
				WHERE w.team = c.id";
		$t = mysql_query($req);
		//Affichage
		$i = 0;
		while ($l = mysql_fetch_row($t)) {
			echo '<tr'.Alternator::get_alternation($i).'><td>'.$l[0].'</td>
				<td><center>'.$l[3].'</center></td>
				<td><br /><div style="overflow: auto; height: 40px">'.stripslashes($l[4]).'</div><br /></td>
				<td>'.$l[1].'</td>
				<td>'.date(Lang::DATE_FORMAT_DAY, $l[2]).'</td>
				<td>
					<form method="POST" action="?f=admin_warns_delete">
						<input type="hidden" value="'.$l[5].'" name="id" />
						<input type="submit" value="'.Lang::DELETE.'" />
			</form>
			</td></tr>';
		}
	}
?>
	</tbody>
	</table>
<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::LEAGUE_WARN_ADDING);
?>
<form method="POST" action="?f=admin_warns_save">
<table class="simple">
	<tr>
		<td><?php echo Lang::TEAM; ?>: </td>
		<td><select name="team">
<?php
	//Accès de l'admin
	if (ArghSession::is_rights(RightsMode::LEAGUE_ADMIN)
	    && !ArghSession::is_rights(RightsMode::LEAGUE_HEADADMIN)) {
		$req0 = "SELECT nom FROM lg_divisions WHERE admin='".ArghSession::get_username()."'";
		$t0 = mysql_query($req0);
		while ($l0 = mysql_fetch_row($t0)) {
			//Affichage des teams que l'admin peut warn
			$req = "SELECT name, id
					FROM lg_clans
					WHERE divi='".$l0[0]."'
					ORDER BY name ASC";
			$t = mysql_query($req);
			while ($l = mysql_fetch_row($t)) {
				echo "<option value=".$l[1].">".$l[0]."</option>";
			}
		}
	} else {
		//Affichage des teams que l'admin peut warn
		$req = "SELECT name, id
				FROM lg_clans
				ORDER BY name ASC";
		$t = mysql_query($req);
		while ($l = mysql_fetch_row($t)) {
			echo "<option value=".$l[1].">".$l[0]."</option>";
		}
	}
?>
	</select></td></tr>
	<tr>
		<td><?php echo Lang::LEAGUE_WARN_VALUE; ?>: </td>
		<td><select name="valeur">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php echo Lang::REASON; ?>: </td>
		<td><textarea  rows="4" cols="50" name="motif"></textarea></td>
	</tr>
	<tr>
		<td colspan="2">
			<center>
				<input type="hidden" name="id" value="<?php echo $l[1]; ?>">
				<input type="submit" value="<?php echo Lang::VALIDATE; ?>">
			</center>
		</td>
	</tr>
</table>
</form>
<?php
	ArghPanel::end_tag();
?>