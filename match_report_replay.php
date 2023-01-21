<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LEAGUE_HEADADMIN,
			RightsMode::LEAGUE_ADMIN
		)
	);

	$team1 = 0;
	if (isset($_GET['team1'])) {
		$team1 = (int)$_GET['team1'];
	}
	
	$team2 = 0;
	if (isset($_GET['team2'])) {
		$team2 = (int)$_GET['team2'];
	}
	
	// Verification paramètre entrants
	if ($team1 + $team2 == 0) {
		exit();
	}
	
	$req = "
		SELECT
		 m.id,
		 m.divi,
		 m.team1,
		 t1.tag AS 'team1tag',
		 t1.name AS 'team1name',
		 t1.logo AS 'team1logo',
		 m.team2,
		 t2.tag AS 'team2tag',
		 t2.name AS 'team2name',
		 t2.logo AS 'team2logo'
		FROM
		 lg_matchs AS m
		INNER JOIN lg_clans AS t1 ON t1.id = m.team1
		INNER JOIN lg_clans AS t2 ON t2.id = m.team2
		WHERE
		 (m.team1 = '".$team1."' AND m.team2 = '".$team2."')
		OR (m.team1 = '".$team2."' AND m.team2 = '".$team1."')";
	$qry = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($qry) == 0) {
		exit();
	}
	$obj = mysql_fetch_object($qry);
	
	if (ArghSession::is_rights(RightsMode::LEAGUE_ADMIN) && (int)ArghSession::get_league_admin() != $obj->divi) exit();
		
	$team1 = $obj->team1;
	$team2 = $obj->team2;
	
	$isPost = false;
	if (isset($_POST['isteam1']) || isset($_POST['isteam2']) || isset($_POST['game1']) || isset($_POST['game2']) || isset($_POST['save1']) || isset($_POST['save2'])) {
		$isPost = true;
	}
	$isPreview = false;
	if (isset($_POST['game1']) || isset($_POST['game2'])) {
		$isPreview = true;
	}
	$isSave = false;
	if (isset($_POST['save1']) || isset($_POST['save2'])) {
		$isSave = true;
	}

	require_once '/home/www/ligue/classes/ReplayParser.php';
	
?>
<link rel="stylesheet" href="themes/default/parser.css" type="text/css">
<?php ArghPanel::begin_tag('Fichiers'); ?>
<table class="listing">
	<colgroup>
		<col width="50" />
		<col />
		<col />
		<col width="150" />
		<col width="100" />
	</colgroup>
	<thead>
		<tr>
			<th>Type</th>
			<th>Nom fichier</th>
			<th>Uploadé par</th>
			<th>Date d'upload</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
<?php
	//Listing fichiers
	$req = "SELECT * FROM lg_uploads WHERE match_id='".$obj->id."' ORDER BY id ASC";
	$t = mysql_query($req);
	$i = 0;
	while ($l = mysql_fetch_object($t)) {
		$type = strrchr($l->fichier, '.');
		$type = substr($type, 1);
		if ($type == 'w3g') {
			$img = 'icon_w3g.jpg';
			$desc = 'Replay';
			echo '<tr'.Alternator::get_alternation($i).'>';
			echo '<td>&nbsp;<img src="'.$img.'" alt="'.$desc.'"></td>';
			echo '<td><a href="match_files/'.$l->fichier.'" onClick="javascript:download('.$l->id.')">'.stripslashes($l->comment).'</a></td>';
			echo '<td>'.$l->qui_upload.'</td>';
			echo '<td>'.date("d M Y à H\hi",$l->date_upload).'</td>';
			echo '<td style="text-align: center;"><a href="?f=match_report_replay&team1='.$team1.'&team2='.$team2.'&id='.$l->fichier.'">Parse</a></td>';
			echo '</tr>';
		}
	}
?>
</table>
<div style="text-align: center; margin-top: 10px;">
	<input type="button" style="width: 80%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href='/ligue/?f=match&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>';" />
