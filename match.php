<link rel="stylesheet" href="themes/default/parser.css" type="text/css">
<?php

	require_once '/home/www/ligue/classes/ReplayClasses.php';

	include 'FCKeditor/fckeditor.php';
	include 'classes/MatchStates.php';
	include 'classes/GooglePie.php';
	
	//Escapes strings to be included in javascript
	function jsspecialchars($s) {
		return preg_replace('/([^ !#$%@()*+,-.\x30-\x5b\x5d-\x7e])/e',"'\\x'.(ord('\\1')<16? '0': '').dechex(ord('\\1'))", $s);
	}
	
	if ($_GET['action'] == 'delete_file') {
		$upload_id = (int)$_GET['id'];
		$req = "SELECT * FROM lg_uploads WHERE id='".$upload_id."'";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);
		if ($l->qui_upload == ArghSession::get_username() or ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN, RightsMode::NEWS_HEADADMIN, RightsMode::NEWS_NEWSER, RightsMode::SHOUTCAST_HEADADMIN, RightsMode::SHOUTCAST_SHOUTCASTER))) {
			unlink('match_files/'.$l->fichier);
			mysql_query("DELETE FROM lg_uploads WHERE id = '".$upload_id."'");
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_DELETE_MATCH_FILE), $l->match_id, AdminLog::TYPE_LEAGUE);
			$al->save_log();
		}
	}

	function mysql_fetch_rowsarr($result, $numass = MYSQL_BOTH) {
		$got = array();
		if(mysql_num_rows($result) == 0) return $got;
		mysql_data_seek($result, 0);
		while ($row = mysql_fetch_array($result, $numass)) {
			array_push($got, $row);
		}
		return $got;
	}
	
	function get_player($a, $un) {
		$p = '';
		$l = sizeof($a);
		for ($i = 0; $i < $l; $i++) {
			if ($a[$i]['username'] == $un) {
				$p = $a[$i]['ggc'];
			}
		}
		return $p;
	}

	function replay_definition_heroes($definition, $name) {
		foreach ($definition->heroes as $key => $value) {
			if ($value['code'] != $value['base_code']) continue;
			if ($value['hero'] == $name) return $value;
		}
		return null;
	}

	function createMatchReport($objmatch, $manche) {
	
		$team1tag = $objmatch->tag1;
		$team2tag = $objmatch->tag2;
		
		$stats = $manche == 1 ? $objmatch->stats1 : $objmatch->stats2;
		$xml = $manche == 1 ? $objmatch->xml1 : $objmatch->xml2;
		
		$ver = $manche == 1 ? $objmatch->version1 : $objmatch->version2;
		if ($ver == '') {
			$req = "SELECT version FROM parser_versions WHERE is_league_version = 1";
			$res = mysql_query($req) or die(mysql_error());
			$row = mysql_fetch_row($res);
			$ver = $row[0];
		}
		$def = new ReplayDefinition($ver);
	
		$html = '';
		$html .= '<table border="0" cellpadding="0" cellspacing="0" class="parser center">';
		$html .= '<colgroup><col width="50%" /><col width="50%" /></colgroup>';
		$html .= '<thead><tr><th colspan="2" style="text-align: left; border-bottom: solid 1px white;">'.Lang::MATCH_SIDE.' '.$manche.'</th></tr></thead>';
		$html .= '<tr><td colspan="2">&nbsp;</td></tr>';
		$html .= '<tr><td class="left">';
		$html .= '<span><strong>'.($manche == 1 ? $team1tag : $team2tag).'</strong></span>';
		$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'forbidden.jpg" width="32" alt="" align="absmiddle" title="Bans" />';
		for ($i = 1; $i < 6; $i++) {
			eval('$ban = $objmatch->ban'.$i.($manche == 1 ? '' : 'r2').';');
			if ($ban != '') {
				$hero = replay_definition_heroes($def, $ban);
				if ($hero != null) $html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.$hero['img'].'.png" width="32" align="absmiddle" alt="" title="'.$hero['hero'].'" />';
				else $html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
			} else {
				$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
			}
		}
		$html .= '</td><td class="right">';
		$html .= '<span><strong>'.($manche == 1 ? $team2tag : $team1tag).'</strong></span>';
		for ($i = 6; $i < 11; $i++) {
			eval('$ban = $objmatch->ban'.$i.($manche == 1 ? '' : 'r2').';');
			if ($ban != '') {
				$hero = replay_definition_heroes($def, $ban);
				if ($hero != null) $html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.$hero['img'].'.png" width="32" align="absmiddle" alt="" title="'.$hero['hero'].'" />';
				else $html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
			} else {
				$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
			}
		}
		$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'forbidden.jpg" width="32" alt="" align="absmiddle" title="Bans" />';
		$html .= '</td></tr>';
		$html .= '<tr><td colspan="2">&nbsp;</td></tr>';
		$html .= '<tr><td class="left">';
		for ($i = 1; $i < 6; $i++) {
			eval('$p = $objmatch->p'.$i.($manche == 1 ? '' : 'r2').';');
			eval('$h = $objmatch->h'.$i.($manche == 1 ? '' : 'r2').';');
			$html .= '<p class="teams-player">';
			if ($h != '') {
				$hero = replay_definition_heroes($def, $h);
				if ($hero != null) $html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.$hero['img'].'.png" width="32" align="absmiddle" alt="" title="'.$hero['hero'].'" />';
				else $html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
			} else {
				$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
			}
			if ($p != '') {
				$html .= '<a href="?f=player_profile&player='.$p.'">'.$p.'</a>';
			} else {
				$html .= '&nbsp;';
			}
			$html .= '</p>';
		}
		$html .= '</td><td class="right">';
		for ($i = 6; $i < 11; $i++) {
			eval('$p = $objmatch->p'.$i.($manche == 1 ? '' : 'r2').';');
			eval('$h = $objmatch->h'.$i.($manche == 1 ? '' : 'r2').';');
			$html .= '<p class="teams-player">';
			if ($p != '') {
				$html .= '<a href="?f=player_profile&player='.$p.'">'.$p.'</a>';
			} else {
				$html .= '&nbsp;';
			}
			if ($h != '') {
				$hero = replay_definition_heroes($def, $h);
				if ($hero != null) $html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.$hero['img'].'.png" width="32" align="absmiddle" alt="" title="'.$hero['hero'].'" />';
				else $html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
			} else {
				$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
			}
			$html .= '</p>';
		}
		$html .= '</td></tr></table>';
		if ($stats == 1) {
			$replay = $replay = DotaReplay::load_from_txt('/home/www/ligue/match_files/'.$xml.'.txt');
			$html .= '<br />'.ReplayFunctions::html_stats($replay);
		}
		return $html;
	}

