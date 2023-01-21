<?php
	
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LEAGUE_HEADADMIN,
			RightsMode::LEAGUE_ADMIN
		)
	);
	
	$manche = 0;
	if (isset($_GET['manche'])) {
		$manche = (int)$_GET['manche'];
	}
	
	$team1 = 0;
	if (isset($_GET['team1'])) {
		$team1 = (int)$_GET['team1'];
	}
	
	$team2 = 0;
	if (isset($_GET['team2'])) {
		$team2 = (int)$_GET['team2'];
	}
	
	// Verification paramètre entrants
	if ($manche + $team1 + $team2 == 0) {
		exit();
	}
	
	$isPost = false;
	if (isset($_POST['save'])) {
		$isPost = true;
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
		 t2.logo AS 'team2logo',";
	if ($manche == 1) {
		$req .= "
			m.xml1 AS 'xml', m.stats1 AS 'stats' ";	
	} else {
		$req .= "
			m.xml2 AS 'xml', m.stats2 AS 'stats' ";	
	}
	$req .= "
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
	$xml = $obj->xml;
	$stats = $obj->stats;

	require_once '/home/www/ligue/classes/ReplayParser.php';

?>
<link rel="stylesheet" href="themes/default/parser.css" type="text/css">
<form action="?f=match_report_stats&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>&manche=<?php echo $manche ?>" method="post">
<?php
	ArghPanel::begin_tag('Stats Report - '.$obj->team1tag.' / '.$obj->team2tag.' - Manche '.$manche);
?>
<table width="100%">
	<tr>
		<td align="left" valign="top">
			<?php
			if ($xml == '') {
				echo '<div style="text-align: center">';
				echo "<span class='lose'>Fichier absent, veuillez parser le replay</span><br /><br />";
				echo '<input type="button" style="width: 80%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href=\'/ligue/?f=match&team1='.$team1.'&team2='.$team2.'\';" />';
				echo '</div>';
			} else if ($isPost == false) {
				if ($stats == 0) {
					echo "<input type='hidden' name='stats' id='stats' value='1' />";
					echo '<div style="text-align: center">';
					echo "<span class='lose'>Le tableau de stats est masqué</span><br /><br />";
					echo '<input type="submit" style="width: 40%" name="save" value="Afficher les stats" />&nbsp;&nbsp;';
					echo '<input type="button" style="width: 40%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href=\'/ligue/?f=match&team1='.$team1.'&team2='.$team2.'\';" />';
					echo '</div>';
				} else {
					echo "<input type='hidden' name='stats' id='stats' value='0' />";
					echo '<div style="text-align: center">';
					echo "<span class='lose'>Le tableau de stats est affiché</span><br /><br />";
					echo '<input type="submit" style="width: 40%" name="save" value="Masquer les stats" />&nbsp;&nbsp;';
					echo '<input type="button" style="width: 40%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href=\'/ligue/?f=match&team1='.$team1.'&team2='.$team2.'\';" />';
					echo '</div>';
				}
			?>
			<br />
			<?php
				$replay = $replay = DotaReplay::load_from_txt('/home/www/ligue/match_files/'.$xml.'.txt');
				echo ReplayFunctions::html_stats($replay);
			} else if ($isPost == true) {
				if (isset($_POST['save'])) {
					$upd = "UPDATE lg_matchs SET ";
					if ($manche == 1) {
						$upd .= " stats1 = '".$_POST['stats']."'";
					} else {
						$upd .= " stats2 = '".$_POST['stats']."'";
					}
					$upd .= " WHERE team1 = '".$obj->team1."' AND team2 = '".$obj->team2."'";
					mysql_query($upd);
				}
				echo '<div style="text-align: center">';
				echo "<span class='lose'>Modification effectuée</span><br /><br />";
				echo '<input type="button" style="width: 80%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href=\'/ligue/?f=match&team1='.$team1.'&team2='.$team2.'\';" />';
				echo '</div>';
			}
			?>
		</td>
	</tr>
</table>
<?php
	ArghPanel::end_tag();
?>