<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::SHOUTCAST_HEADADMIN,
			RightsMode::SHOUTCAST_SHOUTCASTER
		)
	);

	require 'classes/Shoutcast.php';
	
	//Suppression
	if (isset($_GET['action']) && $_GET['action'] == 'delete') {
		$req = "DELETE FROM lg_shoutcast WHERE id = '".(int)$_GET['shout_id']."'";
		mysql_query($req);
	}
	
	//Ajout
	if (isset($_POST['add_shoutcast'])) {
		$req = "INSERT INTO lg_shoutcast (poster, team1, team2, date_shoutcast, comment)
				VALUES ('".ArghSession::get_username()."', '".(int)$_POST['team1']."', '".(int)$_POST['team2']."', '".mktime($_POST['heure'], $_POST['minute'], 0, $_POST['mois'], $_POST['jour'], $_POST['annee'])."', '".mysql_real_escape_string($_POST['comment'])."')";
		mysql_query($req);
	}
	
	ArghPanel::begin_tag(Lang::ADMIN_SHOUTCAST_MANAGEMENT);
?>
<table class="listing">
	<tr>
		<td colspan="2"><b><?php echo Lang::TEAMS; ?></b></td>
		<td><b><?php echo Lang::DATE; ?></b></td>
		<td><b><?php echo Lang::INFOS; ?></b></td>
		<td><b><?php echo Lang::POSTED_BY; ?></b></td>
		<td><b><?php echo Lang::DELETE; ?></b></td>
	</tr>
	<tr><td colspan="6" class="line"></td></tr>
<?php
	$shoutcasts = Shoutcast::get_all();
	$i = 0;
	foreach($shoutcasts as $shout) {
		$alt = Alternator::get_alternation($i);
		$grey = (time() - 10800 > $shout->_timestamp_shoutcast) ? ' class="grey"' : '';
		echo '<tr'.$grey.'>
				<td'.$alt.'>'.$shout->_team1_tag.'</td>
				<td'.$alt.'>'.$shout->_team2_tag.'</td>
				<td'.$alt.'>'.$shout->_date_shoutcast.'</td>
				<td'.$alt.'>'.stripslashes($shout->_comment).'</td>
				<td'.$alt.'>'.$shout->_poster.'</td>
				<td'.$alt.'><center><a href="?f=admin_shoutcast&action=delete&shout_id='.$shout->get_shoutcast_id().'"><img src="img/icons/delete.png" alt="'.Lang::DELETE.'" /></a></center></td>
		</tr>';
}
?>
	<tr><td colspan="6"></td></tr>
</table>
<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::ADMIN_SHOUTCAST_ADDING);
?>
<form method="POST" action="?f=admin_shoutcast">
<table class="listing">
	<colgroup>
		<col />
		<col width="90" />
		<col width="485" />
		<col />
	</colgroup>
<?php
	echo '<tr><td>'.Lang::TEAM.' 1 :</td>';
	$req = "SELECT id, tag FROM lg_clans ORDER BY tag ASC";
	$t = mysql_query($req);
	$teams = array();
	while ($l = mysql_fetch_row($t)) {
		$teams[$l[0]] = $l[1];
	}
	
	echo '<td><select name="team1" style="width: 100px;">';
	foreach ($teams as $id => $tag) {
		echo '<option value="'.$id.'">'.$tag.'</option>';
	}
	echo '</select></td></tr><tr><td>'.Lang::TEAM.' 2 :</td><td><select name="team2" style="width: 100px;">';
	foreach ($teams as $id => $tag) {
		echo '<option value="'.$id.'">'.$tag.'</option>';
	}
	
	$jours = range(1, 31);
	$mois = range(1, 12);
	$heures = range(0, 23);
	$minutes = range(0, 59);
	$annees = range(date("Y"), date("Y") + 1);
	
	echo '</select></td></tr><tr><td>'.Lang::DATE.' :</td><td><select name="jour">';
	foreach ($jours as $j) {
		echo '<option value="'.$j.'">'.$j.'</option>';
	}
	echo '</select>/<select name="mois">';
	foreach ($mois as $j) {
		echo '<option value="'.$j.'">'.$j.'</option>';
	}
	echo '</select>&nbsp;<select name="heure">';
	foreach ($heures as $j) {
		echo '<option value="'.$j.'">'.$j.'</option>';
	}
	echo '</select>:<select name="minute">';
	foreach ($minutes as $j) {
		echo '<option value="'.$j.'">'.$j.'</option>';
	}
	echo '</select>&nbsp;<select name="annee">';
	foreach ($annees as $j) {
		echo '<option value="'.$j.'">'.$j.'</option>';
	}
	echo '</select>&nbsp;&nbsp;&nbsp;<span class="info">'.Lang::ADMIN_SHOUTCAST_DATE_FORMAT.'</span>';
	echo '</td></tr><tr><td>'.Lang::COMMENTS.' :</td><td>';
	echo '<input type="text" size="45" maxlength="100" name="comment" /></td></tr>';
	echo '<tr><td colspan="2"><center><input type="submit" name="add_shoutcast" value="'.Lang::VALIDATE.'" /></center></td></tr>';
?>
	</table>
</form>
<?php
	ArghPanel::end_tag();
?>