?>
<script type="text/javascript">
	
	function download(file_id) {
		requete('download_file.php?file_id=' + file_id);
	}
	
	function requete(fichier) {
		if (window.XMLHttpRequest) xhr_object = new XMLHttpRequest();
		else if (window.ActiveXObject) xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
		else return (false);
		xhr_object.open("GET", fichier, false);
		xhr_object.send(null);
		if (xhr_object.readyState == 4) return (xhr_object.responseText);
		else return (false);
	}
</script>
	
<?php
	//Initialisation
	$lineup = 0;
	$team1 = (int)$_GET['team1'];
	$team2 = (int)$_GET['team2'];
	
	//Sélection du match
	$req = "SELECT m.*, c1.tag as tag1, c1.name as name1, c1.logo as logo1, c2.tag as tag2, c2.name as name2, c2.logo as logo2
			FROM lg_matchs m, lg_clans c1, lg_clans c2
			WHERE c1.id = '".$team1."'
			AND c2.id = '".$team2."'
			AND (m.team1 = '".$team1."' AND m.team2 = '".$team2."') OR (m.team1 = '".$team2."' AND m.team2 = '".$team1."')
			
			LIMIT 1";
	$result = mysql_query($req);
	
	//Vérif
	if (mysql_num_rows($result) == 0) {
		exit;
	}
	
	//Traitement
	$omatch = mysql_fetch_object($result);

	$logo1 = $omatch->logo1;
	$logo2 = $omatch->logo2;

	//logo 1
	if (!@getimagesize($logo1)) {
		$logo1 = '/ligue/nologo.jpg';
	}

	//logo 2
	if (!@getimagesize($logo2)) {
		$logo2 = '/ligue/nologo.jpg';
	}

	$divi = $omatch->divi;

	ArghPanel::begin_tag(Lang::LEAGUE_MATCH.' - '.Lang::DIVISION.' '.$divi);
	
