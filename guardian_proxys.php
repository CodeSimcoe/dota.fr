
<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::GUARDIAN_ADMIN
		)
	);

	require_once 'classes/BanManager.php';

	$player = '';
	if (isset($_GET['player'])) {
		$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
	}
	$mode = '';
	if (isset($_GET['mode'])) {
		$mode = mysql_real_escape_string(substr($_GET['mode'], 0, 25));
	}
	if ($mode == ban && $player != '') {
		$req = "SELECT MAX(kmh) AS 'kmh' FROM lg_info_ip WHERE username = '".mysql_real_escape_string($player)."'";
		$res = mysql_query($req) or die(mysql_error());
		$obj = mysql_fetch_object($res);
		$req = "SELECT * FROM lg_info_ip WHERE username = '".mysql_real_escape_string($player)."' AND kmh = ".$obj->kmh;
		$rto = mysql_query($req) or die(mysql_error());
		$oto = mysql_fetch_object($rto);
		$req = "SELECT * FROM lg_info_ip WHERE username = '".mysql_real_escape_string($player)."' AND last_login < ".$oto->last_login." LIMIT 0, 1";
		$rfr = mysql_query($req) or die(mysql_error());
		$ofr = mysql_fetch_object($rfr);
		$msg = htmlentities($ofr->country).($ofr->city != '' ? ', ' : '').htmlentities($ofr->city).' => '.htmlentities($oto->country).($oto->city != '' ? ', ' : '').htmlentities($oto->city).' = '.$obj->kmh.' km/h. L\'abus de proxys est dangereux pour la santé.';
		BanManager::ban($player, 0, $msg, 'LadderGuardian');
		$log = new AdminLog('Ban proxy: '.$player, 2, 'LadderGuardian');
		$log->save_log();
	}

?>
<link type="text/css" rel="stylesheet" href="guardian.css" />
<?php ArghPanel::begin_tag("Ladder Guardian - Proxys"); ?>
<div class="lg-content">
<?php
	$req = "
		SELECT A.kmh, A.username
		FROM (
			SELECT MAX(kmh) AS 'kmh', username 
			FROM lg_info_ip
			GROUP BY username
			HAVING MAX(kmh) >= 400
		) A
		LEFT JOIN lg_ladderbans AS B
		ON B.qui = A.username
		WHERE IFNULL(B.duree, -1) <> 0
		ORDER BY A.kmh DESC";
	$res = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($res) != 0) {
		echo '<table border="0" cellpadding="2" cellspacing="0" align="center">';
		echo '<colgroup><col width="170" /><col width="430" /></colgroup>';
		while ($obj = mysql_fetch_object($res)) {
			$css = ($count++ % 2 == 0) ? " class='alternate'" : "";
			$req = "SELECT * FROM lg_info_ip WHERE username = '".mysql_real_escape_string($obj->username)."' AND kmh = ".$obj->kmh;
			$rto = mysql_query($req) or die(mysql_error());
			$oto = mysql_fetch_object($rto);
			$req = "SELECT * FROM lg_info_ip WHERE username = '".mysql_real_escape_string($obj->username)."' AND last_login < ".$oto->last_login." LIMIT 0, 1";
			$rfr = mysql_query($req) or die(mysql_error());
			$ofr = mysql_fetch_object($rfr);
			echo '<tr>';
			echo '<td'.$css.' valign="top" colspan="2">';
			echo '<span style="float: right"><a href="http://www.dota.fr/ligue/?f=guardian_proxys&player='.$obj->username.'&mode=ban" onclick="return confirm(\'Voulez vous banlife cet utilisateur ?\');">'.htmlentities($ofr->country).($ofr->city != '' ? ', ' : '').htmlentities($ofr->city).' => '.htmlentities($oto->country).($oto->city != '' ? ', ' : '').htmlentities($oto->city).' = '.$obj->kmh.' km/h</a></span>';
			echo '<a href="http://www.dota.fr/ligue/?f=guardian_proxys&player='.$obj->username.'">'.$obj->username.'</a></td>';
			echo '</tr>';
			if ($player == $obj->username) {
				$req = "
					SELECT * 
					FROM lg_info_ip
					WHERE username = '".mysql_real_escape_string($obj->username)."'
					ORDER BY last_login DESC";
				$list = mysql_query($req) or die(mysql_error());
				if (mysql_num_rows($list) != 0) {
					while ($row = mysql_fetch_object($list)) {
						echo '<tr>';
						echo '<td'.$css.' align="center" valign="top">'.date("d/m/Y H:i:s", $row->last_login).'</td>';
						echo '<td'.$css.' valign="top">'.$row->km.' km en '.$row->h.' h => '.$row->kmh.' km/h<br/>'.htmlentities($row->country).($row->city != '' ? ', ' : '').htmlentities($row->city).'</td>';
						echo '</tr>';
					}
				}
			}
		}
		echo '</table>';
	}
?>
</div>
<?php ArghPanel::end_tag(ArghPanelMode::NORMAL); ?>
