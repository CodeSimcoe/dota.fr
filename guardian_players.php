
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
<form name="frmLGPlayers" method="post" action="?f=guardian_players">
<?php ArghPanel::begin_tag("Ladder Guardian - Players"); ?>
<div class="lg-content">
	<input type="text" name="tbSearch" id="tbSearch" style="width: 400px;" />
	<input type="submit" value="Search" style="width: 100px;" />
<?php
	if (isset($_POST['tbSearch'])) {
		echo '<br /><br />';
		$search = trim(mysql_real_escape_string($_POST['tbSearch']));
		if ($search != '') {
			$req = "
				SELECT username, ggc, bnet  
				FROM lg_users
				WHERE username LIKE '%".$search."%'
				OR ggc LIKE '%".$search."%'
				OR bnet LIKE '%".$search."%'
				ORDER BY username";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%">';
				echo '<colgroup><col width="200" /><col width="200" /><col /></colgroup>';
				$count = 0;
				while ($obj = mysql_fetch_object($res)) {
					$css = ($count++ % 2 == 0) ? " class='alternate'" : "";
					echo '<tr>';
					echo '<td'.$css.'><a href="?f=guardian_players&player='.$obj->username.'">'.$obj->username.'</a></td>';
					echo '<td'.$css.'>'.$obj->ggc.'</td>';
					echo '<td'.$css.'>'.$obj->bnet.'</td>';
					echo '</tr>';
				}
				echo '</table>';
			} else {
				echo 'Aucun joueur ne correspond à la recherche';
			}
		} else {
			echo 'Précisez un critère de recherche';
		}
	}
?>
</div>
<?php ArghPanel::end_tag(ArghPanelMode::NORMAL); ?>
<?php ArghPanel::begin_tag("Ladder Guardian - Players"); ?>
<div class="lg-content">
	<table border="0" cellpadding="2" cellspacing="0" style="width: 100%; table-layout: fixed;">
		<colgroup><col width="25" /><col width="15" /><col /></colgroup>
		<tr>
			<td colspan="3"><strong>Légende</strong></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="gl_none">&nbsp;</td>
			<td>&nbsp;&nbsp;Aucun ban en cours / OK / Affiché</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="gl_ban">&nbsp;</td>
			<td>&nbsp;&nbsp;Ban en cours</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="gl_life">&nbsp;</td>
			<td>&nbsp;&nbsp;Banlife / PAS OK / Masqué</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;&nbsp;Ladder: Wins / Loses / Lefts / Aways</td>
		</tr>
	</table>
	<!--
	<input type="hidden" name="f" value="guardian_players" />
	<input type="text" name="player" id="player" />
	<input type="submit" value="Search" />
	-->
	<br />