?>
<table>
	<colgroup>
		<col />
		<col />
		<col />
		<col />
		<col />
		<col />
		<col />
	</colgroup>
<tr>
	<td colspan="3"><center><h3><?php echo '<a href="?f=team_profile&id='.$omatch->team1.'">'.$omatch->name1.' ['.$omatch->tag1.']</a>'; ?></h3></center></td>
	<td>&nbsp;</td>
	<td colspan="3"><center><h3><?php echo '<a href="?f=team_profile&id='.$omatch->team2.'">'.$omatch->name2.' ['.$omatch->tag2.']</a>'; ?></h3></center></td>
</tr>
<tr>
	<td colspan="3"><center><?php echo '<img src="'.$logo1.'" alt="" />'; ?></center></td>
	<td><center><?php echo Lang::VERSUS; ?></center></td>
	<td colspan="3"><center><?php echo '<img src="'.$logo2.'" alt="" />'; ?></center></td>
</tr>

<?php
	$req = "SELECT winner, COUNT(*)
			FROM lg_paris
			WHERE match_id = '".$omatch->id."'
			GROUP BY winner";
	$t = mysql_query($req);
	while ($l = mysql_fetch_row($t)) {
		switch($l[0]) {
			case 1:
				$w1 = $l[1];
				break;
			case 2:
				$w2 = $l[1];
				break;
			case 3:
				$n = $l[1];
		}
	}
	
	$total = $w1 + $w2 + $n;
	
	$c1 = @round(100 * $w1 / $total, 2);
	$c2 = @round(100 * $n / $total, 2);
	$c3 = @round(100 * $w2 / $total, 2);
	
	$gp = new GooglePie();
	$gp->set_size(300, 100);
	$gp->add_slice(new PieSlice($omatch->tag1, $c1, 'ffff33'));
	$gp->add_slice(new PieSlice($omatch->tag2, $c3, '6666ff'));
	$gp->add_slice(new PieSlice(Lang::DRAW, $c2, 'aaaaaa'));
?>
<tr><td colspan="7">&nbsp;</td></tr>
<tr><td colspan="7">
<center>
	<table>
		<tr><td colspan="2"><center><strong><?php echo Lang::PRONOSTICS; ?></strong></center></td></tr>
		<tr><td>
			<?php echo Lang::WIN.' '.$omatch->tag1.': <b>'.$c1.'%</b> ('.$w1.' '.Lang::VOTES.')'; ?><br />
			<?php echo Lang::WIN.' '.$omatch->tag2.': <b>'.$c3.'%</b> ('.$w2.' '.Lang::VOTES.')'; ?><br />
			<?php echo Lang::DRAW.': <b>'.$c2.'%</b> ('.$n.' '.Lang::VOTES.')'; ?>
		</td><td>
			<?php $gp->render(); ?>
		</td></tr>
	</table>
</center>
</td></tr>
<tr><td colspan="7">&nbsp;</td></tr>

<?php
	//$two_weeks = 1209600;
?>

