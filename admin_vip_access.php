<?php
	
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::VIP_HEADADMIN,
			RightsMode::VIP_ADMIN
		)
	);

	if (isset($_GET['mode'])) {
		$allowed = array('block', 'unblock');
		$mode = strtolower($_GET['mode']);
		if (in_array($mode, $allowed)) {
			$player = trim(mysql_real_escape_string($_GET['player']));
			if ($player != '') {

				$vip_ban = ($mode == 'block') ? 1 : 0;
				$upd = mysql_query("
					UPDATE lg_users SET
						vip_ban = ".$vip_ban.",
						vip_ban_date = ".time()."
					WHERE username = '".$player."'
				") or die(mysql_error());

				$sentence = $_GET['player'].' : VIP '.(($vip_ban == 1) ? Lang::BLOCK : Lang::UNBLOCK);
				$al = new AdminLog($sentence, AdminLog::TYPE_LADDER);
				$al->save_log();

				$notif = new Notification();
				$notif->_destinator = $_GET['player'];
				$notif->_message = ($vip_ban == 1) ? Lang::ADMIN_VIP_NOTIFICATION_BLOCK : Lang::ADMIN_VIP_NOTIFICATION_UNBLOCK;
				$notif->_link = ($vip_ban == 1) ? 'http://www.dota.fr/forum/viewforum.php?f=90' : '?f=laddervip_join';
				$notif->_notif_time = time();
				$notif->save();

			}
		}
	}

	ArghPanel::begin_tag(Lang::ADMIN_VIP_ACCESS);
?>
<form name="frmVIPAccess" method="post" action="?f=admin_vip_access">
	<input type="text" name="tbSearch" id="tbSearch" style="width: 400px;" />
	<input type="submit" value="Search" style="width: 100px;" />
<?php
	if (isset($_POST['tbSearch'])) {
		echo '<br /><br />';
		$search = trim(mysql_real_escape_string($_POST['tbSearch']));
		if ($search != '') {
			$req = "
				SELECT T1.username, T1.ggc  
				FROM lg_users AS T1
				LEFT JOIN lg_laddervip_vouchlist AS T2 ON T1.username = T2.username
				WHERE T2.username IS NULL
				AND (T1.username LIKE '%".$search."%' OR T1.ggc LIKE '%".$search."%')
				AND (T1.is_gold = 1 OR T1.rights > 0)
				AND T1.pts >= 1700
				ORDER BY T1.username";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%">';
				echo '<colgroup><col width="200" /><col width="200" /><col /></colgroup>';
				$count = 0;
				while ($obj = mysql_fetch_object($res)) {
					$css = ($count++ % 2 == 0) ? " class='alternate'" : "";
					echo '<tr>';
					echo '<td'.$css.'><a href="?f=player_profile&player='.$obj->username.'">'.$obj->username.'</a></td>';
					echo '<td'.$css.'>'.$obj->ggc.'</td>';
					echo '<td'.$css.' style="text-align: right;"><a href="?f=admin_vip_access&mode=block&player='.$obj->username.'">'.ucfirst(Lang::BLOCK).'</a></td>';
					echo '</tr>';
				}
				echo '</table>';
			} else {
				echo Lang::NO_PLAYER_MATCH_CRITERIA;
			}
		} else {
			echo Lang::PRECISE_SEARCH_CRITERIA;
		}
	}
?>
</form>
<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::SUSPENDED_PLAYERS);
	$req = "
		SELECT username, ggc, vip_ban_date
		FROM lg_users
		WHERE vip_ban = 1
		ORDER BY username";
	$res = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($res) != 0) {
		echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%">';
		echo '<colgroup><col width="150" /><col width="150" /><col width="150" /><col /></colgroup>';
		$count = 0;
		while ($obj = mysql_fetch_object($res)) {
			$css = ($count++ % 2 == 0) ? " class='alternate'" : "";
			echo '<tr>';
			echo '<td'.$css.'>'.date(Lang::DATE_FORMAT_HOUR, $obj->vip_ban_date).'</td>';
			echo '<td'.$css.'><a href="?f=player_profile&player='.$obj->username.'">'.$obj->username.'</a></td>';
			echo '<td'.$css.'>'.$obj->ggc.'</td>';
			echo '<td'.$css.' style="text-align: right;"><a href="?f=admin_vip_access&mode=unblock&player='.$obj->username.'">'.ucfirst(Lang::UNBLOCK).'</a></td>';
			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo 'Aucun joueur suspendu';
	}
?>
<?php
	ArghPanel::end_tag();
?>
