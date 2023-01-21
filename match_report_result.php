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
		 m.etat,
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
	if (isset($_POST['save'])) {
		$isPost = true;
	}
	
	function attr_check($x, $y) {
		if ($x == $y) {
			return ' checked = checked';
		} else {
			return '';
		}
	}

?>
<?php ArghPanel::begin_tag('Resultat - '.$obj->team1tag.' / '.$obj->team2tag); ?>
<form action="?f=match_report_result&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>" method="post">
	<?php
	if ($isPost == false) {
	?>
	<table width="100%">
		<colgroup>
			<col width="50" />
			<col width="40" />
			<col />
		</colgroup>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr><td colspan="3" style="text-align: left">Choisissez le cas de figure qui correspond au déroulement de la rencontre.</td></tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr><td colspan="3" style="text-align: left"><strong>Cas réguliers :</strong></td></tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#1</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="1"<?php echo attr_check($obj->etat, 1) ?> /></td>
			<td valign="top">Match Ouvert.<br /><span class="info">Ce match n'a pas encore été joué.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#2</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="4"<?php echo attr_check($obj->etat, 4) ?> /></td>
			<td valign="top">[<?php echo $obj->team1name ?>] gagne 2-0 à la suite du match.<br /><span class="info">Victoire de la team [<?php echo $obj->team1name ?>]. Les deux manches ont été jouées.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#3</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="5"<?php echo attr_check($obj->etat, 5) ?> /></td>
			<td valign="top">[<?php echo $obj->team2name ?>] gagne 2-0 à la suite du match.<br /><span class="info">Victoire de la team [<?php echo $obj->team2name ?>]. Les deux manches ont été jouées.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#4</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="6"<?php echo attr_check($obj->etat, 6) ?> /></td>
			<td valign="top">Match Nul, les deux manches sont remportées par Sentinel.<br /><span class="info">Les deux manches ont été jouées, la rencontre se solde par un match nul, voyant deux victoires de Sentinel.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#5</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="11"<?php echo attr_check($obj->etat, 11) ?> /></td>
			<td valign="top">Match Nul, les deux manches sont remportées par Scourge.<br /><span class="info">Les deux manches ont été jouées, la rencontre se solde par un match nul, voyant deux victoires de Scourge.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr><td colspan="3" style="text-align: left"><strong>Cas particuliers :</strong></td></tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#6</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="2"<?php echo attr_check($obj->etat, 2) ?> /></td>
			<td valign="top">[<?php echo $obj->team1name ?>] gagne 2-0 par defwin.<br /><span class="info">Victoire par défaut de la team [<?php echo $obj->team1name ?>] suite à une décision d'admin.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#7</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="3"<?php echo attr_check($obj->etat, 3) ?> /></td>
			<td valign="top">[<?php echo $obj->team2name ?>] gagne 2-0 par defwin.<br /><span class="info">Victoire par défaut de la team [<?php echo $obj->team2name ?>] suite à une décision d'admin.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#8</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="7"<?php echo attr_check($obj->etat, 7) ?> /></td>
			<td valign="top">[<?php echo $obj->team1name ?>] gagne 2-0, en remportant la manche Scourge par defwin.<br /><span class="info">[<?php echo $obj->team1name ?>] gagne la manche en Sentinel à la régulière, puis gagne la manche Scourge par defwin. [<?php echo $obj->team1name ?>] gagne donc 2-0.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#9</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="8"<?php echo attr_check($obj->etat, 8) ?> /></td>
			<td valign="top">[<?php echo $obj->team1name ?>] gagne 2-0, en remportant la manche Sentinel par defwin.<br /><span class="info">[<?php echo $obj->team1name ?>] gagne la manche en Scourge à la régulière, puis gagne la manche Sentinel par defwin. [<?php echo $obj->team1name ?>] gagne donc 2-0.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#10</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="10"<?php echo attr_check($obj->etat, 10) ?> /></td>
			<td valign="top">[<?php echo $obj->team2name ?>] gagne 2-0, en remportant la manche Scourge par defwin.<br /><span class="info">[<?php echo $obj->team2name ?>] gagne la manche en Sentinel à la régulière, puis gagne la manche Scourge par defwin. [<?php echo $obj->team2name ?>] gagne donc 2-0.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#11</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="9"<?php echo attr_check($obj->etat, 9) ?> /></td>
			<td valign="top">[<?php echo $obj->team2name ?>] gagne 2-0, en remportant la manche Sentinel par defwin.<br /><span class="info">[<?php echo $obj->team2name ?>] gagne la manche en Scourge à la régulière, puis gagne la manche Sentinel par defwin. [<?php echo $obj->team2name ?>] gagne donc 2-0.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td valign="top" style="text-align: right;">#12</td>
			<td valign="top" style="text-align: center;"><input type="radio" name="etat" value="12"<?php echo attr_check($obj->etat, 12) ?> /></td>
			<td valign="top">Match fermé par admin. Aucun point pour les deux équipes.<br /><span class="info">Exemple de cas: match non joué dans les limites de temps.</span></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
	</table>
	<div style="text-align: center">
		<input type="button" style="width: 40%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href='/ligue/?f=match&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>';" />
		<input type="submit" style="width: 40%" name="save" value="Valider" />
	</div>
	<?php
	} else {
	
		$al = new AdminLog(sprintf(Lang::ADMIN_LOG_FILLING_RESULT, $obj->id), AdminLog::TYPE_LEAGUE);
		$al->save_log();
	
		//$upd="INSERT INTO lg_adminlog (qui, quand, quoi) VALUES ('".$_SESSION['username']."', '".time()."', 'Remplissage du résultat du match ".$obj->id."')";
		//mysql_query($upd);
		
		$msg = "Résultat enregistré";
		$upd = "UPDATE lg_matchs SET etat = '".(int)$_POST['etat']."', reported = '".time()."' WHERE id = '".$obj->id."'";
		mysql_query($upd);
	?>
	<div style="text-align: center">
		<span class="lose"><?php echo $msg ?></span><br /><br />
		<input type="button" style="width: 80%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href='/ligue/?f=match&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>';" />
	</div>
	<?php
	}
	?>
</form>
<?php ArghPanel::end_tag(); ?>