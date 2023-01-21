<?php
	include_once '/home/www/ligue/mysql_connect.php';
	include_once '/home/www/ligue/classes/MatchStates.php';
	include_once '/home/www/ligue/classes/ArghPanel.php';
	require_once '/home/www/ligue/classes/ArghSession.php';
	ArghSession::begin();
	require_once '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';

	//Divisions autorisées
	$allowedDivisions = array();
	
	$req = "SELECT nom FROM lg_divisions ORDER BY id ASC";
	$t = mysql_query($req);
	if (mysql_num_rows($t)) {
		while ($l = mysql_fetch_row($t)) {
			$allowedDivisions[] = $l[0];
		}
	}
	
	//Vérification GET conforme
	if (!in_array($_GET['div'], $allowedDivisions)) {
		$divi = $allowedDivisions[0];
	} else {
		$divi = $_GET['div'];
	}
	
	ArghPanel::begin_tag(Lang::DIVISION.' '.$divi);
	
	echo '<table class="listing">';
	echo '<colgroup>
		<col width="5%" />
		<col width="40%" />
		<col width="11%" />
		<col width="11%" />
		<col width="11%" />
		<col width="11%" />
		<col width="11%" />
	</colgroup>';

	//Récupération du nombre de teams dans la division
	$req = "SELECT * FROM lg_clans WHERE divi = '".$divi."' ORDER BY name ASC";
	$t = mysql_query($req);
	$nb = mysql_num_rows($t);
	$i = 1;
	while ($l = mysql_fetch_object($t)) {
		$id[$i] = $l->id;
		$team[$i] = $l->name;
		$tag[$i] = $l->tag;
		$i++;
	}
		
	//Warns
	for ($i = 1;$i <= $nb; $i++) {
		$req = "SELECT sum(valeur) FROM lg_warns WHERE team = '".$id[$i]."'";
		$t = mysql_query($req);
		while ($l = mysql_fetch_row($t)) {
			$warns[$i] = (int)$l[0];
		}
	}
	
	//Calcul du malus
	$warn_img = array();
	for ($i = 1; $i <= $nb; $i++) {
		switch ($warns[$i]) {
			case 1:
				$warn_img[$i] = '1yellowcard.gif';
				break;
			case 2:
				$malus[$i] = 1;
				$warn_img[$i] = '2yellowcard.gif';
				break;
			case 3:
				$malus[$i] = 3;
				$warn_img[$i] = '3yellowcard.gif';
				break;
			case 4:
				$malus[$i] = 4;
				$warn_img[$i] = 'redcard.gif';
				break;
			default:
				$malus[$i] = 0;
				$warn_img[$i] = null;
				break;
		}
	}	
	
	//Victoires
	for ($i = 1; $i <= $nb; $i++) {
		$req = "SELECT count(*)
				FROM lg_matchs
				WHERE (
					(team1='".$id[$i]."' AND etat='".MatchStates::TEAM_ONE_DEFAULT_WIN."')
					OR (team1='".$id[$i]."' AND etat='".MatchStates::TEAM_ONE_REGULAR_WIN."')
					OR (team1='".$id[$i]."' AND etat='".MatchStates::TEAM_ONE_WINS_WITH_SCOURGE_DEFWIN."')
					OR (team1='".$id[$i]."' AND etat='".MatchStates::TEAM_ONE_WINS_WITH_SENTINEL_DEFWIN."')
					OR (team2='".$id[$i]."' AND etat='".MatchStates::TEAM_TWO_DEFAULT_WIN."')
					OR (team2='".$id[$i]."' AND etat='".MatchStates::TEAM_TWO_REGULAR_WIN."')
					OR (team2='".$id[$i]."' AND etat='".MatchStates::TEAM_TWO_WINS_WITH_SENTINEL_DEFWIN."')
					OR (team2='".$id[$i]."' AND etat='".MatchStates::TEAM_TWO_WINS_WITH_SCOURGE_DEFWIN."')
					)
				AND divi='".$divi."'";
		$t = mysql_query($req);
		while ($l = mysql_fetch_row($t)) {
			$wins[$i] = $l[0];
		}
	}

	//Nuls
	for ($i = 1; $i <= $nb; $i++) {
		$req = "SELECT count(*)
				FROM lg_matchs
				WHERE (
					(team1='".$id[$i]."' AND etat='".MatchStates::DRAW_REGULAR_SENTINEL."')
					OR (team2='".$id[$i]."' AND etat='".MatchStates::DRAW_REGULAR_SENTINEL."')
					OR (team1='".$id[$i]."' AND etat='".MatchStates::DRAW_REGULAR_SCOURGE."')
					OR (team2='".$id[$i]."' AND etat='".MatchStates::DRAW_REGULAR_SCOURGE."')
					)
				AND divi='".$divi."'";
		$t = mysql_query($req);
			while ($l = mysql_fetch_row($t)) {
			$draws[$i] = $l[0];
		}
	}

	//Défaites
	for ($i = 1; $i <= $nb; $i++) {
		$req = "SELECT count(*)
				FROM lg_matchs
				WHERE (
					(team2='".$id[$i]."' AND etat='".MatchStates::TEAM_ONE_DEFAULT_WIN."')
					OR (team2='".$id[$i]."' AND etat='".MatchStates::TEAM_ONE_REGULAR_WIN."')
					OR (team2='".$id[$i]."' AND etat='".MatchStates::TEAM_ONE_WINS_WITH_SCOURGE_DEFWIN."')
					OR (team2='".$id[$i]."' AND etat='".MatchStates::TEAM_ONE_WINS_WITH_SENTINEL_DEFWIN."')
					OR (team1='".$id[$i]."' AND etat='".MatchStates::TEAM_TWO_DEFAULT_WIN."') 
					OR (team1='".$id[$i]."' AND etat='".MatchStates::TEAM_TWO_REGULAR_WIN."')
					OR (team1='".$id[$i]."' AND etat='".MatchStates::TEAM_TWO_WINS_WITH_SENTINEL_DEFWIN."')
					OR (team1='".$id[$i]."' AND etat='".MatchStates::TEAM_TWO_WINS_WITH_SCOURGE_DEFWIN."')
				)
				AND divi='".$divi."'";
		$t = mysql_query($req);
		while ($l = mysql_fetch_row($t)) {
			$loss[$i] = $l[0];
		}
		$matches[$i] = $wins[$i] + $draws[$i] + $loss[$i];
		//$pts[$i]= 3 * $wins[$i] + $draws[$i] - $malus[$i];
		$pts[$i]= 2 * $wins[$i] + $draws[$i] - $malus[$i];
	}
	//Tri à bulle
	for ($k = 1; $k <= $nb; $k++) {
		for ($j = 1; $j <= $nb - 1; $j++) {
			if ($pts[$j] < $pts[$j + 1] OR ($pts[$j] == $pts[$j + 1] AND $wins[$j] < $wins[$j + 1])) {
				$a_ = $team[$j];
				$b_ = $pts[$j];
				$c_ = $matches[$j];
				$d_ = $wins[$j];
				$e_ = $draws[$j];
				$f_ = $loss[$j];
				$g_ = $tag[$j];
				$i_ = $id[$j];
				$j_ = $warn_img[$j];
				$team[$j] = $team[$j + 1];
				$pts[$j] = $pts[$j + 1];
				$matches[$j] = $matches[$j + 1];
				$wins[$j] = $wins[$j + 1];
				$draws[$j] = $draws[$j + 1];
				$loss[$j] = $loss[$j + 1];
				$tag[$j] = $tag[$j + 1];
				$id[$j] = $id[$j + 1];
				$warn_img[$j] = $warn_img[$j + 1];
				$team[$j + 1] = $a_;
				$pts[$j + 1] = $b_;
				$matches[$j + 1] = $c_;
				$wins[$j + 1] = $d_;
				$draws[$j + 1] = $e_;
				$loss[$j + 1] = $f_;
				$tag[$j + 1] = $g_;
				$id[$j + 1] = $i_;
				$warn_img[$j + 1] = $j_;
			}
		}
	}
	//Admin de la division
	$req = "SELECT u.*, d.* FROM lg_users u, lg_divisions d
			WHERE u.username = d.admin
			AND d.nom = '".$divi."'
			ORDER BY d.id ASC";
	$t = mysql_query($req);
	if (mysql_num_rows($t) > 0) {
		echo '<caption>';
		while ($l = mysql_fetch_object($t)) {
			echo '<p style="margin: 0px; color: white;">'.Lang::ADMIN.': <a href="?f=player_profile&player='.$l->username.'">'.$l->username.'</a></p>';
		}
		echo '</caption>';
	}
