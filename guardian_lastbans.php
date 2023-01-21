
<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::GUARDIAN_ADMIN
		)
	);

?>
<link type="text/css" rel="stylesheet" href="guardian.css" />
<form name="frmLGPlayers" method="post" action="?f=guardian_lastbans">
<?php ArghPanel::begin_tag("Ladder Guardian - Last bans"); ?>
<div class="lg-content">
	<table border="0" cellpadding="2" cellspacing="0" style="width: 100%; table-layout: fixed;">
		<colgroup><col width="25" /><col width="15" /><col /></colgroup>
		<tr>
			<td colspan="3"><strong>Légende</strong></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="gl_ban">&nbsp;</td>
			<td>&nbsp;&nbsp;Ban en cours</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="gl_life">&nbsp;</td>
			<td>&nbsp;&nbsp;Banlife</td>
		</tr>
	</table>
	<br />
<?php
		$req = "
			SELECT T2.username, T2.ggc, T1.quand, T1.duree, T1.par_qui,
			CASE WHEN IFNULL(T1.duree, -1) = -1 THEN 'none' ELSE 
				CASE WHEN IFNULL(T1.duree, -1) = 0 THEN 'life' ELSE 
					CASE WHEN (T1.duree * 86400) + T1.quand > ".time()." THEN 'ban' ELSE 'none' END
				END
			END AS 'bantype'  
			FROM lg_ladderbans AS T1 
			INNER JOIN lg_users AS T2
			ON T1.qui = T2.username
			ORDER BY T1.quand DESC
			LIMIT 0, 50";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%">';
			echo '<colgroup><col width="5" /><col width="50" /><col width="150" /><col /><col width="100" /></colgroup>';
			$count = 0;
			while ($obj = mysql_fetch_object($res)) {
				$css = ($count++ % 2 == 0) ? " class='alternate'" : "";
				echo '<tr>';
				echo '<td class="gl_'.$obj->bantype.'">&nbsp;</td>';
				echo '<td'.$css.' align="center" style="height: 25px">';
				if ($obj->duree == 0) {
					echo '<img src="img/infini.gif" alt="indéfini" align="absmiddle" />';
				} else {
					echo '<strong>'.$obj->duree.'j</strong>';
				}
				echo '</td>';
				echo '<td'.$css.'>'.date("d/m/Y H:i:s", $obj->quand).'</td>';
				echo '<td'.$css.'>&nbsp;&nbsp;<a href="http://www.dota.fr/ligue/?f=guardian_players&player='.$obj->username.'">'.$obj->username.'</a></td>';
				echo '<td'.$css.' style="text-align: right">'.$obj->par_qui.'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
?>
</div>
<?php ArghPanel::end_tag(ArghPanelMode::NORMAL); ?>
</form>