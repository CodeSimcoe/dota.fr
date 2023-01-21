<?php
	require '/home/www/ligue/mysql_connect.php';
	require '/home/www/ligue/misc.php';
	require '/home/www/ligue/classes/AdminLog.php';
	require '/home/www/ligue/classes/CacheManager.php';
	require '/home/www/ligue/classes/Mail.php';

	//REFUSED
	$query = "DELETE FROM lg_pending_nick_changes WHERE validated = 2";
	$result = mysql_query($query);
	
	//ACCEPTED
	$query = "SELECT * FROM lg_pending_nick_changes WHERE changed = 0 AND validated = 1";
	$result = mysql_query($query);
	
	while ($sql = mysql_fetch_object($result)) {
		//echo $sql->old_username.' - '.$sql->new_username.'<br />';

		$old_user = $sql->old_username;
		$new_user = $sql->new_username;
		
		$pass = false;
		
		//Normal
		$content = file(CacheManager::LADDER_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (count($line) == 5 && $line[0] == $old_user) {
				$pass = true;
				break; //break foreach
			}
		}

		//VIP
		$content = file(CacheManager::LADDER_VIP_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (count($line) == 4 && $line[0] == $old_user) {
				$pass = true;
				break; //break foreach
			}
		}
		
		if ($pass) continue; //continue to next iteration

		$tables_array = array(
			array ('lg_activation', 'username'),
			array ('lg_adminlog', 'qui'),
			array ('lg_banners', 'author'),
			array ('lg_comment', 'poster'),
			array ('lg_divisions', 'admin'),
			array ('lg_friendlist', 'username'),
			array ('lg_friendlist', 'friend'),
			array ('lg_ladderadmins', 'user'),
			array ('lg_ladderbans', 'qui'),
			array ('lg_ladderbans', 'par_qui'),
			array ('lg_ladderbans_follow', 'username'),
			array ('lg_ladderbans_follow', 'admin'),
			array ('lg_laddercomment', 'qui'),
			array ('lg_ladderfollow', 'player'),
			array ('lg_laddergames', 'p1'),
			array ('lg_laddergames', 'p2'),
			array ('lg_laddergames', 'p3'),
			array ('lg_laddergames', 'p4'),
			array ('lg_laddergames', 'p5'),
			array ('lg_laddergames', 'p6'),
			array ('lg_laddergames', 'p7'),
			array ('lg_laddergames', 'p8'),
			array ('lg_laddergames', 'p9'),
			array ('lg_laddergames', 'p10'),
			array ('lg_laddervip_admins', 'user'),
			array ('lg_laddervip_follow', 'player'),
			array ('lg_laddervip_games', 'cap1'),
			array ('lg_laddervip_games', 'cap2'),
			array ('lg_laddervip_games', 'p1'),
			array ('lg_laddervip_games', 'p2'),
			array ('lg_laddervip_games', 'p3'),
			array ('lg_laddervip_games', 'p4'),
			array ('lg_laddervip_games', 'p5'),
			array ('lg_laddervip_games', 'p6'),
			array ('lg_laddervip_games', 'p7'),
			array ('lg_laddervip_games', 'p8'),
			array ('lg_laddervip_games', 'pp1'),
			array ('lg_laddervip_games', 'pp2'),
			array ('lg_laddervip_games', 'pp3'),
			array ('lg_laddervip_games', 'pp4'),
			array ('lg_laddervip_games', 'pp5'),
			array ('lg_laddervip_games', 'pp6'),
			array ('lg_laddervip_games', 'pp7'),
			array ('lg_laddervip_games', 'pp8'),
			array ('lg_laddervip_players', 'username'),
			array ('lg_laddervip_playersreports', 'qui'),
			array ('lg_laddervip_playersreports', 'pour_qui'),
			array ('lg_laddervip_vouchlist', 'username'),
			array ('lg_laddervip_winnersreports', 'qui'),
			array ('lg_ladder_stats_players', 'player'),
			array ('lg_ladder_stats_ranks', 'player'),
			array ('lg_ladder_stats_reports', 'player'),
			array ('lg_ladder_stats', 'username'),
			array ('lg_laddervip_stats', 'username'),
			array ('lg_matchs', 'qui_propose'),
			array ('lg_matchs', 'qui_accepte'),
			array ('lg_matchs', 'p1'),
			array ('lg_matchs', 'p2'),
			array ('lg_matchs', 'p3'),
			array ('lg_matchs', 'p4'),
			array ('lg_matchs', 'p5'),
			array ('lg_matchs', 'p6'),
			array ('lg_matchs', 'p7'),
			array ('lg_matchs', 'p8'),
			array ('lg_matchs', 'p9'),
			array ('lg_matchs', 'p10'),
			array ('lg_matchs', 'p1r2'),
			array ('lg_matchs', 'p2r2'),
			array ('lg_matchs', 'p3r2'),
			array ('lg_matchs', 'p4r2'),
			array ('lg_matchs', 'p5r2'),
			array ('lg_matchs', 'p6r2'),
			array ('lg_matchs', 'p7r2'),
			array ('lg_matchs', 'p8r2'),
			array ('lg_matchs', 'p9r2'),
			array ('lg_matchs', 'p10r2'),
			array ('lg_newsmod', 'poster'),
			array ('lg_notifications', 'destinator'),
			array ('lg_paris', 'qui_vote'),
			array ('lg_passrecovery', 'user'),
			array ('lg_playersreports', 'qui'),
			array ('lg_playersreports', 'pour_qui'),
			array ('lg_ranks', 'qui_vote'),
			array ('lg_ranks', 'pour_qui'),
			array ('lg_replaycenter', 'qui_upload'),
			array ('lg_reports_messages', 'author'),
			array ('lg_reports_messages_vip', 'author'),
			array ('lg_rules', 'author'),
			array ('lg_screencenter', 'qui_upload'),
			array ('lg_shoutcast', 'poster'),
			array ('lg_text', 'poster'),
			array ('lg_uploads', 'qui_upload'),
			array ('lg_users', 'username'),
			array ('lg_usersonline', 'user'),
			array ('lg_user_ip', ' user'),
			array ('lg_info_ip', 'username'),
			array ('lg_vouchs', 'qui'),
			array ('lg_vouchs', 'voucher'),
			array ('lg_warns', 'qui_warn'),
			array ('lg_winnersreports', 'qui'),
			array ('multis_autobans', 'who'),
			array ('multis_logs', 'username'),
			array ('multis_tmp', 'username'),
			array ('multis_uids', 'user_who'),
			array ('multis_uids', 'user_with'),
			array ('multis_users', 'username'),
			array ('rc_replays', 'posted_by'),
			array ('lg_reports', 'initiator'),
			array ('lg_reports', 'admin'),
			array ('lg_reports_vip', 'admin'),
			array ('lg_reports_vip', 'admin'),
			array ('lg_players_availabilities', 'username'),
		);
		
		foreach($tables_array as $info) {
			$table = $info[0];
			$field = $info[1];
			$query = "UPDATE `".$table."` SET ".$field." = '".$new_user."' WHERE ".$field." = '".$old_user."'";
			mysql_query($query)/* or die(mysql_error())*/;
		}
		
		//Cas à part
		$query = "SELECT game_id, concerned_players FROM lg_reports";
		$result_ = mysql_query($query);
		while ($l = mysql_fetch_row($result_)) {
			$player_array = explode(';', $l[1]);
			for ($i = 0; $i < count($player_array); $i++) {
				if ($old_user == $player_array[$i]) {
					$player_array[$i] = $new_user;
					break; //break for
				}
			}
			$players_str = implode(';', $player_array);
			$query = "UPDATE lg_reports SET concerned_players = '".$players_str."' WHERE game_id = '".$l[0]."'";
			mysql_query($query) or die(mysql_error());
		}
		
		$query = "UPDATE lg_pending_nick_changes SET changed = 1 WHERE old_username = '".$old_user."' AND new_username = '".$new_user."'";
		mysql_query($query);
		
		$query = "UPDATE lg_users SET last_rename = '".time()."' WHERE username = '".$new_user."'";
		mysql_query($query);
		
		$message = 'Bonjour, suite a une demande de votre part, voici votre nouveau login pour vous connecter sur www.dota.fr : '.$new_user;
		$message .= "\n\n\n";
		$message .= 'Hello, here is your new login on www.dota.fr : '.$new_user;
		
		$address = get_user_mail($new_user);
		if (!empty($address)) {
			$mail = new Mail(array($address), 'www.dota.fr', $message);
			$mail->send();
		}
		
		$al = new AdminLog('Username change : '.$old_user.' => '.$new_user, AdminLog::TYPE_ROUTINES, 'LadderGuardian');
		$al->save_log();
	}
?>