<tr>
	<td colspan="4"><?php echo Lang::DEFAULT_DATE; ?>: </td>
	<!--<td colspan="2"><span class="info"><?php //echo date(Lang::DATE_FORMAT_HOUR, $omatch->date_defaut).' - '.date(Lang::DATE_FORMAT_HOUR, $omatch->date_defaut + $two_weeks); ?></span></td>-->
	<td colspan="2"><span class="info"><?php echo date(Lang::DATE_FORMAT_HOUR, $omatch->date_defaut); ?></span></td>
	<td>&nbsp;</td>
</tr>
<?php
	if ($omatch->team_propose != 0) {

		$req = "SELECT crank FROM lg_users WHERE username = '".$omatch->qui_propose."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		$date_prop = date(Lang::DATE_FORMAT_HOUR, $omatch->date_proposee);
		
		//Si la proposition provient d'un admin
		if ($omatch->team_propose == -1) {
			$sentence = Lang::DATE_IMPOSED_BY.' <b>'.$omatch->qui_propose.'</b> ('.Lang::ADMIN.')';
		} else {
			$sentence = Lang::DATE_PROPOSED_BY.' <b>'.$omatch->qui_propose.'</b> <img src="'.$l[0].'.gif" alt="" />';
		}
		
		echo '<tr><td colspan="4">'.Lang::DATE.' '.$sentence.': </td><td colspan="2">'.$date_prop.' </td><td>';
		if (ArghSession::is_logged() && (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN)) || ArghSession::get_username() == $omatch->qui_propose)) {
			echo '
			<form method="POST" action="index.php?f=match_date_delete">
				<input type="hidden" name="id" value="'.$omatch->id.'" />
				<input type="hidden" name="team1" value="'.$omatch->team1.'" />
				<input type="hidden" name="team2" value="'.$omatch->team2.'" />
				<input type="hidden" name="team_propose" value="'.$omatch->team_propose.'" />
				<input type="hidden" name="qui_propose" value="'.$omatch->qui_propose.'" />
				<input type="submit" value="'.Lang::CANCEL.'" />
			</form>';
		}
		echo '</td></tr>';
	}
	if ($omatch->date_acceptation != 0) {
		$req = "SELECT crank FROM lg_users WHERE username='".$omatch->qui_accepte."' LIMIT 1";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		$date_acc = date(Lang::DATE_FORMAT_HOUR, $omatch->date_acceptation);
		echo '<tr><td colspan="7">'.sprintf(Lang::PROPOSITION_ACCEPTED, $date_acc, $omatch->qui_accepte).'<img src="'.$l[0].'.gif" alt="" /></td></tr>';
	}

