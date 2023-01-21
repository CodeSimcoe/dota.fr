<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	//Constantes
	$ACTIONTIME_HIGH = 240;
	$ACTIONTIME_LOW = 45;
	$PICK_TIME = 50;
	
	//Includes
	require_once ABSOLUTE_PATH.'mysql_connect.php';
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	
	require_once ABSOLUTE_PATH.'classes/AdminLog.php';
	require_once ABSOLUTE_PATH.'classes/LadderStates.php';
	require_once ABSOLUTE_PATH.'classes/TeamSpeakChannels.php';
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	require_once ABSOLUTE_PATH.'classes/ArghPanel.php';
	require_once ABSOLUTE_PATH.'classes/Alternator.php';
	require_once ABSOLUTE_PATH.'classes/RightsMode.php';
	require_once ABSOLUTE_PATH.'classes/Heroes.php';
	include ABSOLUTE_PATH.'laddervip_functions.php';
	
	$game_id = (int)$_GET['id'];
	
	$pictures = array(
		'fp' => '<img src="/ligue/ladder/first_pick_small_gray.jpg" alt="" />',
		'sp' => '<img src="/ligue/ladder/second_pick_small_gray.jpg" alt="" />',
		'se' => '<img src="/ligue/ladder/se_small_gray.jpg" alt="" />',
		'sc' => '<img src="/ligue/ladder/sc_small_gray.jpg" alt="" />',
	);
	
	$opposites = array(
		'fp' => '<img src="/ligue/ladder/second_pick_small_gray.jpg" alt="" />',
		'sp' => '<img src="/ligue/ladder/first_pick_small_gray.jpg" alt="" />',
		'se' => '<img src="/ligue/ladder/sc_small_gray.jpg" alt="" />',
		'sc' => '<img src="/ligue/ladder/se_small_gray.jpg" alt="" />',
	);
	
	//Fonctions
	function getTeam($nb) {
		return ($nb == 1 || $nb == 4 || $nb == 5 || $nb == 8) ? 'A' : 'B';
	}

	function getVote($game_id, $voter) {
		$t = mysql_query("SELECT winner FROM lg_laddervip_winnersreports WHERE game_id = '".$game_id."' AND qui = '".$voter."' LIMIT 1");
		$l = mysql_fetch_row($t);
		$winner = (mysql_num_rows($t) > 0) ? $l[0] : '-';
		if ($winner == 'se') {
			return 'sentinel';
		} elseif ($winner == 'sc') {
			return 'scourge';
		} elseif ($winner == 'none') {
			return 'aucun';
		} else {
			return 'non vot&eacute;';
		}
	}
	
	//Administration
	if (ArghSession::is_rights(array(RightsMode::VIP_HEADADMIN, RightsMode::VIP_ADMIN))) {
	
		//Vider les votes
		if (isset($_POST['subm_empty'])) {
			$req = "DELETE FROM lg_laddervip_playersreports WHERE game_id = '".$game_id."'";
			mysql_query($req);
			
			//Log
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_EMPTY_VOTES_VIP, $game_id, $game_id), AdminLog::TYPE_LADDER);
			$al->save_log();
		}
		
		//Clear
		if (isset($_POST['subm_clear'])) {
			clearGame($game_id);

			//Log
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_CANCEL_GAME_VIP, $game_id, $game_id), AdminLog::TYPE_LADDER);
			$al->save_log();
		}
		
		//Report
		if (isset($_POST['subm_result'])) {
			reportGame($game_id, $_POST['winner']);
			//Log
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_FORCE_RESULT_VIP, $game_id, $game_id), AdminLog::TYPE_LADDER);
			$al->save_log();
		}
		
		if (isset($_POST['subm_forcevotes'])) {
			for ($i = 0; $i < 8; $i++) {
				mysql_query("INSERT INTO lg_laddervip_playersreports (qui, pour_qui, game_id, info) VALUES ('Admin', '".$_POST['leaveraway']."', '".$game_id."', '".$_POST['what']."')");
			}
			//Log
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_FORCE_VOTES_VIP, $game_id, $game_id, mysql_real_escape_string($_POST['leaveraway']), LadderStates::$PLAYERS_INFOS[$_POST['what']]), AdminLog::TYPE_LADDER);
			$al->save_log();
		}
	}
	
	//Game en cours
	ArghPanel::begin_tag(Lang::LADDERVIP_GAME.' #'.(int)$game_id);
	
	$req = "SELECT * FROM lg_laddervip_games WHERE id = '".(int)$game_id."'";
	$t = mysql_query($req);
	if (mysql_num_rows($t) > 0) {
		$l = mysql_fetch_object($t);
		
		$players = array($l->cap1, $l->cap2, $l->p1, $l->p2, $l->p3, $l->p4, $l->p5, $l->p6, $l->p7, $l->p8);
		$sentinel = array($l->cap1, $l->pp1, $l->pp4, $l->pp5, $l->pp8);
		$scourge = array($l->cap2, $l->pp2, $l->pp3, $l->pp6, $l->pp7);
		
		$options = '';
		foreach ($players as $player) {
			$options .= '<option value="'.$player.'">'.$player.'</option>';
		}
		
		//Status
		$status = (string)$l->status;
		$redStatus = substr($status, 2, 7);
		
		//Playing
		if ($status == LadderStates::PLAYING || $status == LadderStates::CLOSED || $status == LadderStates::ADMIN_OPENED) {
		
			$header = '<thead><tr>
					<th>#</th>
					<th>'.Lang::USERNAME.'</th>
					<th>'.Lang::GARENA_ACCOUNT.'</th>
					<th>'.Lang::VOTE.'</th>
					<th>'.Lang::XP.'</th>
				</tr></thead>';
				
			echo '<table class="listing">
				<colgroup>
					<col width="5%" />
					<col width="35%" />
					<col width="35%" />
					<col width="15%" />
					<col width="10%" />
				</colgroup>';
			
			$query = "SELECT * FROM lg_laddervip_follow WHERE game_id = '".$game_id."'";
			$result = mysql_query($query);
			
			$infos = array();
			while ($object = mysql_fetch_object($result)) {
				switch ($object->resultat) {
					case 'win':
						$infos[$object->player] = '<span class="win">+'.$object->xp.'</span>';
						break;
					case 'lose':
						$infos[$object->player] = '<span class="lose">'.$object->xp.'</span>';
						break;
					case 'left':
						if ($object->xp > 0) {
							$sign = '+';
						} else {
							$sign = '';
						}
						$infos[$object->player] = '<span class="draw">'.$sign.$object->xp.'</span>';
						break;
					case 'none':
						$infos[$object->player] = '<span class="info">'.$object->xp.'</span>';
						break;
					case 'away':
						$infos[$object->player] = '<span class="vip">'.$object->xp.'</span>';
						break;
				}
			}
			
			//echo '<tr><td colspan="5"><center><img src="side_sentinel.jpg" title="'.Lang::SENTINEL.'" /></center></td></tr>';

			echo $header;
			$i = 0;
			foreach ($sentinel as $player) {
				echo '<tr'.Alternator::get_alternation($i).'>
					<td><i>'.$i.'.</i></td>
					<td><a href="?f=player_profile&player='.$player.'">'.$player.'</a></td>
					<td>'.getGGC($player).'</td>
					<td>'.getVote($game_id, $player).'</td>
					<td><b>'.$infos[$player].'</b></td>
					</tr>';
			}
			echo '<tr><td colspan="5">&nbsp;</td></tr>';
			
			//Héros
			$heroes = array($l->h1, $l->h4, $l->h5, $l->h8, $l->h9);
			echo '<tr><td colspan="5">';
			foreach ($heroes as $hero) {
				echo '<img src="/ligue/img/heroes/'.$hero.'.gif" title="'.$hero.'" width="48" height="48" />&nbsp;';
			}
			echo '</td></tr><tr><td colspan="5">&nbsp;</td></tr>';
			
			//Side
			echo '<tr><td colspan="5">'.$pictures[$l->cap1_side].$opposites[$l->cap2_side].'</td></tr>';
			echo '<tr><td colspan="5">&nbsp;</td></tr>';
			
			//echo '<tr><td colspan="5"><center><img src="side_scourge.jpg" title="'.Lang::SCOURGE.'" /></center></td></tr>';
			echo $header;
			$i = 0;
			foreach ($scourge as $player) {
				echo '<tr'.Alternator::get_alternation($i).'>
					<td><i>'.$i.'.</i></td>
					<td><a href="?f=player_profile&player='.$player.'">'.$player.'</a></td>
					<td>'.getGGC($player).'</td>
					<td>'.getVote($game_id, $player).'</td>
					<td><b>'.$infos[$player].'</b></td>
					</tr>';
			}
			echo '<tr><td colspan="5">&nbsp;</td></tr>';
			
			//Héros
			$heroes = array($l->h2, $l->h3, $l->h6, $l->h7, $l->h10);
			echo '<tr><td colspan="5">';
			foreach ($heroes as $hero) {
				echo '<img src="/ligue/img/heroes/'.$hero.'.gif" title="'.$hero.'" width="48" height="48" />&nbsp;';
			}
			echo '</td></tr><tr><td colspan="5">&nbsp;</td></tr>';
			
			//Side
			echo '<tr><td colspan="5">'.$pictures[$l->cap2_side].$opposites[$l->cap1_side].'</td></tr>';
			echo '<tr><td colspan="5">&nbsp;</td></tr>';
			
			echo '<tr><td colspan="5"><b>'.Lang::BANS.'</b></td></tr>';
			echo '<tr><td colspan="5" class="line"></td></tr>';
			echo '<tr>
			<td colspan="5">';
			for ($i = 1; $i <= 8; $i++) {
				echo '<img src="/ligue/img/heroes/'.$l->{'ban'.$i}.'.gif" title="'.$l->{'ban'.$i}.'" width="48" height="48" />&nbsp;';
			}
			echo '</td></tr>
			</table>';
		}
		
		//Picking
		if ($status == LadderStates::CHOOSING) {
			//
		
		} elseif ($redStatus == LadderStates::PICKING) {
		
			echo '<br /><center><div id="btn_refresh"><a href="javascript:Refresh(1);"><img src="ladder/btn_refresh.jpg" title="" /></a></div></center><br />';
			//echo '<b>'.Lang::LADDERVIP_PICK_PHASE.'</b><br /><br />';
		
			$step = $status[strlen($status) - 1];
			if ($step == 0) $step = 10;
			
			if ($status[0] == 'p' || $status[0] == 's') {
			
				echo '<center><div id="loader"><img src="img/black.jpg" alt="" /></div></center><br />';
			
				//Caps
				echo '<table>
					<colgroup>
						<col width="281" />
						<col width="50" />
						<col width="281" />
					</colgroup>
					<tr>
						<td><div class="elsenamebar"><a href="?f=player_profile&player='.$l->cap1.'">'.$l->cap1.'</a></div></td>
						<td align="center"><img src="/ligue/ladder/versus.gif" alt="" /></td>
						<td><div class="elsenamebar"><a href="?f=player_profile&player='.$l->cap2.'">'.$l->cap2.'</a></div></td>
					</tr>
					<tr>
						<td><img src="/ligue/img/hbar.jpg" /></td>
						<td></td>
						<td><img src="/ligue/img/hbar.jpg" /></td>
					</tr>
					</table><br />';
				
				
				
			}
			
			//Pick Side
			if ($status[0] == 's') {
			
				if ($step == 1) {
				
					//Le cap1 choisit
					if (ArghSession::get_username() == $l->cap1) {
					
						echo '<center>
							<a href="javascript:PickSide(\'fp\', '.$game_id.');"><img src="/ligue/ladder/first_pick_small.jpg" alt="" /></a>&nbsp;
							<a href="javascript:PickSide(\'sp\', '.$game_id.');"><img src="/ligue/ladder/second_pick_small.jpg" alt="" /></a>
							<br /><br />
							<a href="javascript:PickSide(\'se\', '.$game_id.');"><img src="/ligue/ladder/se_small.jpg" alt="" /></a>&nbsp;
							<a href="javascript:PickSide(\'sc\', '.$game_id.');"><img src="/ligue/ladder/sc_small.jpg" alt="" /></a>
						</center>';
					} else {
					
						//Recuperation du choix du cap1
						//
					
						//Affichage standard pour les 9 autres joueurs
						echo '<center>
								<img src="/ligue/ladder/first_pick_small.jpg" alt="" />&nbsp;
								<img src="/ligue/ladder/second_pick_small.jpg" alt="" />
								<br /><br />
								<img src="/ligue/ladder/se_small.jpg" alt="" />&nbsp;
								<img src="/ligue/ladder/sc_small.jpg" alt="" />
							</center>';
					}
				

				} else {
				
					//Choix du cap 1
					$choice_type = in_array($l->cap1_side, array('fp', 'sp')) ? 'pick' : 'side';
					
					//Affichage Sides
					echo '<table width="100%">
						<colgroup>
							<col width="50%" />
							<col width="50%" />
						</colgroup>
						<tr>
							<td align="left">'.$pictures[$l->cap1_side].'</td>
							<td align="right">'.$opposites[$l->cap1_side].'</td>
						</tr></table><br />';
				
					//Le cap2 choisit
					if (ArghSession::get_username() == $l->cap2) {
					
						if ($choice_type == 'pick') {
					
							echo '<center>
								<a href="javascript:PickSide(\'se\', '.$game_id.');"><img src="/ligue/ladder/se_small.jpg" alt="" /></a>&nbsp;
								<a href="javascript:PickSide(\'sc\', '.$game_id.');"><img src="/ligue/ladder/sc_small.jpg" alt="" /></a>
							</center>';
						} else {
						
							echo '<center>
								<a href="javascript:PickSide(\'fp\', '.$game_id.');"><img src="/ligue/ladder/first_pick_small.jpg" alt="" /></a>&nbsp;
								<a href="javascript:PickSide(\'sp\', '.$game_id.');"><img src="/ligue/ladder/second_pick_small.jpg" alt="" /></a>
							</center>';
						}
					} else {
					
						if ($choice_type == 'pick') {
							//Affichage standard pour les 9 autres joueurs
							echo '<center>
									<img src="/ligue/ladder/se_small.jpg" alt="" />&nbsp;
									<img src="/ligue/ladder/sc_small.jpg" alt="" />
								</center>';
						}
					}
				}
				
				$remaining_time = $l->actiontime + $ACTIONTIME_HIGH - time();
				//$remaining_time = $l->actiontime + $PICK_TIME - time();
				if ($remaining_time < 0) $remaining_time = 0;
				echo '<br /><div style="font-weight: bold; display: inline;" id="timer">'.$remaining_time.'</div>'.Lang::SECOND_LETTER.' '.Lang::REMAINING;
				
				if ($remaining_time == 0) {
					//Timeout
					if ($step == 1) {
					
						//Cap AFK
					
						$availabilities = array('se', 'sc', 'fp', 'sp');
						$randomed = $availabilities[rand(0, 3)];
						$upd = "UPDATE lg_laddervip_games SET cap1_side = '".$randomed."', status = 's_picking2', actiontime = '".time()."' WHERE id = '".$game_id."'";
						
					} else {
						//Second choix : 2 choix sont disponibles
						if ($l->cap1_side == 'fp' || $l->cap1_side == 'sp') {
							$availabilities = array('se', 'sc');
						} else {
							$availabilities = array('fp', 'sp');
						}
						$randomed = $availabilities[rand(0, 1)];
						
						if ($l->cap1_side == 'sp' || $randomed == 'fp') {
							//On swap car c'est le cap1 qui FP
							$upd = "UPDATE lg_laddervip_games SET cap1_side = '".$randomed."', cap2_side = '".$l->cap1_side."', status = 'p_picking1', cap1 = '".$l->cap2."', cap2 = '".$l->cap1."', actiontime = '".time()."' WHERE id = '".$game_id."'";
						} else {
							$upd = "UPDATE lg_laddervip_games SET cap2_side = '".$randomed."', status = 'p_picking1', actiontime = '".time()."' WHERE id = '".$game_id."'";
						}
					}
					
					mysql_query($upd);
				}
			}
			
			//Player pick
			else if ($status[0] == 'p') {
			
				//Joueurs picked
				$pickedPlayers = array();
				$team_a = array();
				$team_b = array();
				for ($i = 1; $i < $step; $i++) {
					$ppl = 'pp'.$i;
					
					$team = getTeam($i);
					if ($team == 'A') {
						$team_a[] = $l->$ppl;
					} else {
						$team_b[] = $l->$ppl;
					}
					if ($l->$ppl != '') $pickedPlayers[$l->$ppl] = $team;
				}
			
				//Affichage Sides
				echo '<table width="100%">
					<colgroup>
						<col width="50%" />
						<col width="50%" />
					</colgroup>
					<tr>
						<td align="left">'.$pictures[$l->cap1_side].$opposites[$l->cap2_side].'</td>
						<td align="right">'.$opposites[$l->cap1_side].$pictures[$l->cap2_side].'</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>';
					
				echo '<tr><td align="left" valign="top">';
				foreach ($team_a as $player) {
					echo '<div class="elsenamebar">'.$player.'</div>';
				}
				
				echo '</td><td align="right" valign="top">';
				foreach ($team_b as $player) {
					echo '<div class="elsenamebar">'.$player.'</div>';
				}
				echo '</td></tr></table><br /><br />';
			
				echo '<center><b>'.Lang::POOL.'</b><br /><img src="img/hbar.jpg" alt="" /><br /><br />';
				
				for ($i = 1; $i <= 8; $i++) {
					$player = 'p'.$i;
					$link = '';
					
					//Add the "plus" img and link for captains
					if (ArghSession::get_username() == $l->cap1 and ($step == 1 || $step == 4 || $step == 5 || $step == 8)) {
						//Cap 1
						$link = '<div style="float: left;"><a href="javascript:PickPlayer(\''.$l->$player.'\', '.$game_id.', 1);"><img src="/ligue/img/plus.gif" width="20" height="20" /></a></div>&nbsp;-&nbsp;';
					} else if (ArghSession::get_username() == $l->cap2 and ($step == 2 || $step == 3 || $step == 6 || $step == 7)) {
						//Cap 2
						$link = '<div style="float: left;"><a href="javascript:PickPlayer(\''.$l->$player.'\', '.$game_id.', 2);"><img src="/ligue/img/plus.gif" width="20" height="20" /></a></div>&nbsp;-&nbsp;';
					}
					
					if (!array_key_exists($l->$player, $pickedPlayers)) {
						echo '<div class="elsenamebar">'.$link.'<div style="display: inline;"><a href="?f=player_profile&player='.$l->$player.'">'.$l->$player.'</a></div></div>';
					}
				}
				
				echo '</center>';
				
				//Etat
				echo '<br /><b>'.Lang::STATUS.'</b> ';
				
				switch ($step) {
					case 1:
					case 5:
					case 8:
						echo sprintf(Lang::LADDERVIP_TO_PICK_1_PLAYER, $l->cap1);
						break;
						
					case 2:
					case 6:	
						echo sprintf(Lang::LADDERVIP_TO_PICK_2_PLAYERS, $l->cap2);
						break;
						
					case 3:
					case 7:
						echo sprintf(Lang::LADDERVIP_TO_PICK_1_PLAYER, $l->cap2);
						break;
						
					case 4:
						echo sprintf(Lang::LADDERVIP_TO_PICK_2_PLAYERS, $l->cap1);
						break;
				}
				echo '<br />';
				
				//Temps restant
				$remaining_time = $l->actiontime + $ACTIONTIME_LOW - time();
				
				if ($remaining_time < 0) {
					$remaining_time = 0;
				}
				
				if ($remaining_time == 0) {
				
					//Picked players
					$picked_players = array();
					for ($i = 1; $i <= $step; $i++) {
						$picked_players[] = $l->{'pp'.$i};
					}
					
					//Available players
					$available_players = array();
					for ($i = 1; $i <= 8; $i++) {
						$available_players[] = $l->{'p'.$i};
					}
					$available_players = array_diff($available_players, $picked_players);
					shuffle($available_players);
					$randomed = array_pop($available_players);
					
					if ($step < 7) {
						mysql_query("
								UPDATE lg_laddervip_games
								SET pp".$step." = '".$randomed."',
								actiontime = '".time()."',
								status = 'p_picking".($step + 1)."'
								WHERE id = '".$game_id."'");
					} else {
						//Finalisation des picks
						$last = array_pop($available_players);
						
						mysql_query("
							UPDATE lg_laddervip_games
							SET pp7 = '".$randomed."',
							pp8 = '".$last."',
							actiontime = '".time()."',
							status = 'h_banning1'
							WHERE id = '".$game_id."'");
					}
				}
				echo '<br /><div style="font-weight: bold; display: inline;" id="timer">'.$remaining_time.'</div>'.Lang::SECOND_LETTER.' '.Lang::REMAINING;
				
			} else if ($status[0] == 'h') {
				//Heroes Pick
				$step = $status[strlen($status) - 1];
				if ($step == 0) $step = 10;
				
				$all_heroes = Heroes::get_sorted_heroes();
				
				//Affichage teams
				echo '<br /><table width="100%">
					<colgroup>
						<col width="48%" />
						<col width="4%" />
						<col width="48%" />
					</colgroup>
					<tr>
						<td align="left">'.$pictures[$l->cap1_side].$opposites[$l->cap2_side].'</td>
						<td></td>
						<td align="right">'.$opposites[$l->cap1_side].$pictures[$l->cap2_side].'</td>
					</tr>
					<tr>
						<td align="left"><div class="elsenamebar">'.$l->cap1.'</div></td>
						<td><center><div id="loader"><img src="img/black.jpg" alt="" /></div></center></td>
						<td align="right"><div class="elsenamebar">'.$l->cap2.'</div></td>
					</tr>
					<tr>
						<td align="left"><img src="img/hbar.jpg" alt="" /><br />&nbsp;</td>
						<td></td>
						<td align="right"><img src="img/hbar.jpg" alt="" /><br />&nbsp;</td>
					</tr>
					<tr>
						<td align="left"><div class="elsenamebar">'.$l->pp1.'</div></td>
						<td></td>
						<td align="right"><div class="elsenamebar">'.$l->pp2.'</div></td>
					</tr>
					<tr>
						<td align="left"><div class="elsenamebar">'.$l->pp4.'</div></td>
						<td></td>
						<td align="right"><div class="elsenamebar">'.$l->pp3.'</div></td>
					</tr>
					<tr>
						<td align="left"><div class="elsenamebar">'.$l->pp5.'</div></td>
						<td></td>
						<td align="right"><div class="elsenamebar">'.$l->pp6.'</div></td>
					</tr>
					<tr>
						<td align="left"><div class="elsenamebar">'.$l->pp8.'</div></td>
						<td></td>
						<td align="right"><div class="elsenamebar">'.$l->pp7.'</div></td>
					</tr>';
				
				
				//Affichage des heros picked
				$picked_heroes = array();
				if ($step > 1) {
					echo '<tr><td align="left">';
					echo '<img src="/ligue/img/heroes/'.$l->h1.'.gif" title="'.$l->h1.'" width="48" height="48" />&nbsp;';
					$picked_heroes[] = $l->h1;
					if ($step > 4) {
						echo '<img src="/ligue/img/heroes/'.$l->h4.'.gif" title="'.$l->h4.'" width="48" height="48" />&nbsp;';
						$picked_heroes[] = $l->h4;
					}
					if ($step > 5) {
						echo '<img src="/ligue/img/heroes/'.$l->h5.'.gif" title="'.$l->h5.'" width="48" height="48" />&nbsp;';
						$picked_heroes[] = $l->h5;
					}
					if ($step > 8) {
						echo '<img src="/ligue/img/heroes/'.$l->h8.'.gif" title="'.$l->h8.'" width="48" height="48" />&nbsp;';
						$picked_heroes[] = $l->h8;
					}
					if ($step > 9) {
						echo '<img src="/ligue/img/heroes/'.$l->h9.'.gif" title="'.$l->h9.'" width="48" height="48" />';
						$picked_heroes[] = $l->h9;
					}
					echo '</td><td></td><td align="right">';
					if ($step > 2) {
						echo '<img src="/ligue/img/heroes/'.$l->h2.'.gif" title="'.$l->h2.'" width="48" height="48" />&nbsp;';
						$picked_heroes[] = $l->h2;
					}
					if ($step > 3) {
						echo '<img src="/ligue/img/heroes/'.$l->h3.'.gif" title="'.$l->h3.'" width="48" height="48" />&nbsp;';
						$picked_heroes[] = $l->h3;
					}
					if ($step > 6) {
						echo '<img src="/ligue/img/heroes/'.$l->h6.'.gif" title="'.$l->h6.'" width="48" height="48" />&nbsp;';
						$picked_heroes[] = $l->h6;
					}
					if ($step > 7) {
						echo '<img src="/ligue/img/heroes/'.$l->h7.'.gif" title="'.$l->h7.'" width="48" height="48" />';
						$picked_heroes[] = $l->h7;
					}
					echo '</td>';
				}
				echo '</table><br />';
				
				//Affichage Bans
				echo '<br /><b>'.Lang::BANS.'</b><br /><center>';
				$banned_heroes = array($l->ban1, $l->ban3, $l->ban5, $l->ban7, $l->ban2, $l->ban4, $l->ban6, $l->ban8);
				foreach ($banned_heroes as $ban) {
					echo '<img src="/ligue/img/heroes/'.$ban.'.gif" title="'.$ban.'" width="48" height="48" />&nbsp;';
				}
				echo '</center>';
				
				$affiliations = array(
					'se' => Lang::SENTINEL,
					'ne' => Lang::NEUTRAL,
					'sc' => Lang::SCOURGE
				);
				
				/*
				//Heroes already banned
				$banned_heroes = array();
				$available_heroes = array();
				for ($j = 1; $j < 8; $j++) {
					$banned_heroes[] = $l->{'ban'.$j};
				}
				*/
				
				//Pool
				echo '<b>'.Lang::POOL.'</b><br />';
				echo '<table width="100%">
					<colgroup>
						<col width="33%" />
						<col width="33%" />
						<col width="33%" />
					</colgroup>';
				foreach ($all_heroes as $main_attribute => $array1) {
					echo '<tr><td colspan="3">&nbsp;</td></tr><tr>';
					foreach ($array1 as $affiliation => $array2) {
						echo '<td valign="top"><p align="center"><img src="img/attributes/'.$main_attribute.'.jpg" alt="" width="24" height="24" /> '.$affiliations[$affiliation].'</p>';
						$i = 0;
						foreach ($array2 as $hero) {
							if (!in_array($hero, $banned_heroes) && !in_array($hero, $picked_heroes)) {
								$available_heroes[] = $hero;
								$picture = '<img src="img/heroes/'.$hero.'.gif" title="'.$hero.'" width="48" height="48" />';
								if ((ArghSession::get_username() == $l->cap1 and ($step == 1 || $step == 4 || $step == 5 || $step == 8 || $step == 9))
									or (ArghSession::get_username() == $l->cap2 and ($step == 2 || $step == 3 || $step == 6 || $step == 7 || $step == 10))) {
									//Captains
									echo '<a href="javascript:PickHero(\''.$hero.'\');">'.$picture.'</a>';
								} else {
									//Players
									echo $picture;
								}
								
								if (++$i % 4 == 0) echo '<br />';
							}
						}
						echo '</td>';
					}
				}
				echo '</table><br />';
				
				echo '<b>'.Lang::STATUS.'</b><br />';
				switch ($step) {
					case 1:
					case 5:
					case 9:
						echo sprintf(Lang::LADDERVIP_TO_PICK_1_HERO,  $l->cap1);
						break;
						
					case 2:
					case 6:
						echo sprintf(Lang::LADDERVIP_TO_PICK_2_HEROES,  $l->cap2);
						break;
						
					case 3:
					case 7:
					case 10:
						echo sprintf(Lang::LADDERVIP_TO_PICK_1_HERO,  $l->cap2);
						break;
						
					case 4:
					case 8:
						echo sprintf(Lang::LADDERVIP_TO_PICK_2_HEROES,  $l->cap1);
						break;
				}
				
				//Temps restant
				$two_heroes_pick = array(2, 4, 6, 8);
				$single_pick = array(1, 10);
				$pick_time = (in_array($step, $single_pick)) ? $PICK_TIME : 2 * $PICK_TIME;
				$remaining_time = $l->actiontime + $pick_time - time();
				if ($remaining_time < 0) {
					$remaining_time = 0;
					
					//Pick Aléatoire
					$available_heroes = Heroes::get_heroes();
					$available_heroes = array_diff($available_heroes, $banned_heroes);
					$available_heroes = array_diff($available_heroes, $picked_heroes);
					shuffle($available_heroes);
					$randomed_hero = array_pop($available_heroes);
					
					if (in_array($step, $two_heroes_pick)) {
						//Must pick 2 heroes
						$status = 'h_picking'.($step == 8 ? 0 : $step + 2);
						$randomed_hero_2 = array_pop($available_heroes);
						$req = "UPDATE lg_laddervip_games
								SET h".$step." = '".$randomed_hero."', h".($step + 1)." = '".$randomed_hero_2."', status = '".$status."', actiontime = '".time()."'
								WHERE id = '".$game_id."'";
					} else {
						$status = ($step == 10) ? LadderStates::PLAYING : 'h_picking'.($step == 9 ? 0 : $step + 1);
						$req = "UPDATE lg_laddervip_games
								SET h".$step." = '".$randomed_hero."', status = '".$status."', actiontime = '".time()."'
								WHERE id = '".$game_id."'";
					}
					//echo '<br />'.$req.'<br />';
					mysql_query($req);

				}
				echo '<br /><div style="font-weight: bold; display: inline;" id="timer">'.$remaining_time.'</div>'.Lang::SECOND_LETTER.' '.Lang::REMAINING;
			}
			
		} elseif ($redStatus == LadderStates::BANNING) {
			echo '<center><div id="btn_refresh"><a href="javascript:Refresh(1);"><img src="ladder/btn_refresh.jpg" title="" /></a></div></center>';
			echo '<br /><b>'.Lang::LADDERVIP_BAN_PHASE.'</b><br />';
			
			//Ban Héros
			$step = $status[strlen($status) - 1];
			
			$all_heroes = Heroes::get_sorted_heroes();
			
			//Affichage teams
			echo '<br /><table width="100%">
				<colgroup>
					<col width="48%" />
					<col width="4%" />
					<col width="48%" />
				</colgroup>
				<tr>
					<td align="left">'.$pictures[$l->cap1_side].$opposites[$l->cap2_side].'</td>
					<td></td>
					<td align="right">'.$opposites[$l->cap1_side].$pictures[$l->cap2_side].'</td>
				</tr>
				<tr>
					<td align="left"><div class="elsenamebar">'.$l->cap1.'</div></td>
					<td><center><div id="loader"><img src="img/black.jpg" alt="" /></div></center></td>
					<td align="right"><div class="elsenamebar">'.$l->cap2.'</div></td>
				</tr>
				<tr>
					<td align="left"><img src="img/hbar.jpg" alt="" /><br />&nbsp;</td>
					<td></td>
					<td align="right"><img src="img/hbar.jpg" alt="" /><br />&nbsp;</td>
				</tr>
				<tr>
					<td align="left"><div class="elsenamebar">'.$l->pp1.'</div></td>
					<td></td>
					<td align="right"><div class="elsenamebar">'.$l->pp2.'</div></td>
				</tr>
				<tr>
					<td align="left"><div class="elsenamebar">'.$l->pp4.'</div></td>
					<td></td>
					<td align="right"><div class="elsenamebar">'.$l->pp3.'</div></td>
				</tr>
				<tr>
					<td align="left"><div class="elsenamebar">'.$l->pp5.'</div></td>
					<td></td>
					<td align="right"><div class="elsenamebar">'.$l->pp6.'</div></td>
				</tr>
				<tr>
					<td align="left"><div class="elsenamebar">'.$l->pp8.'</div></td>
					<td></td>
					<td align="right"><div class="elsenamebar">'.$l->pp7.'</div></td>
				</tr>';
			echo '</table>';
			
			//Bans
			if ($step > 1) {
				echo '<b>'.Lang::BANS.'</b><br /><br />
				<table width="100%">
					<colgroup>
						<col width="50%" />
						<col width="50%" />
					</colgroup>
					<tr>
						<td align="left"><img src="/ligue/img/heroes/'.$l->ban1.'.gif" title="'.$l->ban1.'" width="48" height="48" />';
				if ($step > 3) {
					echo '&nbsp;<img src="/ligue/img/heroes/'.$l->ban3.'.gif" title="'.$l->ban3.'" width="48" height="48" />';
				}
				if ($step > 5) {
					echo '&nbsp;<img src="/ligue/img/heroes/'.$l->ban5.'.gif" title="'.$l->ban5.'" width="48" height="48" />';
				}
				if ($step > 7) {
					echo '&nbsp;<img src="/ligue/img/heroes/'.$l->ban7.'.gif" title="'.$l->ban7.'" width="48" height="48" />';
				}
				echo '</td><td align="right">';
				if ($step > 2) {
					echo '&nbsp;<img src="/ligue/img/heroes/'.$l->ban2.'.gif" title="'.$l->ban2.'" width="48" height="48" />';
				}
				if ($step > 4) {
					echo '&nbsp;<img src="/ligue/img/heroes/'.$l->ban4.'.gif" title="'.$l->ban6.'" width="48" height="48" />';
				}
				if ($step > 6) {
					echo '&nbsp;<img src="/ligue/img/heroes/'.$l->ban6.'.gif" title="'.$l->ban4.'" width="48" height="48" />';
				}
				echo '</td></tr>
				</table><br /><br />';
			}
			
			//Hero Hover
			//echo '<div id="hero_hover"></div><br />';
			
			$affiliations = array(
				'se' => Lang::SENTINEL,
				'ne' => Lang::NEUTRAL,
				'sc' => Lang::SCOURGE
			);
			
			//Heroes already banned
			$banned_heroes = array();
			$available_heroes = array();
			for ($j = 1; $j < $step; $j++) {
				$hero = 'ban'.$j;
				$banned_heroes[] = $l->{'ban'.$j};
			}
			
			//Pool
			echo '<b>'.Lang::POOL.'</b><br />';
			echo '<table width="100%">
				<colgroup>
					<col width="33%" />
					<col width="33%" />
					<col width="33%" />
				</colgroup>';
			foreach ($all_heroes as $main_attribute => $array1) {
				echo '<tr><td colspan="3">&nbsp;</td></tr><tr>';
				foreach ($array1 as $affiliation => $array2) {
					echo '<td valign="top"><p align="center"><img src="img/attributes/'.$main_attribute.'.jpg" alt="" width="24" height="24" /> '.$affiliations[$affiliation].'</p>';
					$i = 0;
					foreach ($array2 as $hero) {
						if (!in_array($hero, $banned_heroes)) {
							$available_heroes[] = $hero;
							$picture = '<img src="img/heroes/'.$hero.'.gif" title="'.$hero.'" width="48" height="48" />';
							if ((ArghSession::get_username() == $l->cap1 and ($step == 1 || $step == 3 || $step == 5 || $step == 7))
								or (ArghSession::get_username() == $l->cap2 and ($step == 2 || $step == 4 || $step == 6 || $step == 8))) {
								//Captains
								echo '<a href="javascript:BanHero(\''.$hero.'\');">'.$picture.'</a>';
							} else {
								//Players
								echo $picture;
							}
							
							if (++$i % 4 == 0) echo '<br />';
						}
					}
					echo '</td>';
				}
			}
			echo '</table><br />';
			
			echo '<b>'.Lang::STATUS.'</b><br />';
			switch ($step) {
				case 1:
				case 3:
				case 5:
				case 7:
					echo sprintf(Lang::LADDERVIP_TO_BAN_1_HERO, $l->cap1);
					break;
					
				case 2:
				case 4:
				case 6:
				case 8:
					echo sprintf(Lang::LADDERVIP_TO_BAN_1_HERO, $l->cap2);
					break;
			}
			
			//Temps restant
			$remaining_time = $l->actiontime + $ACTIONTIME_LOW - time();
			if ($remaining_time < 0) {
				//Random Ban
				$randomed_hero = $available_heroes[rand(0, count($available_heroes) - 1)];
				$status = ($step == 8) ? 'h_picking1' : 'h_banning'.($step + 1);
				
				$req = "UPDATE lg_laddervip_games
						SET ban".$step." = '".$randomed_hero."',
						actiontime = '".time()."',
						status = '".$status."'
						WHERE id = '".$game_id."'";
				mysql_query($req);
					
				$remaining_time = 0;
			}
			echo '<br /><div style="font-weight: bold; display: inline;" id="timer">'.$remaining_time.'</div>'.Lang::SECOND_LETTER.' '.Lang::REMAINING.'<br />';
		}
		
		if ($l->status == LadderStates::ADMIN_OPENED) {
			echo '<center><span class="lose">'.Lang::LADDER_ADMIN_OPENED_GAME.'</span></center><br />';
		}
		
		ArghPanel::end_tag();
				
		require 'classes/ReportModule.php';
		
		ArghPanel::begin_tag(Lang::REPORT_GAME_REPORT);
		$report = new Report(true);
		$report->_game_id = $game_id;
		$report->load();
		if ($report->_status == Report::STATUS_NO_REPORT) {
			echo '<center><a href="?f=ladder_report&vip=true&id='.$game_id.'">'.Lang::REPORT_OPEN.'</a></center>';
		} else {
			echo '<center><img src="img/icons/information.png" alt="" />&nbsp;<a href="?f=ladder_report&vip=true&id='.$game_id.'">'.sprintf(Lang::REPORT_REPORT_OPENED_BY, $report->_initiator).'</a></center>';
		}
		
		ArghPanel::end_tag();
				
		//Admin
		if (ArghSession::is_rights(array(RightsMode::VIP_HEADADMIN, RightsMode::VIP_ADMIN))) {
			ArghPanel::begin_tag(Lang::LADDER_GAME_ADMINISTRATION);
			echo '<b>'.Lang::LADDER_VOTES_INFORMATION.'</b><br /><br />';
			
			$sreq = "SELECT * FROM lg_laddervip_playersreports WHERE game_id = '".$game_id."' ORDER BY qui ASC";
			$st = mysql_query($sreq);
			$j = 0;
			if (mysql_num_rows($st) > 0) {
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
				while ($sl = mysql_fetch_object($st)) {
					if ($sl->info == 1) {
						$info = Lang::LEAVER;
					} elseif ($sl->info == 2) {
						$info = Lang::AWAY;
					} else {
						$info = Lang::BEHAVIOR;
					}
					
					$who = ($l->qui == 'Admin') ? '<span class="vip">'.$sl->qui.'</span>' : '<a href="?f=player_profile&player='.$sl->qui.'">'.$sl->qui.'</a>';
					
					echo '<tr'.Alternator::get_alternation($j).'>
						<td>'.$who.'</td>
						<td><a href="?f=player_profile&player='.$sl->pour_qui.'">'.$sl->pour_qui.'</a></td>
						<td>'.$info.'</td>
					</tr>';
					//echo '<tr><td>'.$sl->qui.'</td><td>'.$sl->pour_qui.'</td><td>'.$info.'</td></tr>';
				}
				
				echo '</tbody>
					</table>';
				
			} else {
				echo '<center>'.Lang::NO_VOTE.'</center>';
			}
			
			echo '<br />
			<form action="?f=laddervip_game&id='.$game_id.'" method="POST">
				<input type="submit" name="subm_empty" value="'.Lang::LADDER_EMPTY_VOTES.'" /><br/>
				<span class="info">'.Lang::LADDER_EMPTY_VOTES_EXPLANATION.'</span>
			</form>';
			
			echo '<br />
			<form action="?f=laddervip_game&id='.$game_id.'" method="POST">
				<select name="what">';
				foreach (LadderStates::$PLAYERS_INFOS as $key => $val) {
					echo '<option value="'.$key.'">'.$val.'</option>';
				}
				echo '</select><select name="leaveraway">';
				sort($players);
				foreach($players as $player) {
					echo '<option value="'.$player.'">'.$player.'</option>';
				}
				echo '</select>&nbsp;<input type="submit" name="subm_forcevotes" value="'.Lang::LADDER_FORCE_VOTES.'" /><br/>
				<span class="info">'.Lang::LADDER_FORCE_VOTES_EXPLANATION.'</span>
			</form>';
			
			echo '<br /><b>'.Lang::LADDER_GAME_ADMINISTRATION.'</b><br />';
			echo '<form action="?f=laddervip_game&id='.$game_id.'" method="POST">
				<input type="submit" name="subm_clear" value="'.Lang::LADDER_CANCEL_RESULT.'" /><br />
				<span class="info">'.Lang::LADDER_CANCEL_RESULT_EXPLANATION.'</span>
			</form>';
			
			/*
				<select name="winner">
					<option value="none">'.Lang::NONE.'</option>
					<option value="se">'.Lang::SENTINEL.'</option>
					<option value="sc">'.Lang::SCOURGE.'</option>
				</select><br />
			*/
			
			echo '<br />
			<form action="?f=laddervip_game&id='.$game_id.'" method="POST">
				<input type="submit" name="subm_result" value="'.Lang::LADDER_FORCE_RESULT.'" />
				<select name="winner">
					<option value="none">'.Lang::NONE.'</option>
					<option value="se">'.Lang::TEAM.' 1</option>
					<option value="sc">'.Lang::TEAM.' 2</option>
				</select><br />
				<span class="info">'.Lang::LADDER_FORCE_RESULT_EXPLANATION.'</span>
			</form>';
			
			ArghPanel::end_tag();
		}
	}
?>
