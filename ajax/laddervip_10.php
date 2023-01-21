<?php
	require_once '/home/www/ligue/classes/ArghSession.php';
	ArghSession::begin();
	require_once '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';
	
	ArghSession::exit_if_not_logged();
	if (!ArghSession::is_vouched()) exit;

	require_once '/home/www/ligue/classes/CacheManager.php';
	require_once '/home/www/ligue/classes/LadderStates.php';
	require_once '/home/www/ligue/classes/TeamSpeakChannels.php';
	require_once '/home/www/ligue/mysql_connect.php';
	require_once '/home/www/ligue/laddervip_functions.php';
	
	$infos = explode('#', getNextGameInfos());
	
	if (getStatus(ArghSession::get_username()) == LadderStates::IN_VIP_GAME) {
	
		//Déjà dans une game
		$req = "SELECT id
				FROM lg_laddervip_games
				WHERE (
					p1 = '".ArghSession::get_username()."'
					OR p2 = '".ArghSession::get_username()."'
					OR p3 = '".ArghSession::get_username()."'
					OR p4 = '".ArghSession::get_username()."'
					OR p5 = '".ArghSession::get_username()."'
					OR p6 = '".ArghSession::get_username()."'
					OR p7 = '".ArghSession::get_username()."'
					OR p8 = '".ArghSession::get_username()."'
					OR cap1 = '".ArghSession::get_username()."'
					OR cap2 = '".ArghSession::get_username()."'
				) ORDER BY id DESC
				LIMIT 1";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		$gid = $l[0];
		echo '<center>'.Lang::LADDER_YOUR_GAME_HAS_STARTED.'. '.Lang::INFORMATION.' <a href="?f=laddervip_game&id='.$gid.'">'.Lang::HERE.'</a></center><br /><br />';
		
		if (ArghSession::is_gold()) {
			//On joue le son s'il n'a pas été joué
			$req = "SELECT soundplayed FROM lg_users WHERE username = '".ArghSession::get_username()."'";
			$t = mysql_query($req);
			$l = mysql_fetch_row($t);
			if ($l[0] == 0) {
				echo '<embed autostart="true" loop="false" hidden="true" src="sound/ArrangedTeamInvitation.wav"></embed>';
				$upd = "UPDATE lg_users SET soundplayed = '1' WHERE username = '".ArghSession::get_username()."'";
				mysql_query($upd);
			}
		}
	}
	
	
	//Verif Garena
	if (!ArghSession::is_garena_account_set()) {
		//Garena manquant
		echo '<center>'.Lang::LADDER_MUST_FILL_GARENA_ACCOUNT.'<br /><br /><a href="?f=member">'.Lang::MEMBER_SPACE.'</a></center>';
		
	} else {
	
		//Boutons
		$cJD = canJoinDet();
		echo '<table class="listing">
			<colgroup>
				<col width="10%" />
				<col width="35%" />
				<col width="20%" />
				<col width="35%" />
			</colgroup>';
		echo '<tr><td colspan="2">
			<div id="btn_refresh">
				<center><a href="javascript:Refresh(1);"><img src="ladder/btn_refresh.jpg" alt="" /></a></center>
			</div>
			</td><td colspan="2"><center><div id="btn_2">';
		switch ($cJD) {
			case 0:
			case 1:
				echo '<img src="ladder/btn_nojoin.jpg" alt="" />';
				break;
				
			case 3:
				echo '<a href="javascript:Leave();"><img src="ladder/btn_leave.jpg" alt="" /></a>';
				break;
				
			case 2:
				echo '<a href="javascript:Join();"><img src="ladder/btn_join.jpg" alt="" /></a>';
				break;
		}
		echo '</div></center></td></tr>
				<tr><td colspan="4"><center>
					<div id="ajax_loading"><img src="img/black.jpg" alt="" /></div>
				</center></td></tr>';
		
		//Début tableau
		echo '<tr><td colspan="4">&nbsp;</td></tr>';
		
		echo '<tr><td colspan="4"><strong>'.Lang::GAME_ID.': # </strong>'.$infos[0].'</td></tr>';
		
		$version = CacheManager::get_ladder_version();
		$mode = CacheManager::get_ladder_mode_modulo($infos[0]);
		$w3_version = CacheManager::get_w3_version();
		
		echo '<tr><td colspan="4"><!--<strong>'.Lang::MODE.': '.$mode.'</strong> / --><strong>'.Lang::VERSION.': <span class="vip">'.$version.'&nbsp;</strong><a href="http://www.getdota.com"><img src="/ligue/icon_w3g.jpg" alt="" /></a></span> - '.Lang::W3_VERSION.': <b>'.$w3_version.'</b></td></tr>';
		echo '<tr><td colspan="4"><strong>'.Lang::PLATFORM.':</strong> Garena -> Tournament -> <span class="win">Argh Room</span> ('.Lang::PASSWORD.' = midas)</td></tr>';
		echo '<tr><td colspan="4"><img src="img/ts.gif"> <strong>'.Lang::TEAMSPEAK.':</strong> ts.dota.fr ('.Lang::PASSWORD.': argh)</td></tr>';
		echo '<tr><td colspan="4"><strong>'.Lang::TEAMSPEAK_CHANNEL.':</strong> :: '.Lang::LADDER.' - '.TeamSpeakChannels::get_laddervip_channel($infos[0]).' ::</td></tr>';
		echo '<tr><td colspan="4">&nbsp;</td></tr>';
		echo '<tr>
				<td><b>'.Lang::SLOT.'</b></td>
				<td><b>'.Lang::PLAYER.'</b></td>
				<td><b>'.Lang::XP.'</b></td>
				<td><b>'.Lang::GARENA_ACCOUNT.'</b></td>
			</tr>';
		echo '<tr class="line"><td colspan="4">&nbsp;</td></tr>';
		
		//Listing
		//Début
		if (file_exists(CacheManager::LADDER_VIP_PLAYERLIST)) {
			$content = file(CacheManager::LADDER_VIP_PLAYERLIST);
			$i = 0;
			foreach ($content as $val) {
				$line = explode(';', $val);
				if (count($line) == 4) {
					$i++;
					//Icon : <td><img src="/ligue/img/vip_ranks/xp/'.(empty($line[1]) ? 10 : $line[1]).'.gif" alt="" /></td>
					$bg = ($line[0] == ArghSession::get_username()) ? ' style="border-top: 1px solid #303036; border-bottom: 1px solid #303036;"' : '';
					echo '<tr'.$bg.'>
							<td><i>'.$i.'.</i></td>
							<td><a href="?f=player_profile&player='.$line[0].'">'.$line[0].'</a></td>
							<td>'.XPColorize($line[1]).'</td>
							<td>'.htmlentities($line[2]).'</td>
						</tr>';
				}
			}
		}
		//Fin Listing

		echo '</table>';
		echo '<div id="icon">'.$cJD.'</div>';
	}
?>
