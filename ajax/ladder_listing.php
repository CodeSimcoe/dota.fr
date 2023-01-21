<?php

	require_once '/home/www/ligue/classes/ArghSession.php';
	ArghSession::begin();
	require_once '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';
	ArghSession::exit_if_not_logged();

	require_once '/home/www/ligue/classes/CacheManager.php';
	require_once '/home/www/ligue/classes/TeamSpeakChannels.php';
	require_once '/home/www/ligue/classes/LadderStates.php';
	require_once '/home/www/ligue/classes/RightsMode.php';
	require_once '/home/www/ligue/classes/LadderManager.php';
	require_once '/home/www/ligue/mysql_connect.php';
	require_once '/home/www/ligue/ladder_functions.php';

	if (getStatus(ArghSession::get_username()) == LadderStates::IN_NORMAL_GAME) {
	}

	// Verif Garena
	if (!ArghSession::is_garena_account_set()) {
		echo '<center>'.Lang::LADDER_MUST_FILL_GARENA_ACCOUNT.'<br /><br /><a href="?f=member">'.Lang::MEMBER_SPACE.'</a></center>';
	} else {
		$version = CacheManager::get_ladder_version();
		$w3_version = CacheManager::get_w3_version();
		echo '<table class="listing">';
		echo '<colgroup><col width="150" /><col /></colgroup>';
		echo '<tbody>';
		echo '<tr><td><strong>'.Lang::VERSION.':</strong></td><td><span class="vip">'.$version.'&nbsp;</strong><a href="http://www.getdota.com"><img src="/ligue/icon_w3g.jpg" alt="" /></a></span></td></tr>';
		echo '<tr><td><strong>'.Lang::W3_VERSION.':</strong></td><td>'.$w3_version.'</td></tr>';
		echo '<tr><td><strong>'.Lang::PLATFORM.':</strong></td><td>Garena -> Tournament -> <span class="win">Argh Room</span> ('.Lang::PASSWORD.' = midas)</td></tr>';
		echo '<tr><td>&nbsp;</td><td>'.sprintf(Lang::LADDER_MINIMUM_LEVEL, 8).'</td></tr>';
		echo '<tr><td><strong>'.Lang::TEAMSPEAK.' 3:</strong></td><td>dota.fr:9988 ('.Lang::PASSWORD.' = argh)</td></tr>';
		echo '<tr><td colspan="2" align="center">&nbsp;</td></tr>';
		echo '<tr><td colspan="2" align="center"><img src="ladder/btn_refresh.jpg" alt="Refresh" style="cursor:pointer;" id="refresh" /></td></tr>';
		echo '</tbody>';
		echo '</table>';
		echo '<br />';
		echo '<hr />';
		echo '<br />';
		echo LadderManager::html_game_info('odd', '', CacheManager::get_ladder_mode_odd(), '');
		echo '<br />';
		echo LadderManager::html_players_table(CacheManager::LADDER_PLAYERLIST_ODD, ArghSession::get_username());
		echo '<br />';
		echo '<hr />';
		echo '<br />';
		echo LadderManager::html_game_info('even', '', CacheManager::get_ladder_mode_even(), '');
		echo '<br />';
		echo LadderManager::html_players_table(CacheManager::LADDER_PLAYERLIST_EVEN, ArghSession::get_username());
	}


?>