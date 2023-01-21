<?php
	abstract class GameReporter {
	
		const XP_WIN 						= 19;
		const XP_LOSE 						= -20;
		const XP_AWAY 						= -10;
		const XP_COEFF_WINNER_WITH_LEAVER 	= 1.25;
		const XP_COEFF_LOSER_WITH_LEAVER 	= 0.35;
		const XP_LEAVER 					= -20;
		
		const MIN_VOTES = 5;
		
		const SENTINEL	= 'se';
		const SCOURGE	= 'sc';
		const NO_WINNER	= 'none';
		
		const WIN 		= 'win';
		const LOSE 		= 'lose';
		const LEFT		= 'left';
		const AWAY	 	= 'away';
		
		const AWAY_AUTO_BAN = 0.2;
		
		public static function report($game_id, $winner_side, $resimulate = false) {
		
			//On vérifie que la game n'a pas déjà été report
			$req = "SELECT status FROM lg_laddergames WHERE id = '".$game_id."'";
			$t = mysql_query($req);
			if (mysql_num_rows($t) > 0) {
				$l = mysql_fetch_row($t);
				$status = $l[0];
				if ($status == LadderStates::CLOSED || $status == LadderStates::REPORTING || ($status == LadderStates::ADMIN_OPENED && !ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN)))) {
					return;
				}
			} else {
				//Verrou
				$upd = "UPDATE lg_laddergames SET status = '".LadderStates::REPORTING."' WHERE id = '".$game_id."'";
				mysql_query($upd);
			}
			
			//Joueurs de la game, avec xp
			$req = '';
			for ($i = 1; $i <= 10; $i++) {
				if ($i > 1) {
					$req .= "\nUNION ";
				}
				//$req .= "SELECT l.p".$i.", ".($resimulate ? 'l.xp'.$i : 'u.pts')." FROM lg_laddergames l, lg_users u WHERE l.id = '".$game_id."' AND l.p".$i." = u.username";
				$req .= "SELECT l.p".$i.", l.xp".$i." FROM lg_laddergames l, lg_users u WHERE l.id = '".$game_id."' AND l.p".$i." = u.username";
			}
			$players = array();
			$t = mysql_query($req);
			$j = 0;
			while ($l = mysql_fetch_row($t)) {
			
				//Coeff modulation
				$MOD_A = 20;
				$MOD_B = 0.04;
				
				$modulation = (1.0 / (get_nb_games($l[0]) + $MOD_A)) + $MOD_B;
			
				$players[] = array(
					'slot' => ++$j,
					'user' => $l[0],
					'xp' => $l[1],
					'xp_gain' => 0,
					'modulation' => $modulation,
					'xp_adapt' => xp_adapt($l[1]),
					'state' => ''
				);
			}
			
			//Winners & Losers
			switch ($winner_side) {
				case self::SENTINEL:
					for ($i = 0; $i <= 4; $i++) $winners[] = &$players[$i];
					for ($i = 5; $i <= 9; $i++) $losers[] = &$players[$i];
					
					break;
					
				case self::SCOURGE:
					for ($i = 0; $i <= 4; $i++) $losers[] = &$players[$i];
					for ($i = 5; $i <= 9; $i++) $winners[] = &$players[$i];
					
					break;
					
				case self::NO_WINNER:
					
					//On fout tout arbitrairement dans winners
					for ($i = 0; $i <= 9; $i++) $winners[] = &$players[$i];
					break;
					
				default:
					return;
			}
			
			$leavers_in_winner_team = array();
			$leavers_in_loser_team = array();
			$aways = array();
			
			//Leavers et Aways
			$req = "SELECT pour_qui, info, COUNT(info)
					FROM lg_playersreports
					WHERE game_id = '".$game_id."'
					GROUP BY info, pour_qui";
			$t = mysql_query($req);
			
			if (mysql_num_rows($t)) {
				while ($l = mysql_fetch_row($t)) {
					$done = false;
					if ($l[2] >= self::MIN_VOTES) {
						foreach ($winners as $key => $winner) {
							if ($winner['user'] == $l[0]) {
								if ($l[1] == 1) {
									$leavers_in_winner_team[] = &$winners[$key];
								} elseif ($l[1] == 2) {
									$aways[] = &$winners[$key];
								}
								//Enlever mauvais comportement
								//unset($winner);
								$done = true;
								break;
							}
						}
						if (!$done) {
							foreach ($losers as $key => $loser) {
								if ($loser['user'] == $l[0]) {
									if ($l[1] == 1) {
										$leavers_in_loser_team[] = &$losers[$key];
									} elseif ($l[1] == 2) {
										$aways[] = &$losers[$key];
									}
									//unset($loser);
									break;
								}
							}
						}
					}
				}
			}
			
			$count_leavers_in_winner_team = count($leavers_in_winner_team);
			$count_leavers_in_loser_team = count($leavers_in_loser_team);
			
			if ($winner_side != self::NO_WINNER) {
			
				$winners_total = 0;
				$losers_total = 0;
				$winners_mod_total = 0;
				$losers_mod_total = 0;
				
				foreach ($winners as $key => $winner) {
					$winners_total += $winners[$key]['xp'];
					$winners_mod_total += $winners[$key]['modulation'];
				}
				foreach ($losers as $key => $loser) {
					$losers_total += $losers[$key]['xp'];
					$losers_mod_total += $losers[$key]['modulation'];
				}
				
				$diff = $losers_total - $winners_total;
				
				//$xp_this_game = max(min(0.016 * ($losers_total - $winners_total) + 36, 60), 12);
				
				foreach ($winners as $key => $winner) {
					$winners[$key]['state'] = self::WIN;
					$winners[$key]['xp_gain'] = $winners[$key]['xp_adapt'] * 5 * self::XP_WIN * $winners[$key]['modulation'] / $winners_mod_total;
				}
				
				foreach ($losers as $key => $loser) {
					$losers[$key]['state'] = self::LOSE;
					$losers[$key]['xp_gain'] = $losers[$key]['xp_adapt'] * 5 * self::XP_LOSE * $losers[$key]['modulation'] / $losers_mod_total;
				}
				
				foreach ($leavers_in_winner_team as $key => $leaver) {
					$leavers_in_winner_team[$key]['state'] = self::LEFT;
					$leavers_in_winner_team[$key]['xp_gain'] = $winners[$key]['xp_adapt'] * (5 * self::XP_WIN * $winners[$key]['modulation'] / $winners_mod_total) + self::XP_LEAVER;
				}
				
				foreach ($leavers_in_loser_team as $key => $leaver) {
					$leavers_in_loser_team[$key]['state'] = self::LEFT;
					$leavers_in_loser_team[$key]['xp_gain'] = $losers[$key]['xp_adapt'] * (5 * self::XP_LOSE * $losers[$key]['modulation'] / $losers_mod_total) + self::XP_LEAVER;
				}
				
				//Correctifs 4v5... + 3v5
				if (($count_leavers_in_winner_team >= 1) && $count_leavers_in_loser_team == 0) {
					//Victoire en inferiorite numerique 4v5
					//Bonus pour les vainqueurs
					foreach ($winners as $key => $winner) {
						$do = true;
						foreach ($leavers_in_winner_team as $leaver) {
							if ($winner['user'] == $leaver['user']) {
								$do = false;
								break;
							}
						}
						if ($do) {
							$winners[$key]['xp_gain'] *= self::XP_COEFF_WINNER_WITH_LEAVER;
						}
					}
				} elseif (($count_leavers_in_winner_team == 0 && $count_leavers_in_loser_team >= 1) 
						|| ($count_leavers_in_winner_team == 1 && $count_leavers_in_loser_team == 1)) {
					//Défaite en inferiorite numerique 4v5 ou 4v4
					//Compensation pour les perdants
					foreach ($losers as $key => $loser) {
						$do = true;
						foreach ($leavers_in_loser_team as $leaver) {
							if ($loser['user'] == $leaver['user']) {
								$do = false;
								break;
							}
						}
						if ($do) {
							$losers[$key]['xp_gain'] *= self::XP_COEFF_LOSER_WITH_LEAVER;
						}
					}
				}
			} else {
				foreach ($aways as $key => $away) {
					$aways[$key]['state'] = self::AWAY;
					$aways[$key]['xp_gain'] = self::XP_AWAY;
				}
				foreach ($leavers_in_winner_team as $key => $leaver) {
					$leavers_in_winner_team[$key]['state'] = self::LEFT;
					$leavers_in_winner_team[$key]['xp_gain'] = $winners[$key]['xp_gain'] + self::XP_LEAVER;
				}
			}
			
			//print_r($aways);
			
			//Follow
			foreach ($players as $key => $player) {
				$players[$key]['xp_gain'] = round($player['xp_gain']);
				$req = "INSERT INTO lg_ladderfollow (player, game_id, resultat, xp)
						VALUES ('".$player['user']."', '".$game_id."', '".$player['state']."', '".$players[$key]['xp_gain']."')";
				mysql_query($req);
				
				$req = "UPDATE lg_users SET pts = ".$players[$key]['xp_gain']." + pts WHERE username = '".$player['user']."'";
				mysql_query($req);
				
				//Ban Auto Away
				if ($player['state'] == self::AWAY) {
					BanManager::ban($player['user'], self::AWAY_AUTO_BAN, 'AutoBan game #'.$game_id, 'LadderGuardian');
				}
			}
			
			$req = "UPDATE lg_laddergames
					SET status = '".LadderStates::CLOSED."',
						winner = '".$winner_side."',
						when_closed = '".time()."',
						b1 = '".$players[0]['xp_gain']."',
						b2 = '".$players[1]['xp_gain']."',
						b3 = '".$players[2]['xp_gain']."',
						b4 = '".$players[3]['xp_gain']."',
						b5 = '".$players[4]['xp_gain']."',
						b6 = '".$players[5]['xp_gain']."',
						b7 = '".$players[6]['xp_gain']."',
						b8 = '".$players[7]['xp_gain']."',
						b9 = '".$players[8]['xp_gain']."',
						b10 = '".$players[9]['xp_gain']."'
					WHERE id = '".$game_id."'";
			mysql_query($req);
			
			// Delete dans lg_ladder_stats
			mysql_query("DELETE FROM lg_ladder_stats WHERE game_id = '".$game_id."'");
			
			//Refund
			if ($winner_side == self::NO_WINNER && $status == LadderStates::PLAYING) {
				foreach ($players as $key => $player) {
					$query = "UPDATE lg_users SET daily_games = daily_games + 1 WHERE username = '".$player['user']."'";
					mysql_query($query);
				}
			}
		}
	}
	
	function xp_adapt($xp_self) {
		return exp((1600 - $xp_self) / 25000);
	}
	
	function streak($player) {
		$req = "SELECT * FROM lg_ladderfollow WHERE player = '".$player."' ORDER BY game_id DESC";
		$t = mysql_query($req);
		$streak = 0;
		$last = 0;
		if ($l = mysql_fetch_object($t)) {
			if ($l->xp > 0) {
				$streak++;
				$last = 1;
			} elseif ($l->xp < 0) {
				$streak--;
				$last = -1;
			}
		}
		
		while ($l = mysql_fetch_object($t)) {
			if ($l->xp > 0 && $last >= 0) $streak++;
			if ($l->xp < 0 && $last <= 0) $streak--;
			if (($l->xp > 0 && $last < 0) || ($l->xp < 0 && $last > 0)) return $streak;
			if ($l->xp > 0) $last = 1;
			elseif ($l->xp < 0) $last = -1;
		}
		return $streak;
	}
	
	
	function WinningStreak($streak) {
			if ($streak == 3) return Lang::STREAK_3;
			if ($streak == 4) return Lang::STREAK_4;
			if ($streak == 5) return Lang::STREAK_5;
			if ($streak == 6) return Lang::STREAK_6;
			if ($streak == 7) return Lang::STREAK_7;
			if ($streak == 8) return Lang::STREAK_8;
			if ($streak == 9) return Lang::STREAK_9;
			if ($streak >= 10 and $streak < 20) return Lang::STREAK_10;
			if ($streak >= 20) return Lang::STREAK_20;
	}
	

	function blocMsg($game_id, $action) {
	
		//Vérif
		$canPost = false;
		$req = "SELECT * FROM lg_laddergames WHERE id = '".$game_id."'";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);
		for ($i = 1; $i <= 10; $i++) {
			$pl = 'p'.$i;
			if (ArghSession::get_username() == $l->$pl) {
				$canPost = true;
				break;
			}
		}
		
		if ($canPost or ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN))) {
			require_once 'FCKeditor/fckeditor.php';
			if (ArghSession::is_logged()) {
				
				ArghPanel::begin_tag(Lang::MESSAGE_ADDING);
				echo '<form method="POST" action="'.$action.'" onSubmit="bouton_envoi.disabled=true;">
				<input type="hidden" name="game_id" value="'.$game_id.'" /><br />';
				
				$oFCKeditor = new FCKeditor('FCKLadder');
				$oFCKeditor->BasePath = '/ligue/FCKeditor/';
				$oFCKeditor->ToolbarSet = 'Basic';
				$oFCKeditor->Width = '100%';
				$oFCKeditor->Height = '150';
				$oFCKeditor->Create();

				echo '<br /><br /><center><input type="submit" value="'.Lang::VALIDATE.'" name="bouton_envoi" /></center>';
			}
			echo '</form>';
			ArghPanel::end_tag();
		}
	}
	
	function clearGame($game_id) {
		$req = "SELECT * FROM lg_laddergames WHERE id = '".$game_id."'";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);
		for ($i = 1; $i <= 10; $i++) {
			$pl = 'p'.$i;
			$bo = 'b'.$i;
			mysql_query("UPDATE lg_users SET pts = pts - ".$l->$bo." WHERE username = '".$l->$pl."'");
			mysql_query("UPDATE lg_laddergames SET ".$bo." = '0' WHERE id = '".$game_id."'");
		}
		mysql_query("UPDATE lg_laddergames SET status = '".LadderStates::ADMIN_OPENED."' WHERE id = '".$game_id."'");
		mysql_query("DELETE FROM lg_ladderfollow WHERE game_id = '".$game_id."'");
	}
	
	function handleMsg($game_id) {
		if (isset($_POST['FCKLadder']) && strlen($_POST['FCKLadder']) >= 1) {
			$texte = mysql_real_escape_string(eregi_replace("<script[^>]*>(.|\n)*script>(\r\n)?", "", $_POST['FCKLadder']));
			
			$req = "SELECT *
					FROM lg_laddercomment
					WHERE game_id = '".$game_id."'
					AND qui = '".ArghSession::get_username()."'
					AND quoi = '".$texte."'";
			$t = mysql_query($req);
			if (mysql_num_rows($t) == 0) {
				$ins = "INSERT INTO lg_laddercomment (qui, quand, quoi, game_id)
						VALUES ('".ArghSession::get_username()."', '".time()."', '".$texte."', '".$game_id."')";
				mysql_query($ins);
			}
		}
	}
	
	function listMsg($game_id) {
		$req = "SELECT * FROM lg_laddercomment WHERE game_id = '".$game_id."' ORDER BY id ASC";
		$t = mysql_query($req);
		
		if (mysql_num_rows($t) > 0) {
			ArghPanel::begin_tag(Lang::MESSAGES);
?>
			<table class="listing">
				<colgroup>
					<col width="25%" />
					<col width="75%" />
				</colgroup>
				<tbody>
<?php
			$i = 0;
			while ($l = mysql_fetch_object($t)) {
				echo '<tr'.Alternator::get_alternation($i).'>
					<td><b>'.$l->qui.'</b><br /><span class="info">'.date(Lang::DATE_FORMAT_HOUR, $l->quand).'</span></td>
					<td>'.stripslashes($l->quoi).'</td>
				</tr>';
			}
?>
				</tbody>
			</table>
<?php
			ArghPanel::end_tag();
		}
	}
	
	function blocLastGames($player, $limit) {
		//derniers matchs
		ArghPanel::begin_tag(Lang::LAST_GAMES);
		$limit = (int)$limit;
		$req = "SELECT l.when_closed, f.player, f.resultat, f.xp, l.id
				FROM lg_laddergames l, lg_ladderfollow f
				WHERE l.id = f.game_id
				AND status = '".LadderStates::CLOSED."'
				AND f.player = '".$player."'
				ORDER BY id DESC
				LIMIT ".$limit;
				
		$t = mysql_query($req);
		
		if (mysql_num_rows($t) > 0) {
			echo '<table class="listing">
				<colgroup>
				</colgroup>
				<thead>
					<tr>
						<th>'.Lang::DATE.'</th>
						<th>'.Lang::RESULT.'</th>
						<th>'.Lang::XP.'</th>
						<th>'.Lang::LINK.'</th>
					</tr>
					<tr>
						<td colspan="4" class="line">&nbsp;</td>
					</tr>
				</thead>
				<tbody>';
			$i = 0;
			while ($l = mysql_fetch_row($t)) {
				if ($l[3] > 0) {
					$result = '<span class="win">'.Lang::WIN.'</span>';
					$score = '<span class="win">+'.$l[3].'</span>';
				} elseif ($l[3] == 0) {
					$result = Lang::GAME_CLOSED;
					$score = '0';
				} elseif ($l[3] < 0 && $l[2] == 'left') {
					$result = '<span class="draw">'.Lang::LEFT.'</span>';
					$score = '<span class="draw">'.$l[3].'</span>';
				} elseif ($l[3] < 0 && $l[2] == 'away') {
					$result = '<span class="info">'.Lang::NOT_SHOW_UP.'</span>';
					$score = '<span class="info">'.$l[3].'</span>';
				} else {
					$result = '<span class="lose">'.Lang::LOSS.'</span>';
					$score = '<span class="lose">'.$l[3].'</span>';
				}
				echo '<tr'.Alternator::get_alternation($i).'>
						<td>'.date(Lang::DATE_FORMAT_HOUR,$l[0]).'</td>
						<td>'.$result.'</td>
						<td><b>'.$score.'</b></td>
						<td><a href="?f=ladder_game&id='.$l[4].'">'.Lang::GAME_SHARP.$l[4].'</a></td>
					</tr>';
			}
			
			echo '</tbody></table>';
		} else {
			echo '<center>'.Lang::NO_GAME.'</center>';
		}
		ArghPanel::end_tag();
	}

	function getNextGameInfos() {
		$req = "SELECT id, opened FROM lg_laddergames ORDER BY id DESC LIMIT 1";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return array($l[0], time() - $l[1]);
	}
	
	function getStatus($player) {
		$req = "SELECT ladder_status FROM lg_users WHERE username='".$player."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0];
	}
	
	function getGGC($player) {
		$req = "SELECT ggc FROM lg_users WHERE username='".$player."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0];
	}
	