</div>
<?php 

	ArghPanel::end_tag();

	if ($isPost == false) {
		if (isset($_GET['id'])) {

			$path = '/home/www/ligue/match_files/'.$_GET['id'];
			$parser = new ReplayParser($path);
			$parser->txt_serialize();

			$replay = DotaReplay::load_from_txt('/home/www/ligue/match_files/'.$_GET['id'].'.txt');

			echo '<form action="?f=match_report_replay&team1='.$team1.'&team2='.$team2.'&id='.$_GET['id'].'" method="post">';
			
			ArghPanel::begin_tag('Parsing '.$_GET['id']);
			
			echo '<hr />';
			echo ReplayFunctions::html($replay, true, true, false, false, true, false, true);
			echo '<input type="hidden" name="map_version" value="'.$replay->version.'" />';
			echo '<br />';
			echo '<div style="text-align: center">';
			echo '<input type="submit" style="width: 40%" name="isteam1" value="La Team 1 est '.$obj->team1tag.'" />&nbsp;&nbsp;';
			echo '<input type="submit" style="width: 40%" name="isteam2" value="La Team 2 est '.$obj->team1tag.'" />';
			echo '</div>';

			$playercount = 1;
			foreach ($replay->sentinel->players AS $key => $player) {
				$preq = "SELECT * FROM lg_users INNER JOIN lg_clans ON lg_users.clan = lg_clans.id WHERE (ggc = '".mysql_real_escape_string($player->name)."' OR bnet = '".mysql_real_escape_string($player->name)."' OR username = '".mysql_real_escape_string($player->name)."')";
				$pqry = mysql_query($preq) or die(mysql_error());
				echo '<input type="hidden" name="h'.$playercount.'" value="'.$player->hero['hero'].'" />';
				echo '<input type="hidden" name="hi'.$playercount.'" value="'.$player->hero['img'].'" />';
				if (mysql_num_rows($pqry ) == 0) {
					echo '<input type="hidden" name="rpn'.$playercount.'" value="'.$player->name.'" />';
				} else {
					$p = mysql_fetch_object($pqry);
					echo '<input type="hidden" name="rpn'.$playercount.'" value="'.$player->name.'" />';
					echo '<input type="hidden" name="un'.$playercount.'" value="'.$p->username.'" />';
				}
				$playercount++;
			}
			foreach ($replay->scourge->players AS $key => $player) {
				$preq = "SELECT * FROM lg_users INNER JOIN lg_clans ON lg_users.clan = lg_clans.id WHERE (ggc = '".mysql_real_escape_string($player->name)."' OR bnet = '".mysql_real_escape_string($player->name)."' OR username = '".mysql_real_escape_string($player->name)."')";
				$pqry = mysql_query($preq) or die(mysql_error());
				echo '<input type="hidden" name="h'.$playercount.'" value="'.$player->hero['hero'].'" />';
				echo '<input type="hidden" name="hi'.$playercount.'" value="'.$player->hero['img'].'" />';
				if (mysql_num_rows($pqry ) == 0) {
					echo '<input type="hidden" name="rpn'.$playercount.'" value="'.$player->name.'" />';
				} else {
					$p = mysql_fetch_object($pqry);
					echo '<input type="hidden" name="rpn'.$playercount.'" value="'.$player->name.'" />';
					echo '<input type="hidden" name="un'.$playercount.'" value="'.$p->username.'" />';
				}
				$playercount++;
			}
			$bancount = 1;
			foreach ($replay->sentinel->bans AS $key => $ban) {
				echo '<input type="hidden" name="b'.$bancount.'" value="'.$ban['hero'].'" />';
				echo '<input type="hidden" name="bi'.$bancount.'" value="'.$ban['img'].'" />';
				$bancount++;
			}
			foreach ($replay->scourge->bans AS $key => $ban) {
				echo '<input type="hidden" name="b'.$bancount.'" value="'.$ban['hero'].'" />';
				echo '<input type="hidden" name="bi'.$bancount.'" value="'.$ban['img'].'" />';
				$bancount++;
			}

			ArghPanel::end_tag();			
			
			echo '</form>';

		}
	} else {
		echo '<form action="?f=match_report_replay&team1='.$team1.'&team2='.$team2.'&id='.$_GET['id'].'" method="post">';
		
		echo '<input type="hidden" name="map_version" value="'.$_POST['map_version'].'" />';
		
		ArghPanel::begin_tag('Parsing '.$_GET['id']);
					
		if (isset($_POST['isteam1'])) {
			$team1lib = $obj->team1tag;
			$team2lib = $obj->team2tag;
			$team1req = "SELECT * FROM lg_users WHERE clan = '".$obj->team1."' ORDER BY ggc";
			$team2req = "SELECT * FROM lg_users WHERE clan = '".$obj->team2."' ORDER BY ggc";
			echo '<input type="hidden" name="isteam1" value="1" />';
		} else if (isset($_POST['isteam2'])) {
			$team1lib = $obj->team2tag;
			$team2lib = $obj->team1tag;
			$team1req = "SELECT * FROM lg_users WHERE clan = '".$obj->team2."' ORDER BY ggc";
			$team2req = "SELECT * FROM lg_users WHERE clan = '".$obj->team1."' ORDER BY ggc";
			echo '<input type="hidden" name="isteam2" value="1" />';
		}
		$team1res = mysql_query($team1req);
		$team1sel = '<select name="##" style="width: 100%""><option value=""></option>';
		while ($pdb = mysql_fetch_object($team1res)) {
			$team1sel .= '<option value="'.$pdb->username.'">'.$pdb->ggc.' ('.$pdb->username.')</option>';
		}
		$team1sel .= '</select>';
		for ($i = 1; $i < 6; $i++) {
			echo '<img src="/ligue/parser/Images/'.$_POST['bi'.$i].'.png" width="32" align="absmiddle" alt="" title="'.stripslashes($_POST['b'.$i]).'" />';
			echo '<input type="hidden" name="b'.$i.'" value="'.stripslashes($_POST['b'.$i]).'" />';
			echo '<input type="hidden" name="bi'.$i.'" value="'.$_POST['bi'.$i].'" />';
		}
		echo "&nbsp;<b>Team ".$team1lib."</b>";
		echo "<blockquote>";
		echo '<table style="width: 100%"><colgroup><col width="45" /><col witdh="150" /><col width="300" /></colgroup>';
		for ($i = 1; $i < 6; $i++) {
			echo '<tr>';
			echo '<td><img alt="" title="'.stripslashes($_POST['h'.$i]).'" src="/ligue/parser/Images/'.$_POST['hi'.$i].'.png" width="32" align="absmiddle" />';
			echo '<input type="hidden" name="h'.$i.'" value="'.stripslashes($_POST['h'.$i]).'" /></td>';
			echo '<input type="hidden" name="hi'.$i.'" value="'.$_POST['hi'.$i].'" /></td>';
			if (isset($_POST['un'.$i])) {
				echo '<td><a href="?f=player_profile&player='.stripslashes($_POST['un'.$i]).'">'.stripslashes($_POST['rpn'.$i]).'</a>';
				echo '<input type="hidden" name="rpn'.$i.'" value="'.stripslashes($_POST['rpn'.$i]).'" /></td>';
				echo '<input type="hidden" name="un'.$i.'" value="'.stripslashes($_POST['un'.$i]).'" /></td>';
				echo '<td>'.stripslashes($_POST['un'.$i]).'</td>';
			} else {
				echo '<td>'.stripslashes($_POST['rpn'.$i]);
				echo '<input type="hidden" name="rpn'.$i.'" value="'.stripslashes($_POST['rpn'.$i]).'" /></td>';
				echo '<td>';
				echo str_replace('name="##"', 'name="un'.$i.'"', $team1sel);
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
		echo "</blockquote>";
		
		$team2res = mysql_query($team2req);
		$team2sel = '<select name="##" style="width: 100%""><option value=""></option>';
		while ($pdb = mysql_fetch_object($team2res)) {
			$team2sel .= '<option value="'.$pdb->username.'">'.$pdb->ggc.' ('.$pdb->username.')</option>';
		}
		$team2sel .= '</select>';
		for ($i = 6; $i < 11; $i++) {
			echo '<img src="/ligue/parser/Images/'.$_POST['bi'.$i].'.png" width="32" align="absmiddle" alt="" title="'.stripslashes($_POST['b'.$i]).'" />';
			echo '<input type="hidden" name="b'.$i.'" value="'.stripslashes($_POST['b'.$i]).'" />';
			echo '<input type="hidden" name="bi'.$i.'" value="'.stripslashes($_POST['bi'.$i]).'" />';
		}
		echo "&nbsp;<b>Team ".$team2lib."</b>";
		echo "<blockquote>";
		echo '<table style="width: 100%"><colgroup><col width="45" /><col witdh="150" /><col width="300" /></colgroup>';
		for ($i = 6; $i < 11; $i++) {
			echo '<tr>';
			echo '<td><img alt="" title="'.stripslashes($_POST['h'.$i]).'" src="/ligue/parser/Images/'.$_POST['hi'.$i].'.png" width="32" align="absmiddle" />';
			echo '<input type="hidden" name="h'.$i.'" value="'.stripslashes($_POST['h'.$i]).'" /></td>';
			echo '<input type="hidden" name="hi'.$i.'" value="'.$_POST['hi'.$i].'" /></td>';
			if (isset($_POST['un'.$i])) {
				echo '<td><a href="?f=player_profile&player='.stripslashes($_POST['un'.$i]).'">'.stripslashes($_POST['rpn'.$i]).'</a>';
				echo '<input type="hidden" name="rpn'.$i.'" value="'.stripslashes($_POST['rpn'.$i]).'" /></td>';
				echo '<input type="hidden" name="un'.$i.'" value="'.stripslashes($_POST['un'.$i]).'" /></td>';
				echo '<td>'.stripslashes($_POST['un'.$i]).'</td>';
			} else {
				echo '<td>'.stripslashes($_POST['rpn'.$i]);
				echo '<input type="hidden" name="rpn'.$i.'" value="'.stripslashes($_POST['rpn'.$i]).'" /></td>';
				echo '<td>';
				echo str_replace('name="##"', 'name="un'.$i.'"', $team2sel);
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
		echo "</blockquote>";
		if ($isPreview == true) {
			echo '<div style="text-align: center">';
			if (isset($_POST['game1'])) {
				echo '<input type="submit" style="width: 80%" name="save1" value="Valider Manche 1" />';
			} else {
				echo '<input type="submit" style="width: 80%" name="save2" value="Valider Manche 2" />';
			}
			echo '</div>';
		} else if ($isSave == true) {
			$upd = "UPDATE lg_matchs SET ";
			if (isset($_POST['save1'])) {
				$manche = 1;
				$upd .= " version1 = '".mysql_real_escape_string($_POST['map_version'])."',";
				$upd .= " xml1 = '".mysql_real_escape_string($_GET['id'])."',";
				if (isset($_POST['isteam1'])) {
					$upd .= " p1 = '".mysql_real_escape_string(stripslashes($_POST['un1']))."',";
					$upd .= " p2 = '".mysql_real_escape_string(stripslashes($_POST['un2']))."',";
					$upd .= " p3 = '".mysql_real_escape_string(stripslashes($_POST['un3']))."',";
					$upd .= " p4 = '".mysql_real_escape_string(stripslashes($_POST['un4']))."',";
					$upd .= " p5 = '".mysql_real_escape_string(stripslashes($_POST['un5']))."',";
					$upd .= " p6 = '".mysql_real_escape_string(stripslashes($_POST['un6']))."',";
					$upd .= " p7 = '".mysql_real_escape_string(stripslashes($_POST['un7']))."',";
					$upd .= " p8 = '".mysql_real_escape_string(stripslashes($_POST['un8']))."',";
					$upd .= " p9 = '".mysql_real_escape_string(stripslashes($_POST['un9']))."',";
					$upd .= " p10 = '".mysql_real_escape_string(stripslashes($_POST['un10']))."',";
					$upd .= " h1 = '".mysql_real_escape_string(stripslashes($_POST['h1']))."',";
					$upd .= " h2 = '".mysql_real_escape_string(stripslashes($_POST['h2']))."',";
					$upd .= " h3 = '".mysql_real_escape_string(stripslashes($_POST['h3']))."',";
					$upd .= " h4 = '".mysql_real_escape_string(stripslashes($_POST['h4']))."',";
					$upd .= " h5 = '".mysql_real_escape_string(stripslashes($_POST['h5']))."',";
					$upd .= " h6 = '".mysql_real_escape_string(stripslashes($_POST['h6']))."',";
					$upd .= " h7 = '".mysql_real_escape_string(stripslashes($_POST['h7']))."',";
					$upd .= " h8 = '".mysql_real_escape_string(stripslashes($_POST['h8']))."',";
					$upd .= " h9 = '".mysql_real_escape_string(stripslashes($_POST['h9']))."',";
					$upd .= " h10 = '".mysql_real_escape_string(stripslashes($_POST['h10']))."',";
					$upd .= " ban1 = '".mysql_real_escape_string(stripslashes($_POST['b1']))."',";
					$upd .= " ban2 = '".mysql_real_escape_string(stripslashes($_POST['b2']))."',";
					$upd .= " ban3 = '".mysql_real_escape_string(stripslashes($_POST['b3']))."',";
					$upd .= " ban4 = '".mysql_real_escape_string(stripslashes($_POST['b4']))."',";
					$upd .= " ban5 = '".mysql_real_escape_string(stripslashes($_POST['b5']))."',";
					$upd .= " ban6 = '".mysql_real_escape_string(stripslashes($_POST['b6']))."',";
					$upd .= " ban7 = '".mysql_real_escape_string(stripslashes($_POST['b7']))."',";
					$upd .= " ban8 = '".mysql_real_escape_string(stripslashes($_POST['b8']))."',";
					$upd .= " ban9 = '".mysql_real_escape_string(stripslashes($_POST['b9']))."',";
					$upd .= " ban10 = '".mysql_real_escape_string(stripslashes($_POST['b10']))."'";
				} else {
					$upd .= " p1 = '".mysql_real_escape_string(stripslashes($_POST['un6']))."',";
					$upd .= " p2 = '".mysql_real_escape_string(stripslashes($_POST['un7']))."',";
					$upd .= " p3 = '".mysql_real_escape_string(stripslashes($_POST['un8']))."',";
					$upd .= " p4 = '".mysql_real_escape_string(stripslashes($_POST['un9']))."',";
					$upd .= " p5 = '".mysql_real_escape_string(stripslashes($_POST['un10']))."',";
					$upd .= " p6 = '".mysql_real_escape_string(stripslashes($_POST['un1']))."',";
					$upd .= " p7 = '".mysql_real_escape_string(stripslashes($_POST['un2']))."',";
					$upd .= " p8 = '".mysql_real_escape_string(stripslashes($_POST['un3']))."',";
					$upd .= " p9 = '".mysql_real_escape_string(stripslashes($_POST['un4']))."',";
					$upd .= " p10 = '".mysql_real_escape_string(stripslashes($_POST['un5']))."',";
					$upd .= " h1 = '".mysql_real_escape_string(stripslashes($_POST['h6']))."',";
					$upd .= " h2 = '".mysql_real_escape_string(stripslashes($_POST['h7']))."',";
					$upd .= " h3 = '".mysql_real_escape_string(stripslashes($_POST['h8']))."',";
					$upd .= " h4 = '".mysql_real_escape_string(stripslashes($_POST['h9']))."',";
					$upd .= " h5 = '".mysql_real_escape_string(stripslashes($_POST['h10']))."',";
					$upd .= " h6 = '".mysql_real_escape_string(stripslashes($_POST['h1']))."',";
					$upd .= " h7 = '".mysql_real_escape_string(stripslashes($_POST['h2']))."',";
					$upd .= " h8 = '".mysql_real_escape_string(stripslashes($_POST['h3']))."',";
					$upd .= " h9 = '".mysql_real_escape_string(stripslashes($_POST['h4']))."',";
					$upd .= " h10 = '".mysql_real_escape_string(stripslashes($_POST['h5']))."',";
					$upd .= " ban1 = '".mysql_real_escape_string(stripslashes($_POST['b6']))."',";
					$upd .= " ban2 = '".mysql_real_escape_string(stripslashes($_POST['b7']))."',";
					$upd .= " ban3 = '".mysql_real_escape_string(stripslashes($_POST['b8']))."',";
					$upd .= " ban4 = '".mysql_real_escape_string(stripslashes($_POST['b9']))."',";
					$upd .= " ban5 = '".mysql_real_escape_string(stripslashes($_POST['b10']))."',";
					$upd .= " ban6 = '".mysql_real_escape_string(stripslashes($_POST['b1']))."',";
					$upd .= " ban7 = '".mysql_real_escape_string(stripslashes($_POST['b2']))."',";
					$upd .= " ban8 = '".mysql_real_escape_string(stripslashes($_POST['b3']))."',";
					$upd .= " ban9 = '".mysql_real_escape_string(stripslashes($_POST['b4']))."',";
					$upd .= " ban10 = '".mysql_real_escape_string(stripslashes($_POST['b5']))."'";
				}
			} else {
				$manche = 2;
				$upd .= " version2 = '".mysql_real_escape_string($_POST['map_version'])."',";
				$upd .= " xml2 = '".mysql_real_escape_string($_GET['id'])."',";
				if (isset($_POST['isteam1'])) {
					$upd .= " p1r2 = '".mysql_real_escape_string(stripslashes($_POST['un6']))."',";
					$upd .= " p2r2 = '".mysql_real_escape_string(stripslashes($_POST['un7']))."',";
					$upd .= " p3r2 = '".mysql_real_escape_string(stripslashes($_POST['un8']))."',";
					$upd .= " p4r2 = '".mysql_real_escape_string(stripslashes($_POST['un9']))."',";
					$upd .= " p5r2 = '".mysql_real_escape_string(stripslashes($_POST['un10']))."',";
					$upd .= " p6r2 = '".mysql_real_escape_string(stripslashes($_POST['un1']))."',";
					$upd .= " p7r2 = '".mysql_real_escape_string(stripslashes($_POST['un2']))."',";
					$upd .= " p8r2 = '".mysql_real_escape_string(stripslashes($_POST['un3']))."',";
					$upd .= " p9r2 = '".mysql_real_escape_string(stripslashes($_POST['un4']))."',";
					$upd .= " p10r2 = '".mysql_real_escape_string(stripslashes($_POST['un5']))."',";
					$upd .= " h1r2 = '".mysql_real_escape_string(stripslashes($_POST['h6']))."',";
					$upd .= " h2r2 = '".mysql_real_escape_string(stripslashes($_POST['h7']))."',";
					$upd .= " h3r2 = '".mysql_real_escape_string(stripslashes($_POST['h8']))."',";
					$upd .= " h4r2 = '".mysql_real_escape_string(stripslashes($_POST['h9']))."',";
					$upd .= " h5r2 = '".mysql_real_escape_string(stripslashes($_POST['h10']))."',";
					$upd .= " h6r2 = '".mysql_real_escape_string(stripslashes($_POST['h1']))."',";
					$upd .= " h7r2 = '".mysql_real_escape_string(stripslashes($_POST['h2']))."',";
					$upd .= " h8r2 = '".mysql_real_escape_string(stripslashes($_POST['h3']))."',";
					$upd .= " h9r2 = '".mysql_real_escape_string(stripslashes($_POST['h4']))."',";
					$upd .= " h10r2 = '".mysql_real_escape_string(stripslashes($_POST['h5']))."',";
					$upd .= " ban1r2 = '".mysql_real_escape_string(stripslashes($_POST['b6']))."',";
					$upd .= " ban2r2 = '".mysql_real_escape_string(stripslashes($_POST['b7']))."',";
					$upd .= " ban3r2 = '".mysql_real_escape_string(stripslashes($_POST['b8']))."',";
					$upd .= " ban4r2 = '".mysql_real_escape_string(stripslashes($_POST['b9']))."',";
					$upd .= " ban5r2 = '".mysql_real_escape_string(stripslashes($_POST['b10']))."',";
					$upd .= " ban6r2 = '".mysql_real_escape_string(stripslashes($_POST['b1']))."',";
					$upd .= " ban7r2 = '".mysql_real_escape_string(stripslashes($_POST['b2']))."',";
					$upd .= " ban8r2 = '".mysql_real_escape_string(stripslashes($_POST['b3']))."',";
					$upd .= " ban9r2 = '".mysql_real_escape_string(stripslashes($_POST['b4']))."',";
					$upd .= " ban10r2 = '".mysql_real_escape_string(stripslashes($_POST['b5']))."'";
				} else {
					$upd .= " p1r2 = '".mysql_real_escape_string(stripslashes($_POST['un1']))."',";
					$upd .= " p2r2 = '".mysql_real_escape_string(stripslashes($_POST['un2']))."',";
					$upd .= " p3r2 = '".mysql_real_escape_string(stripslashes($_POST['un3']))."',";
					$upd .= " p4r2 = '".mysql_real_escape_string(stripslashes($_POST['un4']))."',";
					$upd .= " p5r2 = '".mysql_real_escape_string(stripslashes($_POST['un5']))."',";
					$upd .= " p6r2 = '".mysql_real_escape_string(stripslashes($_POST['un6']))."',";
					$upd .= " p7r2 = '".mysql_real_escape_string(stripslashes($_POST['un7']))."',";
					$upd .= " p8r2 = '".mysql_real_escape_string(stripslashes($_POST['un8']))."',";
					$upd .= " p9r2 = '".mysql_real_escape_string(stripslashes($_POST['un9']))."',";
					$upd .= " p10r2 = '".mysql_real_escape_string(stripslashes($_POST['un10']))."',";
					$upd .= " h1r2 = '".mysql_real_escape_string(stripslashes($_POST['h1']))."',";
					$upd .= " h2r2 = '".mysql_real_escape_string(stripslashes($_POST['h2']))."',";
					$upd .= " h3r2 = '".mysql_real_escape_string(stripslashes($_POST['h3']))."',";
					$upd .= " h4r2 = '".mysql_real_escape_string(stripslashes($_POST['h4']))."',";
					$upd .= " h5r2 = '".mysql_real_escape_string(stripslashes($_POST['h5']))."',";
					$upd .= " h6r2 = '".mysql_real_escape_string(stripslashes($_POST['h6']))."',";
					$upd .= " h7r2 = '".mysql_real_escape_string(stripslashes($_POST['h7']))."',";
					$upd .= " h8r2 = '".mysql_real_escape_string(stripslashes($_POST['h8']))."',";
					$upd .= " h9r2 = '".mysql_real_escape_string(stripslashes($_POST['h9']))."',";
					$upd .= " h10r2 = '".mysql_real_escape_string(stripslashes($_POST['h10']))."',";
					$upd .= " ban1r2 = '".mysql_real_escape_string(stripslashes($_POST['b1']))."',";
					$upd .= " ban2r2 = '".mysql_real_escape_string(stripslashes($_POST['b2']))."',";
					$upd .= " ban3r2 = '".mysql_real_escape_string(stripslashes($_POST['b3']))."',";
					$upd .= " ban4r2 = '".mysql_real_escape_string(stripslashes($_POST['b4']))."',";
					$upd .= " ban5r2 = '".mysql_real_escape_string(stripslashes($_POST['b5']))."',";
					$upd .= " ban6r2 = '".mysql_real_escape_string(stripslashes($_POST['b6']))."',";
					$upd .= " ban7r2 = '".mysql_real_escape_string(stripslashes($_POST['b7']))."',";
					$upd .= " ban8r2 = '".mysql_real_escape_string(stripslashes($_POST['b8']))."',";
					$upd .= " ban9r2 = '".mysql_real_escape_string(stripslashes($_POST['b9']))."',";
					$upd .= " ban10r2 = '".mysql_real_escape_string(stripslashes($_POST['b10']))."'";
				}
			}
			$upd .= " WHERE team1 = '".$obj->team1."' AND team2 = '".$obj->team2."'";
			mysql_query($upd);
			
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_PARSING_PICKS, $obj->id, $manche), AdminLog::TYPE_LEAGUE);
			$al->save_log();
			
			//$upd="INSERT INTO lg_adminlog (qui, quand, quoi) VALUES ('".$_SESSION['username']."', '".time()."', 'Parsing Pick du match ".$obj->id." manche ".$manche."')";
			//mysql_query($upd);
			echo '<div style="text-align: center">';
			echo '<span class="lose">Manche enregistrée</span><br /><br />';
			echo '<input type="button" style="width: 80%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href=\'/ligue/?f=match&team1='.$team1.'&team2='.$team2.'\';" />';
			echo '</div>';
		} else {
			echo '<div style="text-align: center">';
			echo '<input type="submit" style="width: 40%" name="game1" value="Enregistrer Manche 1" />&nbsp;&nbsp;';
			echo '<input type="submit" style="width: 40%" name="game2" value="Enregistrer Manche 2" />';
			echo '</div>';
		}
		
		ArghPanel::end_tag();

		echo '</form>';
	}
	
?>