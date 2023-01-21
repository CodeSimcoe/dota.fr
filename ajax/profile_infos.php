<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/var/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/RightsMode.php';
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'classes/LadderStates.php';
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';
?>
<table class="listing">
	<colgroup><col width="25" /><col width="200" /><col /><col width="25" /></colgroup>
<?php
	
	function Minutes($started) {
		return round((time() - $started) / 60, 0);
	}
	
	function canVouch($player) {
		$req = "SELECT vouchs FROM lg_users WHERE username = '".$player."' AND ((rights & ".RightsMode::VIP_VOUCHER.") = ".RightsMode::VIP_VOUCHER.")";
		$t = mysql_query($req);
		if (mysql_num_rows($t) == 1) {
			$l = mysql_fetch_row($t);
			return ($l[0] > 0);
		} else {
			return false;
		}
	}
	
	$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
	
	/*
	//Vouch
	if (isset($_GET['action']) && $_GET['action'] == 'vouch' && ArghSession::is_rights(RightsMode::VIP_VOUCHER)) {
		if (mysql_num_rows(mysql_query("SELECT * FROM lg_vouchs WHERE voucher = '".ArghSession::get_username()."' AND qui = '".$player."'")) == 0) {
			//Ok on peut voter
			$nbVchRS = mysql_query("SELECT vouchs FROM lg_users WHERE username = '".ArghSession::get_username()."'");
			$nbVch = mysql_fetch_row($nbVchRS);
			if ($nbVch[0] > 0) {
				mysql_query("INSERT INTO lg_vouchs (voucher, qui, date_vouch) VALUES ('".ArghSession::get_username()."', '".$player."', '".time()."')");
				mysql_query("UPDATE lg_users SET vouchs = vouchs - 1 WHERE username = '".ArghSession::get_username()."'") or die(mysql_error());
				//On regarde s'il faut voucher
				if (mysql_num_rows(mysql_query("SELECT * FROM lg_vouchs WHERE qui = '".$player."'")) >= 4) {
					if (mysql_num_rows(mysql_query("SELECT * FROM lg_laddervip_vouchlist WHERE username = '".$player."'")) == 0) {
						if (canVouch(ArghSession::get_username())) {
							//Vouch !
							mysql_query("INSERT INTO lg_laddervip_vouchlist (username, rank) VALUES ('".$player."', '1')") or die(mysql_error());
						}
					}
				}
			}
		}
	}
	*/
	
		
	//Infos generales
	if (isset($_GET['player'])) {
		//Debut verif
		$req = "SELECT u.username, u.joined, u.clan, c.name, c.divi, c.tag, 
				u.crank, u.bnet, u.ggc, u.birth, YEAR(CURRENT_DATE) - YEAR(u.birth) - (IF(DAYOFYEAR(u.birth)>DAYOFYEAR(CURRENT_DATE),1,0)) AS age,
				u.city, u.country, u.gold, u.ladder_status, v.rank,
				u.voucher, u.vouchs, u.last_profile_viewer, u.is_gold, u.rights, u.rgc_account
				FROM lg_users u LEFT JOIN lg_clans c ON u.clan = c.id LEFT JOIN lg_laddervip_vouchlist v ON u.username = v.username
				WHERE u.username = '".$player."'";
		$t=mysql_query($req);
		if (mysql_num_rows($t)) {
			
			//profil general
			$l = mysql_fetch_row($t);
			
			//Nbr vues profil
			if ($l[18] != $_SERVER['REMOTE_ADDR']) {
				$req = "UPDATE lg_users
						SET profile_views = profile_views + 1, last_profile_viewer = '".$_SERVER['REMOTE_ADDR']."'
						WHERE username = '".$player."'";
				mysql_query($req);
			}
			
			$flag = (strlen($l[12]) > 0) ? '<img src="img/flag/'.$l[12].'.gif" alt="" /> ' : '';
			echo '
			<tr><td colspan="4"><center><img src="img/lang/'.ArghSession::get_lang().'/profile.png" alt="'.Lang::PROFILE.'" /></center></td></tr>
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr><td ></td><td>'.Lang::USERNAME.': </td><td>'.$flag.'<strong>'.htmlentities($l[0]).'</strong></td><td></td></tr>';
			if ($l[19] == 1 || $l[20] != 0) {
				echo '<tr><td></td><td>'.Lang::ACCOUNT.': </td><td><span class="vip"><b>'.Lang::GOLD.'</b></span></td><td></td></tr>';
			}
			echo '<tr><td></td><td>'.Lang::REGISTERATION_DATE.': </td><td>'.date(Lang::DATE_FORMAT_DAY, $l[1]).'</td><td></td></tr>';
			
			$user_team = ($l[2] == 0) ? 'aucune' : '<a href="?f=team_profile&id='.$l[2].'">'.htmlentities($l[3]).' ['.htmlentities($l[5]).'] </a><img src="'.$l[6].'.gif" alt="Rang">';
			echo '<tr><td></td><td>'.Lang::TEAM.': </td><td>'.$user_team.'</td><td></td></tr>';
			if ($l[2] != 0 and $l[4] != 0) {
				echo '<tr><td></td><td>'.Lang::DIVISION.': </td><td><a href="?f=league_division&div='.$l[4].'">'.$l[4].'</a></td><td></td></tr>';
			}
			echo '<tr><td></td><td>'.Lang::GOLD.': </td><td><b>'.$l[13].' <img src="img/gold.gif" alt="'.Lang::GOLD.'" /> </b></td><td></td></tr>';
			
			echo '<tr><td colspan="4">&nbsp;</td></tr>';
			//echo '<tr><td></td><td>'.Lang::BNET_ACCOUNT.': </td><td><a href="http://www.battle.net/war3/ladder/w3xp-player-profile.aspx?Gateway=Northrend&PlayerName='.$l[7].'">'.htmlentities($l[7]).'</a></td><td></td></tr>';
			if (!empty($l[8])) {
				echo '<tr><td></td><td>'.Lang::GARENA_ACCOUNT.': </td><td>'.htmlentities($l[8]).'</td><td></td></tr>';
			}
			if (!empty($l[21])) {
				echo '<tr><td></td><td>'.Lang::RGC_ACCOUNT.': </td><td>'.htmlentities($l[21]).'</td><td></td></tr>';
			}
			
			echo '<tr><td colspan="4">&nbsp;</td></tr>';
			
			$age = ($l[9] != '0000-00-00') ? $l[10] : '';
			
			echo '<tr><td></td><td>'.Lang::AGE.': </td><td>'.$age.'</td><td></td></tr>';
			echo '<tr><td></td><td>'.Lang::CITY.': </td><td>'.htmlentities($l[11]).'</td><td></td></tr>';
			
			echo '<tr><td colspan="4">&nbsp;</td></tr>';
			
			//Lien pour Voucher
			echo '<tr><td></td><td>';
			if (ArghSession::is_rights(RightsMode::VIP_VOUCHER)) {
				if (empty($l[15]) && mysql_num_rows(mysql_query("SELECT * FROM lg_vouchs v WHERE v.voucher = '".ArghSession::get_username()."' AND v.qui = '".$player."'")) == 0) {
					if (canVouch(ArghSession::get_username())) {
						echo '<a href="?f=player_profile&player='.$player.'&action=vouch" onClick="return confirm(\''.Lang::CONFIRM_VOUCH.'\');"><img src="img/vouch.jpg" alt="" /></a> ';
					}
				}
			}
			echo Lang::VOUCH_VIP.': </td><td>';
			
			if (empty($l[15])) {
				//Non Vouched
				$vreq = "SELECT *
						FROM lg_vouchs 
						WHERE qui = '".$player."'
						ORDER BY date_vouch ASC";
				$vt = mysql_query($vreq);
				$nbVouchs = mysql_num_rows($vt);
				echo Lang::NO.' - <b>'.$nbVouchs.'</b>/4</td><td></td></tr>';
				if ($nbVouchs > 0) {
					$i = 0;
					while ($vl = mysql_fetch_object($vt)) {
						$i++;
						echo '<tr><td colspan="2"></td><td colspan="2"><span class="info">'.date(Lang::DATE_FORMAT_DAY, $vl->date_vouch).' - '.$vl->voucher.'</span></td></tr>';
					}
				}
			} else {
				//Vouched
				echo Lang::CAPLEVEL.' <b>'.$l[15].'</b></td><td></td></tr>';
			}
			//Voucher
			if ($l[16] == 1) {
				echo '<tr><td></td>
						<td><span class="vip">'.Lang::VOUCHER_VIP.'</span></td>
						<td><b>'.$l[17].'</b> '.Lang::VOUCH.' '.Lang::REMAINING.'</td>
					<td></td></tr>';
			}
			
			echo '<tr><td colspan="4">&nbsp;</td></tr>';
			
			//Ladder Status
			if ($l[14] != LadderStates::READY) {
				//On récupère la game dans laquelle il est
				if ($l[14] == LadderStates::IN_NORMAL_GAME) {
					$areq = "SELECT id, opened
							FROM lg_laddergames
							WHERE (
								p1 = '".$player."'
								OR p2 = '".$player."'
								OR p3 = '".$player."'
								OR p4 = '".$player."'
								OR p5 = '".$player."'
								OR p6 = '".$player."'
								OR p7 = '".$player."'
								OR p8 = '".$player."'
								OR p9 = '".$player."'
								OR p10 = '".$player."'
							) AND status = '".LadderStates::PLAYING."'
							ORDER BY id DESC
							LIMIT 1";
					$at = mysql_query($areq) or die(mysql_error());
					if (mysql_num_rows($at) > 0) {
						$al = mysql_fetch_row($at);
						echo '<tr><td></td><td>'.Lang::STATUS.': </td><td><span class="win">'.Lang::ONLINE.'</span></td><td></td></tr>';
						echo '<tr><td></td><td> </td><td>dans la partie <a href="?f=ladder_game&id='.$al[0].'">'.Lang::LADDER.' #'.$al[0].'</a> ('.Minutes($al[1]).' '.Lang::MINUTE_LETTER.')</td><td></td></tr>';
					} else {
						$line = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM lg_usersonline WHERE user = '".$l[0]."'"));
						$status = '<span class="'.(($line[0] == 1) ? 'win">'.Lang::ONLINE : 'lose">'.Lang::OFFLINE).'</span>';
						
						echo '<tr><td></td><td>'.Lang::STATUS.': </td><td>'.$status.'</td><td></td></tr>';
					}
				} else {
					$areq = "SELECT id, opened
							FROM lg_laddervip_games
							WHERE (
								p1 = '".$player."'
								OR p2 = '".$player."'
								OR p3 = '".$player."'
								OR p4 = '".$player."'
								OR p5 = '".$player."'
								OR p6 = '".$player."'
								OR p7 = '".$player."'
								OR p8 = '".$player."'
								OR cap1 = '".$player."'
								OR cap2 = '".$player."'
							) AND status = '".LadderStates::PLAYING."'
							ORDER BY id DESC
							LIMIT 1";
					$at = mysql_query($areq) or die(mysql_error());
					if (mysql_num_rows($at) > 0) {
						$al = mysql_fetch_row($at);
						echo '<tr><td></td><td>'.Lang::STATUS.': </td><td><span class="win">'.Lang::ONLINE.'</span></td><td></td></tr>';
						echo '<tr><td></td><td> </td><td>'.Lang::IN_LADDERGAME.' <a href="?f=laddervip_game&id='.$al[0].'">'.Lang::LADDER_VIP.' #'.$al[0].'</a> ('.Minutes($al[1]).' '.Lang::MINUTE_LETTER.')</td><td></td></tr>';
					} else {
						$line = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM lg_usersonline WHERE user = '".$l[0]."'"));
						$status = '<span class="'.(($line[0] == 1) ? 'win">'.Lang::ONLINE : 'lose">'.Lang::OFFLINE).'</span>';
						
						echo '<tr><td></td><td>'.Lang::STATUS.': </td><td>'.$status.'</td><td></td></tr>';
					}
				}
			} else {
				$line = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM lg_usersonline WHERE user = '".$l[0]."'"));
				$status = '<span class="'.(($line[0] == 1) ? 'win">'.Lang::ONLINE : 'lose">'.Lang::OFFLINE).'</span>';
				
				echo '<tr><td></td><td>'.Lang::STATUS.': </td><td>'.$status.'</td><td></td></tr>';
			}
		}
	}
?>
	</table>