/*
	function getClanTag($player) {
		$req = "SELECT c.tag
				FROM lg_clans c, lg_users u
				WHERE u.clan = c.id
				AND u.username='".$player."'";
		$t = mysql_query($req);
		if (mysql_num_rows($t) == 1) {
			$l = mysql_fetch_row($t);
			return $l[0];
		}
	}
*/
	
	function getGameStatus($game_id) {
		$req = "SELECT status FROM lg_laddergames WHERE id='".$game_id."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0];
	}
	
	function XPColor($xp) {
		if ($xp >= 6000) {
			return '#444444';
		}elseif ($xp >= 5000 && $xp < 6000) {
			return '#B000B0';
		} elseif ($xp >= 4000 && $xp < 5000) {
			return '#0099FF';
		} elseif ($xp >= 3000 && $xp < 4000) {
			return '#FFCC00';
		} elseif ($xp >= 2400 && $xp < 3000) {
			return '#19FF19';
		} elseif ($xp < 2400 && $xp >= 2300) {
			return '#32FF32';
		} elseif ($xp < 2300 && $xp >= 2200) {
			return '#4BFF4B';
		} elseif ($xp < 2200 && $xp >= 2100) {
			return '#64FF64';
		} elseif ($xp < 2100 && $xp >= 2000) {
			return '#7DFF7D';
		} elseif ($xp < 2000 && $xp >= 1900) {
			return '#96FF96';
		} elseif ($xp < 1900 && $xp >= 1800) {
			return '#AFFFAF';
		} elseif ($xp < 1800 && $xp >= 1700) {
			return '#C8FFC8';
		} elseif ($xp < 1700 && $xp >= 1600) {
			return '#E0FFE0';
		} elseif ($xp < 1600 && $xp >= 1500) {
			return '#FFFFFF';
		} elseif ($xp < 1500 && $xp >= 1400) {
			return '#FFE0E0';
		} elseif ($xp < 1400 && $xp >= 1300) {
			return '#FFC8C8';
		} elseif ($xp < 1300 && $xp >= 1200) {
			return '#FFAFAF';
		} elseif ($xp < 1200 && $xp >= 1100) {
			return '#FF9696';
		} elseif ($xp < 1100 && $xp >= 1000) {
			return '#FF7D7D';
		} elseif ($xp < 1000 && $xp >= 900) {
			return '#FF6464';
		} elseif ($xp < 900 && $xp >= 800) {
			return '#FF4B4B';
		} elseif ($xp < 800 && $xp >= 700) {
			return '#FF3232';
		} elseif ($xp < 700 && $xp >= 600) {
			return '#FF1919';
		} elseif ($xp < 600 && $xp >= 500) {
			return '#FF0000';
		} else {
			return '#CE004E';
		}
	}
	
	function XPColorize($str) {
		return '<font color="'.XPColor($str).'">'.$str.'</font>';
	}
	
	function getPts($player) {
		$req = "SELECT pts FROM lg_users WHERE username = '".$player."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0];
	}
	
	function get_nb_games($player) {
		$req = "SELECT count(*) FROM `lg_ladderfollow` WHERE player = '".$player."' AND resultat != '' AND resultat != 'away'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		
		return $l[0];
	}
	
	/*
	 * Unused
	function getConfidence($player) {
		$req = "SELECT count(id)
				FROM lg_playersreports
				WHERE qui='".$player."'
				AND info='3'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return ($l[0] != 0) ? (getNbGames($player) / $l[0]) : '-';
	}
	
	function XPFactor($nb_games) {
		//®Aglou^3
		if ($nb_games < 100) {
			return ($nb_games + 14)/($nb_games + 10);
			//return 0.85*($nb_games + 15)/($nb_games + 5);
		} else {
			return 1;
		}
	}
	*/
	
	function isBanned($player) {
		//BanList
		$req = "SELECT * FROM lg_ladderbans WHERE qui = '".$player."'";
		$t = mysql_query($req);
		if (mysql_num_rows($t) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
	function isAdmin($player) {
		//Admins
		$req = "SELECT * FROM lg_ladderadmins WHERE user = '".ArghSession::get_username()."'";
		$t = mysql_query($req);
		return (mysql_num_rows($t) > 0);
	}
	
	
	function canJoin() {
	
		if (!ArghSession::is_logged()) {
			return false;
		}
		if (getStatus(ArghSession::get_username()) != 'ready') {
			return false;
		}
		
		$req = "SELECT * FROM lg_laddernext WHERE player='".ArghSession::get_username()."'";
		$t = mysql_query($req);
		if (mysql_num_rows($t) > 0) {
			return false;
		}
		
		$req = "SELECT * FROM lg_laddernext";
		$t = mysql_query($req);
		if (mysql_num_rows($t) >= 10) {
			return false;
		}
		
		return !isBanned(ArghSession::get_username());
	}
	*/

	function canJoinDet() {
	
		/*
		0: Impossible de rejoindre
		1: Deja dedans (Normal)
		2: Peut rejoindre
		3: Deja dedans (VIP)
		4: Deja dedans (Fun)
		*/

		if (!ArghSession::is_logged()) {
			return 0;
		}
		
		if (getStatus(ArghSession::get_username()) != LadderStates::READY) {
			return 0;
		}
		
		require_once '/home/www/ligue/classes/CacheManager.php';
		
		//Normal
		$content = file(CacheManager::LADDER_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (count($line) == 5 && $line[0] == ArghSession::get_username()) {
				return 1;
			}
		}

		//VIP
		$content = file(CacheManager::LADDER_VIP_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (count($line) == 4 && $line[0] == ArghSession::get_username()) {
				return 3;
			}
		}
		
		/*
		//Fun
		$content = file('playerlistfun.txt');
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (count($line) == 3 and $line[0] == ArghSession::get_username()) return 4;
		}
		*/
		
		return 2;
	}

	function html_players_table($file) {
		$html = '';
		if (file_exists($file)) {
			$content = file($file);
			$i = 0;
			$html .= '<table class="listing">';
			$html .= '<colgroup><col width="70" /><col /><col width="100" /><col width="200" /></colgroup>';
			$html .= '<thead><tr>';
			$html .= '<th>'.Lang::SLOT.'</th>';
			$html .= '<th>'.Lang::PLAYER.'</th>';
			$html .= '<th>'.Lang::XP.'</th>';
			$html .= '<th>'.Lang::GARENA_ACCOUNT.'</th>';
			$html .= '</tr></thead>';
			$html .= '<tbody>';
			foreach ($content as $val) {
				$line = explode(';', $val);
				if (count($line) == 5) {
					$i++;
					$bg = ($line[0] == ArghSession::get_username()) ? ' class="alternate"' : '';
					$icon = RightsMode::colorize_rights_mini_ladder($line[4]);
					if ($icon != '') $icon .= '&nbsp;';
					$html .= '<tr'.$bg.'>';
					$html .= '<td><i>'.$i.'.</i></td>';
					$html .= '<td>'.$icon.'<a href="?f=player_profile&amp;player='.urlencode($line[0]).'">'.htmlentities($line[0]).'</a></td>';
					$html .= '<td><b>'.XPColorize($line[1]).'</b></td>';
					$html .= '<td>'.htmlentities($line[2]).'</td>';
					$html .= '</tr>';
				}
			}
			$html .= '</tbody>';
			$html .= '</table>';
		}
		return $html;
	}
	
?>
