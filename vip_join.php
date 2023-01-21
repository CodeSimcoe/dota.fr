<?php
	ArghSession::exit_if_not_logged();
	if (!ArghSession::is_vouched()) exit(Lang::LADDERVIP_CANT_PARTICIPATE);

	require_once 'laddervip_functions.php';
	require_once '/home/www/ligue/classes/VipManager.php';
	require_once '/home/www/ligue/classes/VipCache.php';

	echo '<script type="text/javascript" src="/ligue/javascript/vip/vip_join.js"></script>';

	ArghPanel::begin_tag(Lang::LADDER_VIP);

	if (isBanned(ArghSession::get_username())) {
			$req = "SELECT * FROM lg_ladderbans WHERE qui = '".ArghSession::get_username()."'";
			$res = mysql_query($req) or die(mysql_error());
			$obj = mysql_fetch_object($res);
			if (isFinished($obj->quand, $obj->duree)) {
				BanManager::unban($obj->id);
				$al = new AdminLog(sprintf(Lang::ADMIN_LOG_UNBAN_USER, $obj->qui), AdminLog::TYPE_LADDER, 'LadderGuardian');
				$al->save_log();
			} else {
				$remain = remainingTime($obj->quand, $obj->duree);
				echo '<center>'.sprintf(Lang::LADDER_BANNED_ACCOUNT, $obj->par_qui, $obj->raison).'<br />';
				if ($remain != '-') {
					if ($remain == 0)  echo Lang::LADDER_UNBAN_LESS_1_HOUR;
					else echo sprintf(Lang::LADDER_DELAY_UNTIL_UNBAN, $remain);
				}
				echo '</center>';
			}
	} else {
		echo '<table class="simple"><tr><td colspan="2"><div id="ladder10">';
		//VipManager::AddPlayerToCache(ArghSession::get_username(), VipManager::GetPlayerRank(ArghSession::get_username()), ArghSession::get_garena());
		//VipManager::AddPlayerToCache('Chasca', VipManager::GetPlayerRank(ArghSession::get_username()), ArghSession::get_garena());
		$vip = VipCache::load(1234);
		$vip->set_caps('Todo', 'HaRts-');
		$vip->set_players(array(
			'Chasca',
			'ToTo',
			'Kelevra',
			'Amyga-',
			'CoMbaL',
			'PhOeNiiX',
			'XXLpapa',
			'SOYCD'
		));
		$players = VipManager::GetPlayersFromCache();
		foreach ($players AS $player) {
			echo $player[0].'-'.$player[1].'-'.$player[2].'<br/>';
		}
		//VipManager::RemovePlayerFromCache(ArghSession::get_username());
		//VipManager::RemovePlayerFromCache('Chasca');
		echo '</div></td></tr></table>';
	}
	
	ArghPanel::end_tag();

	ArghPanel::begin_tag(Lang::ONLINE_VIP_PLAYERS);
	$req = "
		SELECT o.user, u.ladder_status
		FROM lg_usersonline o, lg_users u
		WHERE o.vip = '1'
		AND u.username = o.user
		ORDER BY o.user ASC";
	$res = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($res) > 0) {
		$players = array();
		while ($obj = mysql_fetch_object($res)) {
			$status = "win";
			if ($obj->ladder_status == 'busy_norm') $status = "lose";
			else if ($obj->ladder_status == 'busy_vip') $status = "vip";
			$players[$obj->user] = array( "user" => $obj->user, "status" => $status );
		}
		$content = file(CacheManager::LADDER_VIP_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (isset($players[$line[0]])) $players[$line[0]]["status"] = "vip";
		}
		$content = file(CacheManager::LADDER_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (isset($players[$line[0]])) $players[$line[0]]["status"] = "lose";
		}
	}
	echo '<table class="simple">';
	echo '<tr><td>'.Lang::LEGEND.'</td></tr>';
	echo '<tr><td class="line">&nbsp;</td></tr>';
	echo '<tr><td><span class="win">'.Lang::AVAILABLE.'</span></td></tr>';
	echo '<tr><td><span class="vip">'.Lang::IN_A_VIP_GAME.'</span></td></tr>';
	echo '<tr><td><span class="lose">'.Lang::IN_A_LADDER_GAME.'</span></td></tr>';
	echo '<tr><td>&nbsp;</td></tr>';
	echo '<tr><td>'.Lang::USERNAME.'</td></tr>';
	echo '<tr><td class="line">&nbsp;</td></tr>';
	foreach ($players as $player) {
		echo '<tr><td><span class="'.$player["status"].'">'.$player["user"].'</span></td></tr>';
	}
	echo '</table>';
	ArghPanel::end_tag();
?>