?>
<?php
	//ouvert
	if ($omatch->etat == MatchStates::NOT_PLAYED_YET) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="win">'.Lang::OPEN.'</span></td></tr>';
		
		//Si aucune date n'a été proposée
		if ((ArghSession::is_logged() && ((ArghSession::get_clan_rank() <= 2 && (ArghSession::get_clan() == $omatch->team1 || ArghSession::get_clan() == $omatch->team2)) || (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN)) && ArghSession::get_clan() != $omatch->team1 && ArghSession::get_clan() != $omatch->team2))) && $omatch->date_proposee == 0) {
			echo '<form method="POST" action="index.php?f=match_date_add">';
			
			//Passage de valeurs par POST
			echo '<input type="hidden" name="qui_propose" value="'.ArghSession::get_username().'" />';
			echo '<input type="hidden" name="id" value="'.$omatch->id.'" />';
			echo '<input type="hidden" name="team1" value="'.$omatch->team1.'" />';
			echo '<input type="hidden" name="team2" value="'.$omatch->team2.'" />';
			
			//La proposition de date est considérée comme admin seulement si l'admin n'est pas membre d'une des deux teams, sinon il est simplement considéré comme joueur
			if (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN)) && ArghSession::get_clan() != $omatch->team1 && ArghSession::get_clan() != $omatch->team2) {
				echo '<input type="hidden" name="team_propose" value="-1" />';
			}
			else {
				echo '<input type="hidden" name="team_propose" value="'.ArghSession::get_clan().'" />';
			}
			
			echo '<tr><td colspan="7">'.Lang::PROPOSE_DATE.': <b><select name="day">';
			$days = range(1, 31);
			foreach ($days as $day) {
				echo '<option value="'.$day.'">'.$day.'</option>';
			}
			echo '</select>/<select name="month">';
			$months = range(1, 12);
			foreach ($months as $month) {
				echo '<option value="'.$month.'">'.Lang::$MONTHS_ARRAY[$month - 1].'</option>';
			}
			$current_year = date("Y");
			$next_year = $current_year + 1;
			echo '</select>/<select name="year">
				<option selected="selected" value="'.$current_year.'">'.$current_year.'</option>
				<option value="'.$next_year.'">'.$next_year.'</option>
				</select>&nbsp;&nbsp; @ &nbsp;<select name="hour">';
			$hours = range(0, 23);
			$default = 20;
			foreach ($hours as $hour) {
				if ($hour == $default) {
					echo '<option selected="selected" value="'.$default.'">'.$default.'</option>';
				} else {
					echo '<option value="'.$hour.'">'.$hour.'</option>';
				}
			}
			echo '</select>:<select name="minute">';
			for ($min = 0; $min <= 45; $min += 15) {
				echo '<option value="'.$min.'">'.$min.'</option>';
			}
			echo '</select> &nbsp; </b><input type="submit" value="'.Lang::VALIDATE.'" /></form></td></tr>';
		}
	
		//Si une date a été proposée
		if ((ArghSession::is_logged() && $omatch->team_propose != ArghSession::get_clan() && ((ArghSession::get_clan_rank() <= 2 && (ArghSession::get_clan() == $omatch->team1 || ArghSession::get_clan() == $omatch->team2)) || (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN)) && ArghSession::get_clan() != $omatch->team1 && ArghSession::get_clan() != $omatch->team2))) && $omatch->team_propose > 0 && $omatch->date_acceptation == 0) {
			echo '<tr><td colspan="7">&nbsp;</td></tr>
				  <tr><td colspan="4">'.Lang::ACCEPT_DATE_PROPOSAL.'</td>
				  <td colspan="3">';
				  
			//formulaire acceptation
			echo '<form method="POST" action="index.php?f=match_date_confirm" style="display: inline;">';
			echo '<input type="hidden" name="id" value="'.$omatch->id.'" />';
			echo '<input type="hidden" name="id1" value="'.$omatch->team1.'" />';
			echo '<input type="hidden" name="id2" value="'.$omatch->team2.'" />';
			echo '<input type="hidden" name="team_propose" value="'.$omatch->team_propose.'" />';
			echo '<input type="hidden" name="date_acceptation" value="'.$omatch->date_acceptation.'" />';
			echo '<input type="submit" value="'.Lang::ACCEPT.'" />';
			echo '</form>';
			echo '&nbsp;&nbsp;';
			//formulaire refus
			echo '<form method="POST" action="index.php?f=match_date_refuse" style="display: inline;">';
			echo '<input type="hidden" name="id" value="'.$omatch->id.'" />';
			echo '<input type="hidden" name="team_propose" value="'.$omatch->team_propose.'" />';
			echo '<input type="hidden" name="id1" value="'.$omatch->team1.'" />';
			echo '<input type="hidden" name="id2" value="'.$omatch->team2.'" />';
			echo '<input type="submit" value="'.Lang::REFUSE.'">';
			echo '</form>';

			echo '</td></tr>';
		}
	
	//defwin team1
	} elseif ($omatch->etat == MatchStates::TEAM_ONE_DEFAULT_WIN) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.sprintf(Lang::MATCH_DEFAULT_WIN, $omatch->name1).'</td></tr>';
	//defwin team2
	} elseif ($omatch->etat == MatchStates::TEAM_TWO_DEFAULT_WIN) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.sprintf(Lang::MATCH_DEFAULT_WIN, $omatch->name2).'</td></tr>';
	//win team1
	} elseif ($omatch->etat == MatchStates::TEAM_ONE_REGULAR_WIN) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.sprintf(Lang::MATCH_WON_BY_2_0, $omatch->name1).'</td></tr>';
		$lineup = 1;
	//win team2
	} elseif ($omatch->etat == MatchStates::TEAM_TWO_REGULAR_WIN) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.sprintf(Lang::MATCH_WON_BY_2_0, $omatch->name2).'</td></tr>';
		$lineup = 1;
	//draw, sentinel
	} elseif ($omatch->etat == MatchStates::DRAW_REGULAR_SENTINEL) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.Lang::MATCH_DRAW_SENTINEL.'</td></tr>';
		$lineup = 1;
	//draw, scourge
	} elseif ($omatch->etat == MatchStates::DRAW_REGULAR_SCOURGE) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.Lang::MATCH_DRAW_SCOURGE.'</td></tr>';
		$lineup = 1;
	//win team1 round1 + defwin round2
	} elseif ($omatch->etat == MatchStates::TEAM_ONE_WINS_WITH_SCOURGE_DEFWIN) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.sprintf(Lang::MATCH_WON_WITH_SCOURGE_DEFWIN, $omatch->name1).'</td></tr>';
		$lineup = 1;
	//win team1 round2 + defwin round1
	} elseif ($omatch->etat == MatchStates::TEAM_ONE_WINS_WITH_SENTINEL_DEFWIN) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.sprintf(Lang::MATCH_WON_WITH_SENTINEL_DEFWIN, $omatch->name1).'</td></tr>';
		$lineup = 1;
	//win team2 round1 + defwin round2
	} elseif ($omatch->etat == MatchStates::TEAM_TWO_WINS_WITH_SCOURGE_DEFWIN) {
		echo '<tr><td colspan="7"><b>Statut</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.sprintf(Lang::MATCH_WON_WITH_SCOURGE_DEFWIN, $omatch->name2).'</td></tr>';
		$lineup = 1;
	//win team2 round2 + defwin round1
	} elseif ($omatch->etat == MatchStates::TEAM_TWO_WINS_WITH_SENTINEL_DEFWIN) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.sprintf(Lang::MATCH_WON_WITH_SENTINEL_DEFWIN, $omatch->name2).'</td></tr>';
		$lineup = 1;
	//Match jamais joué et clos
	} elseif ($omatch->etat == MatchStates::ADMIN_CLOSED) {
		echo '<tr><td colspan="7"><b>'.Lang::STATUS.'</b>: <span class="lose">'.Lang::CLOSED.'</span></td></tr>
		<tr><td colspan="7">'.Lang::MATCH_ADMIN_CLOSED.'</td></tr>';
	}
	
	//lineup
	if ($lineup == 1) {
		if ($omatch->etat != MatchStates::TEAM_ONE_WINS_WITH_SENTINEL_DEFWIN && $omatch->etat != MatchStates::TEAM_TWO_WINS_WITH_SCOURGE_DEFWIN) {
			//manche 1
			echo '<tr><td colspan="7">&nbsp;</td></tr>';
			echo '<tr><td colspan="7" align="center">'.createMatchReport($omatch, 1).'</td></tr>';
			//echo '<tr><td colspan="7" align="center">'.createMatchReport($omatch, $omatch->tag1, $omatch->tag2, 1).'</td></tr>';
			/*
			$file="../ligue/replay/xml-report/argh_".$team1."-".$team2."_1.xml";
			if (file_exists($file)) {
				echo '<tr><td colspan=7>&nbsp;</td></tr>';
				echo '<tr><td colspan=7><center><a href="'.$file.'">Voir le rapport détaillé</a></center></td></tr>';
			}
			*/
		}
		if ($omatch->etat != MatchStates::TEAM_ONE_WINS_WITH_SCOURGE_DEFWIN && $omatch->etat != MatchStates::TEAM_TWO_WINS_WITH_SENTINEL_DEFWIN) {
			//manche 2
			echo '<tr><td colspan="7">&nbsp;</td></tr>';
			echo '<tr><td colspan="7" align="center">'.createMatchReport($omatch, 2).'</td></tr>';
			//echo '<tr><td colspan="7" align="center">'.createMatchReport($omatch, $omatch->tag1, $omatch->tag2, 2).'</td></tr>';
			/*
			$file="../ligue/replay/xml-report/argh_".$team1."-".$team2."_2.xml";
			if (file_exists($file)) {
				echo "<tr><td colspan=7>&nbsp;</td></tr>";
				echo '<tr><td colspan=7><center><a href="'.$file.'">Voir le rapport détaillé</a></center></td></tr>';
			}
			*/
		}
	}
