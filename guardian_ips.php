
<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::GUARDIAN_ADMIN
		)
	);

	function notag($txt) {
		$pattern = "<[^>]+>";
		return(ereg_replace($pattern,"",$txt));
	}
	$player = '';
	if (isset($_GET['player'])) {
		$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
	}
	$ip = '';
	if (isset($_GET['ip'])) {
		$ip = $_GET['ip'];
		$file = file_get_contents("http://www.geoiptool.com/fr/?IP=".$ip, FILE_TEXT);
		$file = notag($file);
		$file = ereg_replace("\t", "", $file);
		$file = ereg_replace("\r", "", $file);
		$file = ereg_replace(" {2,}", " ", $file);
		$file = ereg_replace("^ (.+)", "\1", $file);
		$file = ereg_replace("\n ", "\n", $file);
		while (ereg("\n\n", $file)) $file = ereg_replace("\n\n", "\n", $file);
		if (ereg("Adresse IP:\n[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\nPays:\n([^\n]+)\n", $file, $values)) {
			$country = trim($values[1]);
		} else {
			$country = "Unknown";
		}
		if (ereg("Ville:\n([^\n]+)\n", $file, $values)) {
			if ($values[1] == 'Code postal:') {
				$city = "Unknown";
			} else {
				$city = trim($values[1]);
			}
		} else {
			$city = "Unknown";
		}
		$host = gethostbyaddr($ip);
		$result = 'Unknown';
		if ($host != null)
		{
			if ($host != '' && $host != '.')
			{
				if ($host != $obj->ip)
				{
					$result = $host;
				}
			}
		}
		$req = mysql_query("
			DELETE FROM multis_ips WHERE ip = '".$ip."'
		") or die(mysql_error());
		$req = mysql_query("
			INSERT INTO multis_ips (ip, country, city, hostname)
			VALUES ('".$ip."', '".mysql_real_escape_string($country)."', '".mysql_real_escape_string($city)."', '".mysql_real_escape_string($result)."')
		") or die(mysql_error());
	}
?>
<link type="text/css" rel="stylesheet" href="guardian.css" />
<form name="frmLGPlayers" method="post" action="?f=guardian_ips">
<?php ArghPanel::begin_tag("Ladder Guardian - IPs"); ?>
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
				SELECT DISTINCT T1.ip
				FROM lg_user_ip AS T1
				WHERE T1.log_time <> '00000000000000'
				ORDER BY T1.log_time DESC
				LIMIT 0, 50";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				echo '<tr><td colspan="2">&nbsp;</td></tr>';
				echo '<tr><td colspan="2"><strong>IPs</strong></td></tr>';
				echo '<tr><td colspan="2">';
				echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%;">';
				$count = 0;
				while ($obj = mysql_fetch_object($res)) {
					$css = ($count++ % 2 == 0) ? " class='alternate'" : "";
					echo '<tr>';
					echo '<td'.$css.'><strong><a style="float: left;" href="?f=guardian_ips&ip='.$obj->ip.'">'.$obj->ip.'</a></strong>';
					$host = gethostbyaddr($obj->ip);
					$result = 'Unknown';
					if ($host != null)
					{
						if ($host != '' && $host != '.')
						{
							if ($host != $obj->ip)
							{
								$result = $host;
							}
						}
					}
					echo '<span style="float: right; text-align: right;">'.$result.'</span>';
					echo '<br style="clear: both;" /><table border="0" cellpadding="2" cellspacing="0" style="width: 100%; table-layout: fixed;">';
					echo '<colgroup><col width="25" /><col width="150" /><col width="5" /><col /></colgroup>';
					$req = "
						SELECT T1.user, UNIX_TIMESTAMP(T1.log_time) AS 'last_login', 
						CASE WHEN IFNULL(T2.duree, -1) = -1 THEN 'none' ELSE 
							CASE WHEN IFNULL(T2.duree, -1) = 0 THEN 'life' ELSE 
								CASE WHEN (T2.duree * 86400) + T2.quand > ".time()." THEN 'ban' ELSE 'none' END
							END
						END AS 'bantype'
						FROM lg_user_ip AS T1
						LEFT JOIN lg_ladderbans AS T2
						ON T2.qui = T1.user
						WHERE T1.ip = '".$obj->ip."'
						AND T1.log_time <> '00000000000000'
						ORDER BY T1.log_time DESC";
					$ret = mysql_query($req) or die(mysql_error());
					while ($obk = mysql_fetch_object($ret)) {
						echo '<tr>';
						echo '<td>&nbsp;</td>';
						echo '<td>'.date("d/m/Y H:i:s", $obk->last_login).'</td>';
						echo '<td class="gl_'.$obk->bantype.'">&nbsp;</td>';
						echo '<td style="clear: both;">';
						echo '<div style="float: left;">&nbsp;&nbsp;<a href="http://www.dota.fr/ligue/?f=guardian_players&player='.$obk->user.'">'.$obk->user.'</a></div>';
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