<?php
	ArghPanel::begin_tag(ArghSession::is_logged() ? Lang::PROFILE : Lang::LOGIN);
	echo '<form method="POST" action="login_validate.php">';
	if (ArghSession::is_logged()) {
		ArghSession::set_gold_and_xp();
		echo '<img src="'.ArghSession::get_clan_rank().'.gif" alt="" /><a href="?f=player_profile&amp;player='.ArghSession::get_username().'">'.ArghSession::get_username().'</a><br />';
		echo '<img src="img/gold.gif" alt="" /> <span class="vip"><b>'.ArghSession::get_gold().'</b></span><br />';
		echo '<img src="img/xp.gif" alt="" /> <b>'.ArghSession::get_xp().'</b><br />';
		if (ArghSession::is_vouched()) {
			echo '<img src="img/xp_vip.gif"> <b>'.ArghSession::get_xp_vip().'</b></span><br />';
		}
		
		if (ArghSession::is_gold()) {
			echo '<span class="vip"><b>'.Lang::GOLD_ACCOUNT.'</b></span><br />';
			echo '<a href="?f=changenick">'.Lang::USERNAME_CHANGE.'</a><br />';
		} else {
			echo '<b>'.Lang::BASIC_ACCOUNT.' (<a href="?f=buy_gold">?</a>)</b><br />';
		}
		
		//Credits
		ArghSession::load_credits();
		if (!ArghSession::is_gold()) {
			echo '<b>'.Lang::CREDITS.': '.ArghSession::get_daily_credits().' (<a href="?f=buy_gold">?</a>)</b><br />';
		}
		
		if (ArghSession::get_clan() != 0) {
			echo '<a href="?f=clan_home">'.Lang::TEAM_HOME.'</a><br />';
		}
		
		echo '<a href="?f=member">'.Lang::MEMBER_SPACE.'</a><br />';
		
		//Friendlist
		if (ArghSession::is_gold()) {
			echo '<a href="?f=friendlist">'.Lang::FRIENDLIST.'</a><br />';
		}
		
		//Notifications
		require 'classes/NotificationManager.php';
		
		if (ArghSession::is_gold()) {
			$notifications_sql = NotificationManager::get_user_notifications(ArghSession::get_username(), true);
			if (mysql_num_rows($notifications_sql)) {
				echo '<a href="?f=notifications">'.Lang::NOTIFICATIONS.'&nbsp;<img src="img/icons/email.png" alt="" /></a><br />';
			} else {
				echo '<a href="?f=notifications">'.Lang::NOTIFICATIONS.'</a><br />';
			}
		}
		
		
		echo '<a href="logout.php">'.Lang::LOG_OUT.'</a><br />';
	} else {
		echo '<input type="hidden" name="url" value="'.selfURL().'" />';
?>
	<table>
		<colgroup>
			<col width="35%" />
			<col width="65%" />
		</colgroup>
		<tr>
			<td><?php echo Lang::USER_FIELD; ?></td>
			<td><input type="text" size="12" name="username" /></td>
		</tr>
		<tr>
			<td><?php echo Lang::PASSWORD_FIELD; ?></td>
			<td><input type="password" size="12" name="password" /></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="checkbox" name="autolog" /> <?php echo Lang::REMEMBER_ME; ?></td>
		</tr>
	</table>
	<br />
	<center>
		<input type="submit" value="<?php echo Lang::LOG_IN; ?>" /><br />
		<a href="?f=pass_recovery"><?php echo Lang::FORGOTTEN_PASSWORD; ?></a>
	</center>
<?php
	}
	echo '</form>';
	ArghPanel::end_tag(ArghPanelMode::LEFT);
	
	//General
	ArghPanel::begin_tag(Lang::MENU);
	echo '<a href="?f=main">'.Lang::MENU_HOME.'</a><br />
	<a href="?f=news_list">'.Lang::MENU_NEWS_ARCHIVES.'</a><br />
	<a href="?f=register">'.Lang::MENU_REGISTRATION.'</a><br />
	<a href="?f=staff">'.Lang::MENU_STAFF.'</a><br />
	<a href="/forum/index.php" target="_blank">'.Lang::MENU_FORUM.'</a><br />
	<a href="?f=sponsors">'.Lang::MENU_SPONSORS.'</a><br />
	<br />
	<a href="?f=players_list">'.Lang::MENU_PLAYERS.'</a><br />
	<a href="?f=teams_list&mode=name">'.Lang::MENU_TEAMS.'</a><br />
	<br />
	<a href="?f=screenshots">'.Lang::SCREENSHOTS.'</a><br />
	<a href="?f=screenshots_upload">'.Lang::SCREENSHOTS_UPLOAD.'</a><br />';
	ArghPanel::end_tag(ArghPanelMode::LEFT);
	
	//Ladder
	ArghPanel::begin_tag(Lang::LADDER);
	echo '<a href="?f=ladder_rules">'.Lang::LADDER_RULES.'</a><br />';
	echo '<a href="?f=laddervip_rules">'.Lang::LADDERVIP_RULES.'</a><br />';
	if (ArghSession::is_gold()) {
		echo '<a href="?f=ladder_stats">'.Lang::LADDER_STATS.'</a><br />';
	}
	echo '<a href="?f=ladder_rankp">'.Lang::LADDER_PLAYER_RANKING.'</a><br />
	<a href="?f=ladder_rankc">'.Lang::LADDER_TEAM_RANKING.'</a><br />';
	//<a href="?f=laddervip_stats">'.Lang::LADDERVIP_STATS.'</a><br />
	echo '<a href="?f=laddervip_rank">'.Lang::LADDERVIP_RANK.'</a><br />
	<br />';
	if (ArghSession::is_logged()) {
		echo '<a href="?f=ladder_join">'.Lang::LADDER_JOIN.'</a><br />
		<a href="?f=ladder_qg">'.Lang::LADDER_HQ.'</a><br />';
	}
	echo '<a href="?f=ladder_current">'.Lang::LADDER_RUNNING_GAMES.'</a><br />';
	
	/*
	//Google Ad
	if (ArghSession::display_google_ad()) {
		echo '<br />';
		include 'google/ad_120_90.html';
		echo '<br />';
	}*/

	echo '<br />';
	
	//Ladder VIP
	if (ArghSession::is_vouched()) {
		echo '<a href="?f=laddervip_join">'.Lang::LADDERVIP_JOIN.'</a><br />';
	}
	echo '<a href="?f=laddervip_qg">'.Lang::LADDERVIP_HQ.'</a><br />';
	echo '<a href="?f=laddervip_current">'.Lang::LADDERVIP_RUNNING_GAMES.'</a><br /><br />';
	
	//Alliés / Adversaires
	/*
	echo '<a href="?f=ladder_allies">'.Lang::LADDER_ALLIES.'</a><br />
	<a href="?f=ladder_ennemies">'.Lang::LADDER_OPPONENTS.'</a><br /><br />';
	*/
	
	//Paypal
	//include 'paypal/donation.html';
	ArghPanel::end_tag(ArghPanelMode::LEFT);
?>
