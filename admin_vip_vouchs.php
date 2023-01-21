<?php
	
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::VIP_HEADADMIN,
			RightsMode::VIP_ADMIN
		)
	);

	ArghPanel::begin_tag(Lang::PLAYER_VOUCH);

?>
<table class="simple">
	<form method="POST" action="?f=admin_vip_vouchs">
	<tr><td><select name="player">
<?php
	$req = "SELECT username, ggc
			FROM lg_users
			WHERE ggc != ''
			ORDER BY username ASC";
	$t = mysql_query($req);
	while ($l = mysql_fetch_row($t)) {
		echo '<option value="'.$l[0].'">'.$l[0].' | '.$l[1].'</option>';
	}
?>
	</select> <select name="rank">
<?php
	for ($i = 1; $i <= 3; $i++) {
		echo '<option value="'.$i.'">'.$i.'</option>';
	}
?>
	</select> <input type="submit" value="<?php echo ucfirst(Lang::VOUCH); ?>" name="sent" /></td></tr>
	</form>
<?php
	if (isset($_POST['sent'])) {
		mysql_query("DELETE FROM lg_laddervip_vouchlist WHERE username = '".mysql_real_escape_string($_POST['player'])."'");
		mysql_query("INSERT INTO lg_laddervip_vouchlist (username, rank) VALUES ('".mysql_real_escape_string($_POST['player'])."', '".(int)$_POST['rank']."')");
		
		$sentence = sprintf(Lang::ADMIN_LOG_PLAYER_VOUCHED, $_POST['player'], $_POST['rank']);
		$al = new AdminLog($sentence, AdminLog::TYPE_LADDER);
		$al->save_log();
		
		$notif = new Notification();
		$notif->_destinator = $_POST['player'];
		$notif->_message = 'Félicitations, vous avez été vouch sur le VIP. Caplevel '.(int)$_POST['rank'];
		$notif->_link = '?f=laddervip_join';
		$notif->_notif_time = time();
		$notif->save();
		
		echo '<tr><td><span class="win">'.$sentence.'</span></td></tr>';
	}
?>
</table>

<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::PLAYER_UNVOUCH);
?>

<table class="simple">
	<form method="POST" action="?f=admin_vip_vouchs">
	<tr><td><select name="player">
<?php
	$req = 'SELECT username FROM lg_laddervip_vouchlist ORDER BY username ASC';

	$t = mysql_query($req);
	while ($l = mysql_fetch_row($t)) {
		echo '<option value="'.$l[0].'">'.$l[0].'</option>';
	}
?>
	</select> <input type="submit" value="<?php echo ucfirst(Lang::UNVOUCH); ?>" name="sent2" /></td></tr>
	</form>
<?php
	if (isset($_POST['sent2'])) {
		mysql_query("DELETE FROM lg_laddervip_vouchlist WHERE username = '".mysql_real_escape_string($_POST['player'])."'");
		mysql_query("DELETE FROM lg_vouchs WHERE qui = '".mysql_real_escape_string($_POST['player'])."'");
		
		$sentence = sprintf(Lang::ADMIN_LOG_PLAYER_UNVOUCHED, $_POST['player']);
		
		$al = new AdminLog($sentence, AdminLog::TYPE_LADDER);
		$al->save_log();
		
		$notif = new Notification();
		$notif->_destinator = $_POST['player'];
		$notif->_message = 'Vous avez été unvouch du VIP.';
		$notif->_link = 'http://www.dota.fr/forum/viewforum.php?f=59';
		$notif->_notif_time = time();
		$notif->save();
		
		echo '<tr><td><span class="win">'.$sentence.'</span></td></tr>';
	}
?>
	</table>
<?php
	ArghPanel::end_tag();
?>