<?php
	if ($player != '') {
		$req = "
			SELECT username, password, joined FROM lg_users WHERE username = '".$player."'";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			$obj = mysql_fetch_object($res);
			$player = $obj->username;
			$md5 = $obj->password;
			echo '<table border="0" cellpadding="2" cellspacing="0">';
			echo '<colgroup><col width="170" /><col width="430" /></colgroup>';
			echo '<tr><td>Player</td><td><a href="http://www.dota.fr/ligue/?f=player_profile&player='.$obj->username.'">'.$obj->username.'</a></td></tr>';
			echo '<tr><td>Inscription</td><td>'.date("d/m/Y H:i:s", $obj->joined).'</td></tr>';

			$req = "
				SELECT UNIX_TIMESTAMP(T1.log_time) AS 'log_time', T1.ip, IFNULL(T2.country, '') AS 'country', IFNULL(T2.city , '') AS 'city', IFNULL(T2.hostname, '') AS 'hostname'
				FROM lg_user_ip AS T1 
				LEFT JOIN multis_ips AS T2
				ON T1.ip = T2.ip 
				WHERE T1.user = '".$player."' AND T1.log_time <> '00000000000000'
				ORDER BY T1.log_time DESC LIMIT 0, 1";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				$obj = mysql_fetch_object($res);
				echo '<tr><td valign="top">Dernière IP</td><td><a href="?f=guardian_players&player='.$player.'&ip='.$obj->ip.'">'.$obj->ip.'</a>';
				if ($obj->country.', '.$obj->city != ', ') {
					echo '<br />'.$obj->country.', '.$obj->city.'<br />'.$obj->hostname;
				}
				echo '</td></tr>';
			}
			
			$req = "
				SELECT 
				 IFNULL(SUM(games), 0) AS 'total', 
				 IFNULL(SUM(wins), 0) AS 'wins', 
				 IFNULL(SUM(loses), 0) AS 'loses', 
				 IFNULL(SUM(lefts), 0) AS 'lefts',
				 IFNULL(SUM(aways), 0) AS 'aways' 
				FROM lg_ladder_stats_players
				WHERE player = '".$player."'
				AND year >= 2009 
				AND ((month = 1 AND day >= 17) OR (month > 1))";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				$obj = mysql_fetch_object($res);
				if ($obj->total > 0) {
					echo '<tr><td>Ladder</td><td><div class="gl_none" style="float: left;">&nbsp;</div>&nbsp;('.$obj->wins.'/'.$obj->loses.'/'.$obj->lefts.'/'.$obj->aways.')</td></tr>';
				} else {
					echo '<tr><td>Ladder</td><td><div class="gl_life">&nbsp;</div></td></tr>';
				}
			} else {
				echo '<tr><td>Ladder</td><td><div class="gl_life">&nbsp;</div></td></tr>';
			}
			
			$req = "
				SELECT 
				CASE WHEN IFNULL(duree, -1) = -1 THEN 'none' ELSE 
					CASE WHEN IFNULL(duree, -1) = 0 THEN 'life' ELSE 
						CASE WHEN (duree * 86400) + quand > ".time()." THEN 'ban' ELSE 'none' END
					END
				END AS 'bantype' 
				FROM lg_ladderbans 
				WHERE qui = '".$player."' 
				ORDER BY bantype DESC LIMIT 0, 1";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				$obj = mysql_fetch_object($res);
				echo '<tr><td>Ban en cours</td><td><div class="gl_'.$obj->bantype.'">&nbsp;</div></td></tr>';
			} else {
				echo '<tr><td>Ban en cours</td><td><div class="gl_none">&nbsp;</div></td></tr>';
			}

			$req = "SELECT COUNT(*) AS 'uidok' FROM multis_logs WHERE username = '".$player."'";
			$res = mysql_query($req) or die(mysql_error());
			$obj = mysql_fetch_object($res);
			echo '<tr><td>UID</td><td><div class="gl_'.($obj->uidok > 0 ? 'none' : 'life').'">&nbsp;</div></td></tr>';

			$req = "SELECT COUNT(*) AS 'banok' FROM lg_ladderbans_follow WHERE username = '".$player."'";
			$res = mysql_query($req) or die(mysql_error());
			$obj = mysql_fetch_object($res);
			echo '<tr><td>Casier</td><td><div class="gl_'.($obj->banok > 0 ? 'life' : 'none').'">&nbsp;</div></td></tr>';
			
			// CASIER JUDICIAIRE
			$req = "
				SELECT motif, admin, quand, `force`, `type`, afficher FROM lg_ladderbans_follow WHERE username = '".$player."' ORDER BY quand DESC";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				echo '<tr><td colspan="2">&nbsp;</td></tr>';
				echo '<tr><td colspan="2"><strong>Casier</strong></td></tr>';
				echo '<tr><td colspan="2">';
				echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%;">';
				echo '<colgroup><col width="5" /><col width="50" /><col /></colgroup>';
				$count = 0;
				while ($obj = mysql_fetch_object($res)) {
					$css = ($count++ % 2 == 0) ? " class='alternate'" : "";
					echo '<tr>';
					echo '<td class="gl_'.($obj->afficher ? 'none' : 'life').'">&nbsp;</td>';
					echo '<td'.$css.' align="center" valign="top">';
					if ($obj->type == 'ban') {
 						if ($obj->force == 0) {
 							echo '<img src="img/infini.gif" alt="indéfini" align="absmiddle" />';
 						} else {
 							echo '<strong>'.$obj->force.'j</strong>';
 						}
 					} else {
 						echo '<img src="img/'.(($obj->force == 4) ? 'red' : $obj->force.'yellow').'card.gif" alt="'.$obj->force.'" align="absmiddle" />';
 					}
					echo '</td>';
					echo '<td'.$css.'>';
					echo date("d/m/Y H:i:s", $obj->quand).' - '.$obj->admin.'<br />'.$obj->motif;
					echo '</td>';
					echo '</tr>';
				}
				echo '</table>';
				echo '</td></tr>';
			}

			// MD5
			$req = "
				SELECT 
					username, 
					bnet, 
					ggc, 
					mail
				FROM lg_users
				WHERE password = '".$md5."'
				ORDER BY username";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				echo '<tr><td colspan="2">&nbsp;</td></tr>';
				echo '<tr><td colspan="2"><strong>MD5</strong></td></tr>';
				echo '<tr><td colspan="2">';
				echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%; font-size: 8pt;">';
				echo '<colgroup><col width="5" /><col width="120" /><col width="120" /><col width="120" /><col /></colgroup>';
				echo '<thead><tr><th></th><th style="text-align: left;">Username</th><th style="text-align: left;">Bnet</th><th style="text-align: left;">GGC</th><th style="text-align: left;">Email</th></tr></thead>';
				$count = 0;
				while ($obj = mysql_fetch_object($res)) {
					$css = ($count++ % 2 == 0) ? " class='alternate'" : "";
					echo '<tr>';
					$breq = "
						SELECT 
						CASE WHEN IFNULL(duree, -1) = -1 THEN 'none' ELSE 
							CASE WHEN IFNULL(duree, -1) = 0 THEN 'life' ELSE 
								CASE WHEN (duree * 86400) + quand > ".time()." THEN 'ban' ELSE 'none' END
							END
						END AS 'bantype' 
						FROM lg_ladderbans 
						WHERE qui = '".$obj->username."' 
						ORDER BY bantype DESC LIMIT 0, 1";
					$bres = mysql_query($breq) or die(mysql_error());
					if (mysql_num_rows($bres) != 0) {
						$bobj = mysql_fetch_object($bres);
						echo '<td class="gl_'.$bobj->bantype.(($count % 2 == 1) ? " alternate" : "").'">&nbsp;</td>';
					} else {
						echo '<td class="gl_none'.(($count % 2 == 1) ? " alternate" : "").'">&nbsp;</td>';
					}
					echo '<td'.$css.' style="font-size: 8pt;" valign="top"><a href="http://www.dota.fr/ligue/?f=guardian_players&player='.$obj->username.'">'.$obj->username.'</a></td>';
					echo '<td'.$css.' style="font-size: 8pt;" valign="top">'.$obj->bnet.'</td>';
					echo '<td'.$css.' style="font-size: 8pt;" valign="top">'.$obj->ggc.'</td>';
					echo '<td'.$css.' style="font-size: 8pt;" valign="top">'.$obj->mail.'</td>';
					echo '</tr>';
				}
				echo '</table>';
				echo '</td></tr>';
			}

			// UIDS
			$req = "
				SELECT distinct uid FROM multis_logs WHERE username = '".$player."' ORDER BY last_login DESC";
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
					echo '<td'.$css.'><strong>'.$obj->uid.'</strong><br />';
					echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%; table-layout: fixed;">';
					echo '<colgroup><col width="25" /><col width="150" /><col width="5" /><col /></colgroup>';
					$req = "
						SELECT T1.username, T1.last_login, 
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
						if ($obk->username != $player) {
							echo '<div style="float: right;"><a href="http://www.dota.fr/ligue/?f=guardian_mix&mix='.$player.'&with='.$obk->username.'">Mix</a></div>';
						}
						echo '</td>';
						echo '<tr/>';
					}
					echo '</table>';
					echo '</td></tr>';
				}
				echo '</table>';
				echo '</td></tr>';
			}
			
			// IPs
			$req = "
				SELECT DISTINCT T1.ip, IFNULL(T2.country, '') AS 'country', IFNULL(T2.city , '') AS 'city', IFNULL(T2.hostname, '') AS 'hostname'
				FROM lg_user_ip AS T1 
				LEFT JOIN multis_ips AS T2
				ON T1.ip = T2.ip 
				WHERE T1.user = '".$player."' AND T1.log_time <> '00000000000000'
				ORDER BY T1.log_time DESC
				LIMIT 0, 10";
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
					echo '<td'.$css.'><strong><a style="float: left;" href="?f=guardian_players&player='.$player.'&ip='.$obj->ip.'">'.$obj->ip.'</a></strong>';
					if ($obj->country.', '.$obj->city != ', ') {
						echo '<span style="float: right; text-align: right;">'.$obj->country.', '.$obj->city.'<br />'.$obj->hostname.'</span>';
					}
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
						if ($obk->user != $player) {
							echo '<div style="float: right;"><a href="http://www.dota.fr/ligue/?f=guardian_mix&mix='.$player.'&with='.$obk->user.'">Mix</a></div>';
						}
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
		} else {
			echo "Ce joueur n'existe pas.";
		}
	}
?>
</div>
<?php ArghPanel::end_tag(ArghPanelMode::NORMAL); ?>
</form>