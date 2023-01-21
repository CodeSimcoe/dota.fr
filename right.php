<?php
	require 'classes/HeroRandomizer.php';
	
	//League Time
	ArghPanel::begin_tag(Lang::MENU_LEAGUE_TIME);
	echo '<center>';
	HeroRandomizer::display_random_picture();
	echo '<br /><img src="img/clock_small.jpg" alt="" />&nbsp;'.date(Lang::DATE_FORMAT_HOUR);
	echo '</center>';
	ArghPanel::end_tag(ArghPanelMode::RIGHT);
	
	
	//Divisions
	ArghPanel::begin_tag(Lang::LEAGUE);
	$divisions = CacheManager::get_division_cache();
	foreach ($divisions as $div) {
		echo '<a href="?f=league_division&div='.$div.'">'.Lang::DIVISION.' '.$div.'</a><br />';
	}
	echo '<br />';
	echo '<center><a href="http://www.gg-game.com"><img src="img/gg.jpg" alt="Garena Partner" /></a></center><br /><br />';
	echo '<a href="?f=league_rules">'.Lang::LEAGUE_RULES.'</a><br />';
	echo '<a href="?f=league_warns">'.Lang::LEAGUE_WARNINGS.'</a><br />';
	echo '<a href="?f=league_stats">'.Lang::LEAGUE_STATISTICS.'</a><br />';
	echo '<a href="?f=league_palmares">'.Lang::LEAGUE_HALL_OF_FAME.'</a><br />';
	if (ArghSession::is_logged()) {
		echo '<a href="?f=league_pronostics">'.Lang::LEAGUE_FORECASTS.'</a><br />';
	}
	ArghPanel::end_tag(ArghPanelMode::RIGHT);
	
	//Administration
	if (ArghSession::is_logged() 
	    && ArghSession::is_rights(
			array(
				RightsMode::WEBMASTER,
				RightsMode::LEAGUE_HEADADMIN,
				RightsMode::LEAGUE_ADMIN,
				RightsMode::LADDER_HEADADMIN,
				RightsMode::GUARDIAN_ADMIN,
				RightsMode::LADDER_ADMIN,
				RightsMode::VIP_HEADADMIN,
				RightsMode::VIP_ADMIN,
				RightsMode::NEWS_HEADADMIN,
				RightsMode::NEWS_NEWSER,
				RightsMode::SHOUTCAST_HEADADMIN,
				RightsMode::SCREENSHOTS_ADMIN,
				RightsMode::SHOUTCAST_SHOUTCASTER
			)
		)) {
		
		ArghPanel::begin_tag(Lang::ADMIN);

		echo '<script type="text/javascript" src="/ligue/javascript/ui.accordion.js"></script>';
		echo '<script type="text/javascript">';
		echo '$(document).ready(function(){';
		echo '	var idx = 0;';
		echo '	$("#right_acc a").each(function() {';
		echo '		if (location.href.toLowerCase().indexOf(this.href.toLowerCase()) != -1) {';
		echo '			idx = $("#right_acc h3").index($(this).parent().prev());';
		echo '		}';
		echo '	});';
		echo '	$("#right_acc").accordion({ active: idx });';
		echo '});';
		echo '</script>';
		echo '<div id="right_acc">';
		if (ArghSession::is_rights(array(RightsMode::NEWS_HEADADMIN, RightsMode::NEWS_NEWSER, RightsMode::SHOUTCAST_HEADADMIN, RightsMode::SHOUTCAST_SHOUTCASTER))) {
			echo '<h3><a href="#">'.Lang::NEWSERS.'</a></h3>';
			echo '<div>';
			if (ArghSession::is_rights(array(RightsMode::NEWS_HEADADMIN, RightsMode::NEWS_NEWSER))) {
				echo '<a href="?f=admin_news_list">'.Lang::NEWS_MODULE.'</a><br />';
				echo '<a href="?f=admin_replay_center">'.Lang::REPLAY_CENTER.'</a><br />';
			}
			if (ArghSession::is_rights(array(RightsMode::SHOUTCAST_HEADADMIN, RightsMode::SHOUTCAST_SHOUTCASTER))) {
				echo '<a href="?f=admin_shoutcast">'.Lang::SHOUTCAST.'</a><br />';
			}
			echo '</div>';
		}
		if (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN))) {
			echo '<h3><a href="#">'.Lang::LEAGUE.'</a></h3>';
			echo '<div>';
			if (ArghSession::is_rights(RightsMode::LEAGUE_HEADADMIN)) {
				echo '<a href="?f=admin_divisions">'.Lang::DIVISIONS.'</a><br />';
				echo '<a href="?f=admin_divisions_cache">'.Lang::DIVISION_CACHE.'</a><br />';
				echo '<a href="?f=admin_rules_edit">'.Lang::RULES_ADMIN.'</a><br />';
			}
			echo '<a href="?f=admin_warns_list">'.Lang::LEAGUE_WARNINGS.'</a><br />';
			echo '</div>';
		}
		if (ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN))) {
			echo '<h3><a href="#">'.Lang::LADDER.'</a></h3>';
			echo '<div>';
			if (ArghSession::is_rights(RightsMode::LADDER_HEADADMIN)) {
				echo '<a href="?f=admin_ladder_version">'.Lang::LADDER_VERSION.'</a><br />';
				echo '<a href="?f=admin_rules_edit">'.Lang::RULES_ADMIN.'</a><br />';
			}
			echo '<a href="?f=admin_ladder_reports">'.Lang::REPORT_GAME_REPORT.'</a><br />';
			//echo '<a href="?f=admin_ladder_reports&vip=true">'.Lang::REPORT_GAME_REPORT.' '.Lang::VIP.'</a><br />';
			echo '<a href="?f=admin_lastregistered">'.Lang::LAST_REGISTERED.'</a><br />';
			echo '<a href="?f=admin_ladder_multiip">'.Lang::MULTIPLE_IP.'</a><br />';
			echo '<a href="?f=admin_ladder_bans">'.Lang::BANLIST.'</a><br />';
			echo '<a href="?f=admin_ladder_topleavers">'.Lang::TOP_LEAVERS.'</a><br />';
			echo '<a href="?f=ladder_rankp_bott">'.Lang::REVERSE_RANKING.'</a><br />';
			echo '</div>';
		}
		if (ArghSession::is_rights(array(RightsMode::VIP_HEADADMIN, RightsMode::VIP_ADMIN))) {
			echo '<h3><a href="#">'.Lang::LADDER_VIP.'</a></h3>';
			echo '<div>';
			echo '<a href="?f=admin_vip_access">'.Lang::ADMIN_VIP_ACCESS.'</a><br />';
			if (ArghSession::is_rights(RightsMode::VIP_HEADADMIN)) {
				echo '<a href="?f=admin_ladder_reports&vip=true">'.Lang::REPORT_GAME_REPORT.' '.Lang::VIP.'</a><br />';
				echo '<a href="?f=admin_rules_edit">'.Lang::RULES_ADMIN.'</a><br />';
				echo '<a href="?f=admin_vip_vouchs_list">'.Lang::VOUCH_LIST.'</a><br />';
				echo '<a href="?f=admin_vip_vouchs_waitinglist">'.Lang::WAITING_PLAYERS.'</a><br />';
			}
			echo '<a href="?f=admin_ladder_bans">'.Lang::BANLIST.'</a><br />';
			echo '<a href="?f=admin_vip_vouchs">'.Lang::VOUCHS_ADMIN.'</a><br />';
			echo '<a href="?f=admin_vip_vouchers">'.Lang::VOUCHERS_ADMIN.'</a><br />';
			echo '</div>';
		}
		if (ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::GUARDIAN_ADMIN))) {
			echo '<h3><a href="#">'.Lang::LADDER_GUARDIAN_ADMIN.'</a></h3>';
			echo '<div>';
			echo '<a href="?f=guardian_credits">'.Lang::CREDITS.'</a><br />';
			echo '<a href="?f=guardian_lastbans">'.Lang::LADDER_GUARDIAN_LAST_BANS.'</a><br />';
			echo '<a href="?f=guardian_players">'.Lang::LADDER_GUARDIAN_PLAYERS.'</a><br />';
			echo '<a href="?f=guardian_uids">'.Lang::LADDER_GUARDIAN_UIDS.'</a><br />';
			echo '<a href="?f=guardian_ips">'.Lang::LADDER_GUARDIAN_IPS.'</a><br />';
			//echo '<a href="?f=guardian_proxys">'.Lang::LADDER_GUARDIAN_PROXYS.'</a><br />';
			echo '<a href="?f=guardian_connects">'.Lang::LADDER_GUARDIAN_CONNECTS.'</a><br />';
			echo '</div>';
		}
		if (ArghSession::is_rights(array(RightsMode::SCREENSHOTS_ADMIN))) {
			echo '<h3><a href="#">'.Lang::SCREENSHOTS.'</a></h3>';
			echo '<div>';
			echo '<a href="?f=screenshots_pending">'.Lang::SCREENSHOTS_PENDING.'</a><br />';
			echo '</div>';
		}
		if (ArghSession::is_rights(RightsMode::WEBMASTER)) {
			echo '<h3><a href="#">'.Lang::ADMIN.'</a></h3>';
			echo '<div>';
			echo '<a href="?f=admin_golds">'.Lang::ADMIN_GOLD_ACCOUNTS.'</a><br />';
			echo '<a href="?f=admin_log">'.Lang::ADMIN_LOG.'</a><br />';
			echo '<a href="?f=admin_banners">'.Lang::BANNERS.'</a><br />';
			echo '<a href="?f=admin_herodatabase">'.Lang::HERO_DATABASE.'</a><br />';
			echo '<a href="?f=admin_teams">'.Lang::TEAMS.'</a><br />';
			echo '<a href="?f=admin_players">'.Lang::USERS.'</a><br />';
			echo '<a href="?f=admin_rights">'.Lang::RIGHTS.'</a><br />';
			echo '<a href="?f=admin_changenick">'.Lang::ADMIN_USERNAME_CHANGES.'</a><br />';
			echo '</div>';
			echo '<h3><a href="#">'.Lang::PARSER.'</a></h3>';
			echo '<div>';
			echo '<a href="?f=admin_parser_definitions">'.Lang::PARSER_DEFINITIONS.'</a><br />';
			echo '</div>';
		}
		echo '</div>';

		ArghPanel::end_tag(ArghPanelMode::RIGHT);

	}

?>