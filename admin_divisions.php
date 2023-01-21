<?php

	ArghSession::exit_if_not_rights(
		RightsMode::LEAGUE_HEADADMIN
	);
	
	require 'classes/RoundRobin.php';
	
	ArghPanel::begin_tag(Lang::ADMIN_DIVISIONS);
	
?>
<form method="POST" action="?f=admin_divisions">
	<table class="simple">
	<tr>
		<td><b><?php echo Lang::ID; ?></b></td>
		<td><b><?php echo Lang::NAME; ?></b></td>
		<td><b><?php echo Lang::ADMIN; ?></b></td>
		<td><b><?php echo Lang::DELETE; ?> ?</b></td>
	</tr>
	<tr><td colspan="4" class="line"></td></tr>
<?php
	$error1 = false;
	if (isset($_POST['name'])) {
		$req = "SELECT nom FROM lg_divisions";
		$t = mysql_query($req);
		while ($l = mysql_fetch_row($t)) {
			if ($l[0] == $_POST['name']) {
				$error1 = true;
				break;
			}
		}
		if (!$error1) {
			$team = array();
			for ($i = 1; $i <= 10; $i++) {
				if (!empty($_POST['team'.$i])) $team[] = $_POST['team'.$i];
			}

			shuffle($team);
			$matchesPerPlayday = (count($team) % 2 == 0) ? count($team)/2 : floor(count($team)/2) ;
			
			$divi = $_POST['name'];
			
			//Heure de match
			$heure = (int)$_POST['hour'];
			$minute = (int)$_POST['minute'];
			
			//délai entre 2 journées
			$inc = (int)$_POST['delay'];
			
			//date de départ
			$day = (int)$_POST['day'];
			$month = (int)$_POST['month'];
			$year = (int)$_POST['year'];
			
			
			$dates = array();
			for ($i = 0; $i < count($team); $i++) {
				$dates[] = mktime($heure, $minute, 0, $month, $day + $i*$inc, $year);
			}

			$rr = new RoundRobin();
			$rr->create($team);
			foreach ($rr->tour as $key => $val) {
				$playday = floor($key / $matchesPerPlayday) + 1;
				//echo 'playday '.$playday.' : '.$val[0].' vs '.$val[1].'<br />';
				mysql_query("
					INSERT INTO lg_matchs (divi, team1, team2, j, etat, date_defaut) 
					VALUES ('".mysql_real_escape_string($divi)."', '".$val[0]."', '".$val[1]."', '".$playday."', '1', '".$dates[$playday-1]."')
				");
			}
			
			//Modif de la table clans
			foreach ($team as $val) {
				$req = "UPDATE lg_clans SET divi = '".mysql_real_escape_string($divi)."' WHERE id = '".$val."'";
				mysql_query($req);
			}

			//Ajout dans la table divisions
			$req="INSERT INTO lg_divisions (nom, admin) VALUES ('".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($_POST['newadmin'])."')";
			mysql_query($req);
		}
	}
	
	$error = false;
	if (isset($_POST['idmax'])) {
		//On vérifie qu'il n'y a pas 2 noms identiques
		for ($i=1; $i<=$_POST['idmax']; $i++) {
			for ($j=$i+1; $j<=$_POST['idmax'] and $i<$_POST['idmax']; $j++) {
				if ($_POST['name'.$i] == $_POST['name'.$j]) {
					$req="SELECT * FROM lg_divisions WHERE nom = '".mysql_real_escape_string($_POST['name'.$i])."'";
					if (mysql_num_rows(mysql_query($req)) > 0) {
						$error=true;
					}
				}
			}
		}
		//La requête
		if (!$error) {
			for ($i = 1; $i <= $_POST['idmax']; $i++) {
				if (isset($_POST['admin'.$i])) {
					$admin_i = $_POST['admin'.$i];
					$nom_i = $_POST['name'.$i];
					$upd_i = "UPDATE lg_divisions SET admin = '".mysql_real_escape_string($admin_i)."', nom = '".mysql_real_escape_string($nom_i)."' WHERE id = ".$i;
					mysql_query($upd_i);
				}
				//Suppression d'une division
				if ($_POST['delete'.$i] == 1) {
					mysql_query("UPDATE lg_clans SET divi = 0 WHERE divi = '".mysql_real_escape_string($_POST['name'.$i])."'");
					mysql_query("DELETE FROM lg_matchs WHERE divi = '".mysql_real_escape_string($_POST['name'.$i])."'");
					mysql_query("DELETE FROM lg_divisions WHERE id = ".$i);
					
					//Admin Log
					$al = new AdminLog(sprintf(Lang::ADMIN_LOG_DIVISION_DELETED, $_POST['name'.$i], $i), AdminLog::TYPE_LEAGUE);
					$al->save_log();
				}
			}
			//Admin Log
			$al = new AdminLog(Lang::ADMIN_LOG_DIVISION_EDITED, AdminLog::TYPE_LEAGUE);
			$al->save_log();
		} else {
			echo '<tr><td colspan="4"><span class="info">'.Lang::ADMIN_DIVISION_NAMES_MUST_BE_UNIQUE.'</span></td></tr>';
		}
	}

	$req = "SELECT * FROM lg_divisions ORDER BY id ASC";
	$t = mysql_query($req);
	while ($l = mysql_fetch_object($t)) {
		echo '<tr><td>'.$l->id.'</td><td><input type="text" size="12" name="name'.$l->id.'" value="'.$l->nom.'" maxlength="25" /></td><td><select name="admin'.$l->id.'">';
		$sreq = "
			SELECT DISTINCT * 
			FROM lg_users 
			WHERE (rights & ".RightsMode::LEAGUE_HEADADMIN.") = ".RightsMode::LEAGUE_HEADADMIN." 
			OR (rights & ".RightsMode::LEAGUE_ADMIN.") = ".RightsMode::LEAGUE_ADMIN." 
			ORDER BY username ASC";
		$st = mysql_query($sreq);
		while ($sl = mysql_fetch_object($st)) {
			echo '<option'.attr_($sl->username, $l->admin).' value="'.$sl->username.'">'.$sl->username.'</option>';
		}
		echo '</select></td><td><select name="delete'.$l->id.'"><option value="0">'.Lang::NO.'</option><option value="1">'.Lang::YES.'</option></select></td></tr>';
		$idmax = $l->id;
	}
?>
	<tr><td colspan="4"><center><input type="hidden" name="idmax" value="<?php echo $idmax; ?>" /><input type="submit" value="Valider" /></center></td></tr>
</table>
</form>
<?php
	ArghPanel::end_tag();
?>
<form method="POST" action="?f=admin_divisions">
<?php
	ArghPanel::begin_tag(Lang::ADMIN_CREATE_DIVISION);
?>
<table class="simple">

	<tr><td><b><?php echo Lang::NAME; ?></b></td><td><b><?php echo Lang::ADMIN; ?></b></td></tr>
	<tr><td colspan="2" class="line"></td></tr>
<?php
	if ($error1) {
		echo '<tr><td colspan="2"><span class="info">'.Lang::ADMIN_DIVISION_NAME_TAKEN.'</span></td></tr>';
	}
?>
	<tr><td><input type="text" maxlength="2" name="name" /></td><td><select name="newadmin">
<?php
		//$sreq = "SELECT * FROM lg_users WHERE access >= 75 ORDER BY username ASC";
		$sreq = "
			SELECT DISTINCT * 
			FROM lg_users 
			WHERE (rights & ".RightsMode::LEAGUE_HEADADMIN.") = ".RightsMode::LEAGUE_HEADADMIN." 
			OR (rights & ".RightsMode::LEAGUE_ADMIN.") = ".RightsMode::LEAGUE_ADMIN." 
			ORDER BY username ASC";
		$st = mysql_query($sreq);
		while ($sl = mysql_fetch_object($st)) {
			echo '<option value="'.$sl->username.'">'.$sl->username.'</option>';
		}
?>
	</select></td></tr>
<?php
	$req = "SELECT * FROM lg_clans WHERE divi = 0 ORDER BY name ASC";
	$t = mysql_query($req);
	$teams = array();
	while ($l = mysql_fetch_object($t)) {
		$teams[$l->id] = $l->tag.' - '.$l->name;
	}
	
	for ($i = 1; $i <= 10; $i++) {
		$team = 'team'.$i;
		echo '<tr><td>'.Lang::TEAM.' '.$i.'</td><td><select name="'.$team.'"><option value="0"> - '.Lang::NO_TEAM.' - </option>';

		foreach($teams as $key => $val) {
			echo '<option value="'.$key.'">'.$val.'</option>';
		}
		echo '</select></td></tr>';
	}
?>
</table>
	<br />
<?php echo Lang::ADMIN_DIVISION_START_DATE.': <select name="day">';

	for  ($j = 1; $j <= 31; $j++) {
		echo '<option value="'.$j.'">'.$j.'</option>';
	}
	
	echo '</select>/<select name="month">';
	
	for  ($j = 1; $j <= 12; $j++) {
		echo '<option value="'.$j.'">'.$j.'</option>';
	}
	
	echo '</select>/<select name="year">';
	
	$startingYear = date("Y");
	for  ($j = $startingYear - 1; $j <= $startingYear + 1; $j++) {
		echo '<option value="'.$j.'"'.attr_($startingYear, $j).'>'.$j.'</option>';
	}
	echo '</select><br />'.Lang::ADMIN_DIVISION_PLAYDAY_DELAY.': <select name="delay">';

	for  ($j = 1; $j <= 30; $j++) {
		if ($j == 7) {
			echo '<option selected="selected">'.$j.'</option>';
		} else { 
			echo '<option value="'.$j.'">'.$j.'</option>';
		}
	}
	
	echo '</select>'.Lang::DAYS.'<br />'.Lang::ADMIN_DIVISION_DEFAULT_DATE.': <select name="hour">';

	for  ($j = 0; $j <= 23; $j++) {
		if ($j == 20) {
			echo '<option selected="selected">'.$j.'</option>';
		} else { 
			echo '<option value="'.$j.'">'.$j.'</option>';
		}
	}
	
	echo '</select>:<select name="minute">';
	
	for  ($j = 0; $j <= 45; $j += 15) {
		echo '<option value="'.$j.'">'.$j.'</option>';
	}
	echo '</select><br />
		<center><input type="submit" value="'.Lang::CREATE.'" /></center>
	</form>';
	ArghPanel::end_tag();
?>