?>
</table>

<?php
	ArghPanel::end_tag();

if (ArghSession::is_logged() && (ArghSession::is_rights(RightsMode::LEAGUE_HEADADMIN)
	|| (ArghSession::is_rights(RightsMode::LEAGUE_ADMIN) && (int)ArghSession::get_league_admin() == $divi))) {
	
	ArghPanel::begin_tag(Lang::MATCH_ADMINISTRATION);
?>
	<table class="simple">
		<colgroup>
			<col width="150" />
			<col width="200" style="text-align: center" />
			<col width="120" style="text-align: center" />
		</colgroup>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2"><a href="?f=match_report_result&team1=<?php echo $omatch->team1; ?>&team2=<?php echo $omatch->team2; ?>"><?php echo Lang::MATCH_EDIT_RESULT; ?></a></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2"><a href="?f=match_report_replay&team1=<?php echo $omatch->team1; ?>&team2=<?php echo $omatch->team2; ?>"><?php echo Lang::MATCH_LAUNCH_PARSER; ?></a></td>
		</tr>
		<tr>
			<td><strong><?php echo Lang::MATCH_SIDE; ?> 1</strong></td>
			<td><a href="?f=match_report_picks&team1=<?php echo $omatch->team1; ?>&team2=<?php echo $omatch->team2; ?>&manche=1"><?php echo Lang::MATCH_EDIT_PICKS; ?></a></td>
			<td><a href="?f=match_report_bans&team1=<?php echo $omatch->team1; ?>&team2=<?php echo $omatch->team2; ?>&manche=1"><?php echo Lang::MATCH_EDIT_BANS; ?></a></td>
		</tr>
		<tr>
			<td><strong><?php echo Lang::MATCH_SIDE; ?> 2</strong></td>
			<td><a href="?f=match_report_picks&team1=<?php echo $omatch->team1; ?>&team2=<?php echo $omatch->team2; ?>&manche=2"><?php echo Lang::MATCH_EDIT_PICKS; ?></a></td>
			<td><a href="?f=match_report_bans&team1=<?php echo $omatch->team1; ?>&team2=<?php echo $omatch->team2; ?>&manche=2"><?php echo Lang::MATCH_EDIT_BANS; ?></a></td>
		</tr>
		<tr>
			<td><strong><?php echo Lang::LEAGUE_STATISTICS; ?></strong></td>
			<td><a href="?f=match_report_stats&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>&manche=1"><?php echo Lang::MATCH_SIDE; ?> 1</a></td>
			<td><a href="?f=match_report_stats&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>&manche=2"><?php echo Lang::MATCH_SIDE; ?> 2</a></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
	</table>
<?php
	ArghPanel::end_tag();
}

