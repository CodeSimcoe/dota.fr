<?php
	include 'ladder_functions.php';
	include 'classes/TeamSpeakChannels.php';

	$game_id = (int)$_GET['id'];
	
	function Minutes($started) {
		return round((time() - $started) / 60, 0);
	}
	
	
	$votes = array();
	$t = mysql_query("SELECT qui, winner, vote_time FROM lg_winnersreports WHERE game_id = '".$game_id."' ORDER BY vote_time ASC");
	
	while ($l = mysql_fetch_object($t)) {
		switch ($l->winner) {
			case 'se':
				$winner = Lang::SENTINEL;
				break;
			case 'sc':
				$winner = Lang::SCOURGE;
				break;
			case 'none';
				$winner = Lang::NONE;
				break;
			/*
			default:
				$winner = Lang::NOT_VOTED;
				break;
			*/
		}
		
		$votes[$l->qui] = array($winner, $l->vote_time);
	}
	
	handleMsg($game_id);
	
	//Administration
	if (ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN))) {
	
		$req = "SELECT * FROM lg_laddergames WHERE id = '".$game_id."'";
		$t = mysql_query($req);
		$players = array();
		$l = mysql_fetch_object($t);
		for ($i = 0; $i <= 9; $i++) {
			$pl = 'p'.$i;
			$players[$i] = $l->$pl;
		}
		
		if (in_array(ArghSession::get_username(), $players)
			&& !ArghSession::is_rights(RightsMode::WEBMASTER)
			&& (isset($_POST['subm_forcevotes'])
				|| isset($_POST['subm_forcevotes'])
				|| isset($_POST['subm_empty'])
				|| isset($_POST['subm_clear'])
				)
			) {
			echo '<script language="javascript">alert(\''.Lang::LADDER_CANT_ADMINISTRATE_GAME.'.\')</script>';
		} else {
		
			//Vider les votes
			if (isset($_POST['subm_empty'])) {
				$req = "DELETE FROM lg_playersreports WHERE game_id = '".$game_id."'";
				mysql_query($req);
				
				//Log
				$al = new AdminLog(sprintf(Lang::ADMIN_LOG_EMPTY_VOTES, $game_id, $game_id), AdminLog::TYPE_LADDER);
				$al->save_log();
			}
			
			//Clear
			if (isset($_POST['subm_clear'])) {
				clearGame($game_id);

				//Log
				$al = new AdminLog(sprintf(Lang::ADMIN_LOG_CANCEL_GAME, $game_id, $game_id), AdminLog::TYPE_LADDER);
				$al->save_log();
			}
			
			//Report
			if (isset($_POST['subm_result'])) {
				GameReporter::report($game_id, $_POST['winner'], true);
				$upd = "UPDATE lg_ladder_stats_games SET new = 1 WHERE game_id = '".$game_id."'";
				mysql_query($upd);
				
				//Log
				$al = new AdminLog(sprintf(Lang::ADMIN_LOG_FORCE_RESULT, $game_id, $game_id), AdminLog::TYPE_LADDER);
				$al->save_log();
			}
			
			if (isset($_POST['subm_forcevotes'])) {
				for ($i = 0; $i < 8; $i++) {
					$ins = "INSERT INTO lg_playersreports (qui, pour_qui, game_id, info)
							VALUES ('".Lang::ADMIN."', '".mysql_real_escape_string($_POST['leaveraway'])."', '".$game_id."', '".mysql_real_escape_string($_POST['what'])."')";
					mysql_query($ins);
				}
				//Log
				$al = new AdminLog(sprintf(Lang::ADMIN_LOG_FORCE_VOTES, $game_id, $game_id, mysql_real_escape_string($_POST['leaveraway']), LadderStates::$PLAYERS_INFOS[$_POST['what']]), AdminLog::TYPE_LADDER);
				$al->save_log();
			}
			
		}
	}
	
	//Infos sur la game
	$req = "SELECT * FROM lg_laddergames WHERE id = '".$game_id."'";
	$t = mysql_query($req);
	if (mysql_num_rows($t) > 0) {
		$se = '';
		$sc = '';
		$tot_se = 0;
		$tot_sc = 0;
		$l = mysql_fetch_object($t);
		
		ArghPanel::begin_tag(Lang::LADDER_GAME.' #'.$game_id);
		echo '<table class="listing">
			<colgroup>
				<col width="5%" />
				<col width="35%" />
				<col width="25%" />
				<col width="20%" />
				<col width="15%" />
			</colgroup>';
		
		$option = '';
		
		
		//Cap
		$mode = CacheManager::get_ladder_mode_modulo($game_id);
		
		$is_cd_mode = false;
		
		if ($mode == '-cd') {
			$rrr = "SELECT u.username, u.pts, IFNULL(v.rank, 0)
					FROM lg_users u LEFT JOIN lg_laddervip_vouchlist v ON u.username = v.username
					WHERE u.username IN ('".$l->p1."', '".$l->p2."', '".$l->p3."', '".$l->p4."', '".$l->p5."')
					ORDER BY v.rank DESC, u.pts DESC";
			$ttt = mysql_query($rrr) or die(mysql_error());
			while ($lll = mysql_fetch_row($ttt)) {
				$cap1 = $lll[0];
				break;
			}
			
			$rrr = "SELECT u.username, u.pts, IFNULL(v.rank, 0)
					FROM lg_users u LEFT JOIN lg_laddervip_vouchlist v ON u.username = v.username
					WHERE u.username IN ('".$l->p6."', '".$l->p7."', '".$l->p8."', '".$l->p9."', '".$l->p10."')
					ORDER BY v.rank DESC, u.pts DESC";
			$ttt = mysql_query($rrr) or die(mysql_error());
			while ($lll = mysql_fetch_row($ttt)) {
				$cap2 = $lll[0];
				break;
			}
			$is_cd_mode = true;
		}
		
		for ($i = 1; $i <= 10; $i++) {
		
			$pl = 'p'.$i;
			$bo = 'b'.$i;
			$xp = 'xp'.$i;
			
			//Infos du joueur i
			$sreq = "SELECT ggc, pts
					FROM lg_users
					WHERE username = '".$l->$pl."'";
			$st = mysql_query($sreq);
			$sl = mysql_fetch_row($st);
			$sreq2 = "SELECT resultat
					FROM lg_ladderfollow
					WHERE player = '".$l->$pl."'
					AND game_id = '".$l->id."'";
			$st2 = mysql_query($sreq2);
			$sl2 = mysql_fetch_row($st2);
		
			if ($i == 1) {
				echo '<tr><td colspan="5" align="center"><img src="img/sentinel.png" alt="'.Lang::SENTINEL.'" /></td></tr>';
			} elseif ($i == 6) {
				echo '<tr><td colspan="5">&nbsp;</td></tr>
					<tr><td colspan="5" align="center"><img src="img/scourge.png" alt="'.Lang::SCOURGE.'" /></td></tr>';
			}
			if ($i == 1 or $i == 6) {
				echo '<tr>
					<td><b>#</b></td>
					<td><b>'.Lang::USERNAME.'</b></td>
					<td><b>'.Lang::GARENA_ACCOUNT.'</b></td>
					<td><b>'.Lang::VOTE.'</b></td>
					<td><b>'.Lang::XP.'</b></td>
					</tr>';
			}

			$option .= '<option>'.$l->$pl.'</option>';
			if ($i <= 5) {
				$se .= $sl[0].' ';
				$tot_se += $l->$xp;
				//$se .= getGGC($l->$pl).' ';
				//$tot_se += getPts($l->$pl);
			} else {
				$sc .= $sl[0].' ';
				$tot_sc += $l->$xp;
				//$sc .= getGGC($l->$pl).' ';
				//$tot_sc += getPts($l->$pl);
			}
			$alt = (($i < 6 and $i%2 == 0) or ($i > 5 and $i%2 == 1)) ? ' class="alternate"' : '';
			echo '<tr'.$alt.'><td>';
			
			switch ($sl2[0]) {
				case 'win':
					$score = '<span class="win">+'.$l->$bo.'</span>';
					break;
				case 'lose':
				case 'away':
					$score = '<span class="lose">'.$l->$bo.'</span>';
					break;
				case 'left':
					$score = '<span class="draw">'.($l->$bo > 0 ? '+' : '').$l->$bo.'</span>';
					break;
				default:
					$score = '<span class="info">0</span>';
			}
			if (($l->$pl == $cap1 || $l->$pl == $cap2) && $is_cd_mode) {
				$img_cap = '<img src="/img/potential_cap_gold.png" alt="" />&nbsp;';
			} else {
				$img_cap = '';
			}
			
			echo '<i>'.$i.'.</i></td>
				<td>'.$img_cap.'<a href="?f=player_profile&player='.$l->$pl.'">'.$l->$pl.'</a> <span class="info">('.$l->$xp.' / '.$sl[1].')</span></td>
				<td'.$alt.'>'.$sl[0].'</td>
				<td'.$alt.'>'.(empty($votes[$l->$pl][0]) ? Lang::NOT_VOTED : $votes[$l->$pl][0]).'</td>
				<td'.$alt.'><b>'.$score.'</b></td>
				</tr>';
		}
		
		echo '<tr><td colspan="5">&nbsp;</td></tr>';
		
		if ($l->status == LadderStates::CLOSED) {
			echo '<tr><td colspan="5"><center><strong>'.Lang::WINNER.'</strong><br />';
			switch ($l->winner) {
				case 'se':
					echo '<img src="ladder/se.jpg" alt="'.Lang::SENTINEL.'" />';
					break;
				case 'sc';
					echo '<img src="ladder/sc.jpg" alt="'.Lang::SCOURGE.'" />';
					break;
				default:
					echo '<img src="ladder/none.jpg" alt="'.Lang::NONE.'" />';
					break;
			}
			echo '</center></td></tr>';
			
		} elseif ($l->status == LadderStates::PLAYING) {
		
			echo '<tr><td colspan="5" align="center">'.sprintf(Lang::LADDER_GAME_STARTED_X_MINS_AGO, Minutes($l->opened)).'</td></tr>';
			echo '<tr><td colspan="5">&nbsp;</td></tr>';
			echo '<tr><td colspan="5" align="center"><b>'.Lang::MODE.': '.$mode.' - '.Lang::TEAMSPEAK_CHANNEL.':</b> :: Ladder - '.TeamSpeakChannels::get_ladder_channel($game_id).'</td></tr>';
			echo '<tr><td colspan="5">&nbsp;</td></tr>';
			echo '<tr><td colspan="5" align="center"><textarea cols="65" rows="2">'.Lang::SENTINEL.': '.$se.'
'.Lang::SCOURGE.': '.$sc.'</textarea></td></tr>';
		} elseif ($l->status == LadderStates::ADMIN_OPENED) {
			echo '<tr><td colspan="5" align="center"><span class="lose">'.Lang::LADDER_ADMIN_OPENED_GAME.'</span></td></tr>';
		}
		echo '</table>';
		ArghPanel::end_tag();
		
		require 'classes/ReportModule.php';
		
		ArghPanel::begin_tag(Lang::REPORT_GAME_REPORT);
		$report = new Report();
		$report->_game_id = $game_id;
		$report->load();
		if ($report->_status == Report::STATUS_NO_REPORT) {
			echo '<center><a href="?f=ladder_report&id='.$game_id.'">'.Lang::REPORT_OPEN.'</a></center>';
		} else {
			echo '<center><img src="img/icons/information.png" alt="" />&nbsp;<a href="?f=ladder_report&id='.$game_id.'">'.sprintf(Lang::REPORT_REPORT_OPENED_BY, $report->_initiator).'</a></center>';
		}
		
		ArghPanel::end_tag();
		
		//Game Administration
		if (ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN))) {
			ArghPanel::begin_tag(Lang::LADDER_GAME_ADMINISTRATION);
			echo sprintf(Lang::LADDER_GAME_OPENED_ON, date(Lang::DATE_FORMAT_HOUR, $l->opened)).'<br />';
			if ($l->when_closed > 0) {
				echo sprintf(Lang::LADDER_GAME_CLOSED_ON, date(Lang::DATE_FORMAT_HOUR, $l->when_closed)).'<br /><br />';
			}
			echo '<br /><b>'.Lang::LADDER_VOTES_INFORMATION.'</b><br /><br />';
			
			foreach ($votes as $player => $data) {
				echo $data[1]."\t".'<a href="?f=player_profile&player='.$player.'">'.$player.'</a><br />';
			}
			echo '<br />';
			
			$req = "SELECT *
					FROM lg_playersreports
					WHERE game_id = '".$game_id."'
					ORDER BY pour_qui ASC, info ASC, qui ASC";
			$t = mysql_query($req);
			$j = 0;
			if (mysql_num_rows($t) > 0) {
				echo '<table class="listing">
					<colgroup>
						<col width="55%" />
						<col width="25%" />
						<col width="25%" />
					</colgroup>';
				echo '<thead>
						<tr>
							<th>'.Lang::VOTER.'</th>
							<th>'.Lang::CONCERNED_PLAYER.'</th>
							<th>'.Lang::REASON.'</th>
						</tr>
					</thead>
					<tbody>';
				while ($l = mysql_fetch_object($t)) {
					if ($l->info == 1) {
						$info = Lang::LEAVER;
					} elseif ($l->info == 2) {
						$info = Lang::AWAY;
					} else {
						$info = Lang::BEHAVIOR;
					}
					
					$who = ($l->qui == 'Admin') ? '<span class="vip">'.$l->qui.'</span>' : '<a href="?f=player_profile&player='.$l->qui.'">'.$l->qui.'</a>';
					
					echo '<tr'.Alternator::get_alternation($j).'>
						<td>'.$who.'</td>
						<td><a href="?f=player_profile&player='.$l->pour_qui.'">'.$l->pour_qui.'</a></td>
						<td>'.$info.'</td>
					</tr>';
				}
				echo '</tbody>
					</table>';
				
			} else {
				echo '<center>'.Lang::NO_VOTE.'</center>';
			}
			
			echo '<br />
			<form action="?f=ladder_game&id='.$game_id.'" method="POST">
				<input type="submit" name="subm_empty" value="'.Lang::LADDER_EMPTY_VOTES.'" /><br/>
				<span class="info">'.Lang::LADDER_EMPTY_VOTES_EXPLANATION.'</span>
			</form>';
			
			echo '<br />
			<form action="?f=ladder_game&id='.$game_id.'" method="POST">
				<select name="what">';
				foreach (LadderStates::$PLAYERS_INFOS as $key => $val) {
					echo '<option value="'.$key.'">'.$val.'</option>';
				}
				echo '</select><select name="leaveraway">'.$option.'
				</select>&nbsp;<input type="submit" name="subm_forcevotes" value="'.Lang::LADDER_FORCE_VOTES.'" /><br/>
				<span class="info">'.Lang::LADDER_FORCE_VOTES_EXPLANATION.'</span>
			</form>';
			
			echo '<br /><b>'.Lang::LADDER_GAME_ADMINISTRATION.'</b><br />';
			echo '<form action="?f=ladder_game&id='.$game_id.'" method="POST">
				<input type="submit" name="subm_clear" value="'.Lang::LADDER_CANCEL_RESULT.'" /><br />
				<span class="info">'.Lang::LADDER_CANCEL_RESULT_EXPLANATION.'</span>
			</form>';
			
			echo '<br />
			<form action="?f=ladder_game&id='.$game_id.'" method="POST">
				<input type="submit" name="subm_result" value="'.Lang::LADDER_FORCE_RESULT.'" />
				<select name="winner">
					<option value="none">'.Lang::NONE.'</option>
					<option value="se">'.Lang::SENTINEL.'</option>
					<option value="sc">'.Lang::SCOURGE.'</option>
				</select><br />
				<span class="info">'.Lang::LADDER_FORCE_RESULT_EXPLANATION.'</span>
			</form>';
			
			ArghPanel::end_tag();
		}
		
		listMsg($game_id);
		blocMsg($game_id, '?f=ladder_game&amp;id='.$game_id);
	}
?>