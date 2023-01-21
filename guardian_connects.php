
<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::GUARDIAN_ADMIN
		)
	);

?>
<link type="text/css" rel="stylesheet" href="guardian.css" />
<form name="frmLGPlayers" method="post" action="?f=guardian_connects">
<?php ArghPanel::begin_tag("Ladder Guardian - Connects"); ?>
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
			$res = mysql_query("TRUNCATE TABLE multis_ranks") or die(mysql_error());
			$res = mysql_query("TRUNCATE TABLE multis_connects") or die(mysql_error());
			$res = mysql_query("
				INSERT INTO multis_ranks (uid, last_login)
				SELECT DISTINCT A.uid, A.last_login
				FROM multis_logs A
				INNER JOIN (
				  SELECT DISTINCT uid
				  FROM multis_logs
				  GROUP BY uid
				  HAVING COUNT(username) > 1
				) B
				ON A.uid = B.uid") or die(mysql_error());
			$res = mysql_query("
				INSERT INTO multis_connects (uid, last_login, rank)
				SELECT A.uid, A.last_login, COUNT(*)
				FROM multis_ranks A
				JOIN multis_ranks B ON A.uid = B.uid AND A.last_login <= B.last_login
				GROUP BY A.uid, A.last_login
				HAVING COUNT(*) < 3
			") or die(mysql_error());
			$req = "
				SELECT DISTINCT uid
				FROM multis_connects
				GROUP BY uid
				HAVING MAX(last_login) - MIN(last_login) <= (3600 * 4)
				ORDER BY MAX(last_login) DESC
				LIMIT 0, 50";
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