ArghPanel::begin_tag(Lang::MATCH_FILES);
?>

<table class="simple">
	<colgroup>
		<col style="text-align: center" />
		<col style="text-align: center" />
		<col style="text-align: center" />
		<col style="text-align: center" />
		<col />
	</colgroup>
	<tr>
		<th><?php echo Lang::TYPE; ?></th>
		<th><?php echo Lang::FILENAME; ?></th>
		<th><?php echo Lang::UPLOADED_BY; ?></th>
		<th><?php echo Lang::UPLOAD_DATE; ?></th>
		<th>&nbsp;</th>
	</tr>
	<tr>
		<td colspan="5" class="line"></td>
	</tr>
<?php
	//Listing fichiers
	$req = "SELECT * FROM lg_uploads WHERE match_id='".$omatch->id."' ORDER BY id ASC";
	$t = mysql_query($req);
	while ($l = mysql_fetch_object($t)) {
		$type = strrchr($l->fichier, '.');
		$type = substr($type, 1);
		if ($type == 'w3g') {
			$img = 'icon_w3g.jpg';
			$desc = Lang::REPLAY;
		} else {
			$img = 'icon_jpg.jpg';
			$desc = Lang::SCREENSHOT;
		}
		echo '<tr>
			<td><img src="'.$img.'" alt="'.$desc.'" /></td>
			<td><a href="match_files/'.$l->fichier.'" onClick="javascript:download('.$l->id.')">'.stripslashes($l->comment).'</a></td>
			<td>'.$l->qui_upload.'</td>
			<td>'.date(Lang::DATE_FORMAT_HOUR, $l->date_upload).'</td><td>';
		if (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN, RightsMode::NEWS_HEADADMIN, RightsMode::NEWS_NEWSER, RightsMode::SHOUTCAST_HEADADMIN, RightsMode::SHOUTCAST_SHOUTCASTER)) || ArghSession::get_username() == $l->qui_upload) {
			echo '<a href="?f=match&action=delete_file&id='.$l->id.'&team1='.$omatch->team1.'&team2='.$omatch->team2.'">
			<img src="icon_delete.jpg" alt="'.Lang::DELETE.'" /></a>';
		}
		echo '</td></tr>';
	}
	
	//Formulaire d'upload (tauren/shams/admins)
	if ((ArghSession::is_logged()) && (((ArghSession::get_clan() == $omatch->team1 or ArghSession::get_clan() == $omatch->team2) and ArghSession::get_clan_rank() <= 3) or ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN, RightsMode::NEWS_HEADADMIN, RightsMode::NEWS_NEWSER, RightsMode::SHOUTCAST_HEADADMIN, RightsMode::SHOUTCAST_SHOUTCASTER)))) {
		echo '<form enctype="multipart/form-data" action="?f=match_upload" method="POST">';
		echo '<tr><td colspan="5">'.Lang::FILE.': <input name="fichier" type="file" /> <input name="go" type="submit" value="'.Lang::VALIDATE.'" /></td></tr>';
		echo '<tr><td colspan="5"><span class="info">'.Lang::ALLOWED_EXTENSIONS.': .w3g .jpg</span></td></tr>';
		echo '<tr><td colspan="5"><span class="info">'.Lang::MAXIMUM_WEIGHT.': 2mo</span></td></tr>';
		echo '<input type="hidden" name="team1" value="'.$omatch->team1.'" />
		<input type="hidden" name="team2" value="'.$omatch->team2.'" />
		<input type="hidden" name="match_id" value="'.$omatch->id.'" />
		</form>';
	}
?>
</table>

<?php
	ArghPanel::end_tag();

	
	//Temporaire
	$match = new Match();
	$match->_id = $omatch->id;
	$name = 'ckmatch';
	
	$can_add_message = false;
	if (ArghSession::is_logged()) {
		$can_add_message = ArghSession::is_rights(
			array(
				RightsMode::LEAGUE_HEADADMIN, 
				RightsMode::LEAGUE_ADMIN, 
				RightsMode::NEWS_HEADADMIN, 
				RightsMode::NEWS_NEWSER, 
				RightsMode::SHOUTCAST_HEADADMIN, 
				RightsMode::SHOUTCAST_SHOUTCASTER));
		if (!$can_add_message) $can_add_message = (ArghSession::get_clan() == $omatch->team1 or ArghSession::get_clan() == $omatch->team2);
	}
	
	$messager = new Messager($name, Tables::LEAGUE_MESSAGES, $match->_id, $can_add_message);
	$messager->deploy();
?>