?>
<thead>
<tr>
	<th><strong>#</strong></th>
	<th><?php echo htmlentities(Lang::TEAM); ?></th>
	<th class="diviz"><?php echo htmlentities(Lang::MATCHS); ?></th>
	<th class="diviz"><?php echo htmlentities(ucfirst(Lang::POINTS)); ?></th>
	<th class="diviz"><?php echo htmlentities(Lang::WINS); ?></th>
	<th class="diviz"><?php echo htmlentities(Lang::DRAWS); ?></th>
	<th class="diviz"><?php echo Lang::LOSSES; ?></th>
</tr>
</thead>
<tbody>
<?
	for ($i = 1; $i <= $nb; $i++) {
	
		echo '<tr style="height: 20px;">
			<td>'.$i.'</td>
			<td><a href="?f=team_profile&id='.$id[$i].'">'.htmlentities(stripslashes($team[$i])).'</a> ['.$tag[$i].']'.(empty($warn_img[$i]) ? '' : '&nbsp;<a href="?f=league_warns"><img src="/ligue/img/'.$warn_img[$i].'" width="10" height="13" /></a>').'</td>
			<td><center>'.$matches[$i].'</center></td>
			<td class="points"><span class="red"><strong>'.$pts[$i].'</strong></span></td>
			<td><center><span class="win">'.$wins[$i].'</span></center></td>
			<td><center><span class="draw">'.$draws[$i].'</span></center></td>
			<td><center><span class="lose">'.$loss[$i].'</span></center></td>
		</tr>';
	}
	
	echo '</tbody></table>';
	ArghPanel::end_tag();
?>
