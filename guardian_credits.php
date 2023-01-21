<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::GUARDIAN_ADMIN
		)
	);

?>
<link type="text/css" rel="stylesheet" href="guardian.css" />
<form name="frmLGPlayers" method="post" action="?f=guardian_uids">
<?php ArghPanel::begin_tag("Ladder Guardian - Credits"); ?>
<div class="lg-content">
<table border="0" cellpadding="2" cellspacing="0" style="width: 100%; table-layout: fixed;">
		<colgroup><col width="25" /><col width="15" /><col /></colgroup>
		<tr>
			<td colspan="3"><strong>Légende</strong></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="gl_none">&nbsp;</td>
			<td>&nbsp;&nbsp;Aucun ban en cours</td>
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
			echo '<table border="0" cellpadding="2" cellspacing="0">';
			echo '<colgroup><col width="170" /><col width="430" /></colgroup>';
			$req = "
				SELECT DISTINCT T1.uid
				FROM multis_logs AS T1
				LEFT JOIN lg_users AS T2
				ON T1.username = T2.username
				WHERE T2.daily_games = 0 AND T2.is_gold = 0 AND T2.rights_base = 0
				AND year(from_unixtime(T1.last_login)) = ".date('Y')."
				AND month(from_unixtime(T1.last_login)) = ".date('n')."
				AND day(from_unixtime(T1.last_login)) = ".date('j')."
				ORDER BY T1.last_login DESC";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				echo '<tr><td colspan="2">&nbsp;</td></tr>';
				echo '<tr><td colspan="2"><strong>UIDs</strong></td></tr>';
				echo '<tr><td colspan="2">';
				echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%;">';
				$count = 0;
				while ($obj = mysql_fetch_object($res)) {
					$css = ($count++ % 2 == 0) ? " class='alternate'" : "";
					echo '<tr>';
					echo '<td'.$css.'><strong>'.$obj->uid.'</strong>';
					echo '<br /><table border="0" cellpadding="2" cellspacing="0" style="width: 100%; table-layout: fixed;">';
					echo '<colgroup><col width="25" /><col width="150" /><col width="5" /><col /></colgroup>';
					$req = "
						SELECT T1.username, T1.last_login AS 'last_login', 
						CASE WHEN IFNULL(T2.duree, -1) = -1 THEN 'none' ELSE 
							CASE WHEN IFNULL(T2.duree, -1) = 0 THEN 'life' ELSE 
								CASE WHEN (T2.duree * 86400) + T2.quand > ".time()." THEN 'ban' ELSE 'none' END
							END
						END AS 'bantype'
						FROM multis_logs AS T1
						LEFT JOIN lg_ladderbans AS T2
						ON T2.qui = T1.username
						WHERE T1.uid = '".$obj->uid."'
						ORDER BY T1.last_login DESC";
					$ret = mysql_query($req) or die(mysql_error());
					while ($obk = mysql_fetch_object($ret)) {
						echo '<tr>';
						echo '<td>&nbsp;</td>';
						echo '<td>'.date("d/m/Y H:i:s", $obk->last_login).'</td>';
						echo '<td class="gl_'.$obk->bantype.'">&nbsp;</td>';
						echo '<td style="clear: both;">';
						echo '<div style="float: left;">&nbsp;&nbsp;<a href="http://www.dota.fr/ligue/?f=guardian_players&player='.$obk->username.'">'.$obk->username.'</a></div>';
						echo '</td>';
						echo '<tr/>';
					}
					echo '</table>';
					echo '</td></tr>';
				}
				echo '</table>';
				echo '</td></tr>';
			}
			
			echo '</table>';
?>
</div>
<?php ArghPanel::end_tag(ArghPanelMode::NORMAL); ?>
</form>