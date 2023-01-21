<?php
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

	function getTeamVip($u) {
		return ($u == 'cap1' or $u == 'p1' or $u == 'p2' or $u == 'p3' or $u == 'p4') ? 'se' : 'sc';
	}
	
	function reportGame($game_id, $winner) {
	
		$game_id = (int)$game_id;
		//Constantes
		$gain_fixe = 36;
		$malus_leave = -30;
		$malus_away = -25;

		//Calcul points
		$data = array(
			'cap1' => array('player' => '', 'pts' => 0, 'info' => 0, 'gain' => 0),
			'p1' => array('player' => '', 'pts' => 0, 'info' => 0, 'gain' => 0),
			'p2' => array('player' => '', 'pts' => 0, 'info' => 0, 'gain' => 0),
			'p3' => array('player' => '', 'pts' => 0, 'info' => 0, 'gain' => 0),
			'p4' => array('player' => '', 'pts' => 0, 'info' => 0, 'gain' => 0),
			'cap2' => array('player' => '', 'pts' => 0, 'info' => 0, 'gain' => 0),
			'p5' => array('player' => '', 'pts' => 0, 'info' => 0, 'gain' => 0),
			'p6' => array('player' => '', 'pts' => 0, 'info' => 0, 'gain' => 0),
			'p7' => array('player' => '', 'pts' => 0, 'info' => 0, 'gain' => 0),
			'p8' => array('player' => '', 'pts' => 0, 'info' => 0, 'gain' => 0)
		);
		$corresp = array(
			'pp1' => 'p1',
			'pp2' => 'p5',
			'pp3' => 'p6',
			'pp4' => 'p2',
			'pp5' => 'p3',
			'pp6' => 'p7',
			'pp7' => 'p8',
			'pp8' => 'p4',
		);
		
		//Caps
		$req = "
			SELECT u.username, u.pts_vip
			FROM lg_laddervip_games l, lg_users u
			WHERE l.id='".$game_id."'
			AND l.cap1 = u.username
			LIMIT 1
		";
		$t = mysql_query($req) or die(mysql_error());
		while ($l = mysql_fetch_row($t)) {
			$data['cap1']['player'] = $l[0];
			$data['cap1']['pts'] = $l[1];
		}
		$req = "
			SELECT u.username, u.pts_vip
			FROM lg_laddervip_games l, lg_users u
			WHERE l.id='".$game_id."'
			AND l.cap2 = u.username
			LIMIT 1
		";
		$t = mysql_query($req) or die(mysql_error());
		while ($l = mysql_fetch_row($t)) {
			$data['cap2']['player'] = $l[0];
			$data['cap2']['pts'] = $l[1];
		}
		
		//8 joueurs
		for ($i=1; $i<=8; $i++) {
			$pl = 'pp'.$i;
			$req = "
				SELECT u.username, u.pts_vip
				FROM lg_laddervip_games l, lg_users u
				WHERE l.id = '".$game_id."'
				AND l.".$pl." = u.username
				LIMIT 1
			";
			$t = mysql_query($req) or die(mysql_error());
			while ($l = mysql_fetch_row($t)) {
				$data[$corresp[$pl]]['player'] = $l[0];
				$data[$corresp[$pl]]['pts'] = $l[1];
			}
		}
		
		$req = "SELECT * FROM lg_laddervip_playersreports WHERE game_id = '".$game_id."'";
		$t = mysql_query($req);
		while ($l = mysql_fetch_object($t)) {
			foreach ($data as $key => $pl) {
				if ($pl['player'] == $l->pour_qui) {
					switch ($l->info) {
						case 1:
							$data[$key]['info'] += 1;
							break;
						case 2:
							$data[$key]['info'] += 10;
							break;
						case 3:
							$data[$key]['info'] += 100;
							break;
					}
				}
			}
		}
		
		//Arrays
		$leavers = array();
		$aways = array();
		
		//Formatage code info
		foreach ($data as $key => $pl) {
			if (strlen($data[$key]['info']) == 1) $data[$key]['info'] = '00'.$data[$key]['info'];
			if (strlen($data[$key]['info']) == 2) $data[$key]['info'] = '0'.$data[$key]['info'];
			//Leaver
			if ($data[$key]['info'][2] >= 5) $leavers[] = $key;
			if ($data[$key]['info'][1] >= 5) $aways[] = $key;
		}
		
		$E1 = ($data['cap1']['pts'] + $data['p1']['pts'] + $data['p2']['pts'] + $data['p3']['pts'] + $data['p4']['pts']) / 5;
		$E2 = ($data['cap2']['pts'] + $data['p5']['pts'] + $data['p6']['pts'] + $data['p7']['pts'] + $data['p8']['pts']) / 5;
		
		$D1 = $E2 - $E1;
		$D2 = -$D1;
		
		if ($winner != 'none') {
			if ($winner == 'se') {
				//Vainqueur Senti (1)
				//Coeff
				$F = XPCoeff($D1);
				//Gain
				$G = $gain_fixe * $F;
				$G = min(max($G, 18), 72);

				//Equipe gagnante
				$data['cap1']['gain'] = $G;
				$data['p1']['gain'] = $G;
				$data['p2']['gain'] = $G;
				$data['p3']['gain'] = $G;
				$data['p4']['gain'] = $G;
				
				//Equipe perdante
				$data['cap2']['gain'] = -$G;
				$data['p5']['gain'] = -$G;
				$data['p6']['gain'] = -$G;
				$data['p7']['gain'] = -$G;
				$data['p8']['gain'] = -$G;
				
			} else {
				//Vainqueur Scourge (2)
				//Coeff
				$F = XPCoeff($D2);
				//Gain
				$G = $gain_fixe * $F;
				$G = min(max($G, 18), 72);
				
				//Equipe gagnante
				$data['cap2']['gain'] = $G;
				$data['p5']['gain'] = $G;
				$data['p6']['gain'] = $G;
				$data['p7']['gain'] = $G;
				$data['p8']['gain'] = $G;
				
				//Equipe perdante
				$data['cap1']['gain'] = -$G;
				$data['p1']['gain'] = -$G;
				$data['p2']['gain'] = -$G;
				$data['p3']['gain'] = -$G;
				$data['p4']['gain'] = -$G;
			}
		}

		switch (count($leavers)) {
			case 0:
				//Pas de leaver
				break;
			case 1:
				//Num leaver
				$leaver = $leavers[0];
				//Détermination de la team du leaver
				$team = getTeamVip($leaver);

				if ($winner == $team) {
					//La team du leaver gagne
					if ($winner == 'se') {
						//Winner Sentinel
						//Bonus pour ceux qui ont joué en infériorité
						$data['cap1']['gain'] *= 1.25;
						$data['p1']['gain'] *= 1.25;
						$data['p2']['gain'] *= 1.25;
						$data['p3']['gain'] *= 1.25;
						$data['p4']['gain'] *= 1.25;
						//Malus pour ceux qui ont joué en supériorité
						//
					} elseif ($winner == 'sc') {
						//Winner Scourge
						//Bonus pour ceux qui ont joué en infériorité
						$data['cap2']['gain'] *= 1.25;
						$data['p5']['gain'] *= 1.25;
						$data['p6']['gain'] *= 1.25;
						$data['p7']['gain'] *= 1.25;
						$data['p8']['gain'] *= 1.25;
						//Malus pour ceux qui ont joué en supériorité
						//
					}
				} else {
					//La team du leaver perd
					if ($winner == 'se') {
						//Winner Sentinel
						//Team en infériorité
						$data['cap2']['gain'] *= 0.33;
						$data['p5']['gain'] *= 0.33;
						$data['p6']['gain'] *= 0.33;
						$data['p7']['gain'] *= 0.33;
						$data['p8']['gain'] *= 0.33;
						//Team en supériorité
						//
					} elseif ($winner == 'sc') {
						//Winner Scourge
						//Team en infériorité
						$data['cap1']['gain'] *= 0.33;
						$data['p1']['gain'] *= 0.33;
						$data['p2']['gain'] *= 0.33;
						$data['p3']['gain'] *= 0.33;
						$data['p4']['gain'] *= 0.33;
						//Team en supériorité
						//
					}
				}
				//Le leaver prend sa pénalité
				$data[$leaver]['gain'] += $malus_leave;
				break;
			case 2:
				$leaver1 = $leavers[0];
				$leaver2 = $leavers[1];

				//Vérif 4v4
				if (getTeamVip($leaver1) != getTeamVip($leaver2)) {
					//4v4
					//Distrib Points
					if ($winner == 'se') {
						//Winner Sentinel
						//Minimisation de la perte
						$data['cap2']['gain'] *= 0.5;
						$data['p5']['gain'] *= 0.5;
						$data['p6']['gain'] *= 0.5;
						$data['p7']['gain'] *= 0.5;
						$data['p8']['gain'] *= 0.5;
					} elseif ($winner == 'sc') {
						//Winner Scourge
						//Minimisation de la perte
						$data['cap1']['gain'] *= 0.5;
						$data['p1']['gain'] *= 0.5;
						$data['p2']['gain'] *= 0.5;
						$data['p3']['gain'] *= 0.5;
						$data['p4']['gain'] *= 0.5;
					}
				} else {
					//3v5
					$data['cap1']['gain'] = 0;
					$data['p1']['gain'] = 0;
					$data['p2']['gain'] = 0;
					$data['p3']['gain'] = 0;
					$data['p4']['gain'] = 0;
					$data['cap2']['gain'] = 0;
					$data['p5']['gain'] = 0;
					$data['p6']['gain'] = 0;
					$data['p7']['gain'] = 0;
					$data['p8']['gain'] = 0;
				}
				$data[$leaver1]['gain'] += $malus_leave;
				$data[$leaver2]['gain'] += $malus_leave;
				
				break;
			default:
				//3+ leavers
				$data['cap1']['gain'] = 0;
				$data['p1']['gain'] = 0;
				$data['p2']['gain'] = 0;
				$data['p3']['gain'] = 0;
				$data['p4']['gain'] = 0;
				$data['cap2']['gain'] = 0;
				$data['p5']['gain'] = 0;
				$data['p6']['gain'] = 0;
				$data['p7']['gain'] = 0;
				$data['p8']['gain'] = 0;
				
				foreach ($leavers as $l) $data[$l]['gain'] += $malus_leave;
				break;
		}
		
		//Joueurs qui ne se sont pas présentés
		foreach ($aways as $a) $data[$a]['gain'] = $malus_away;
		
		//Formatage Follow Code Info
		foreach ($data as $key => $pl) {
			//Leaver
			if ($data[$key]['info'][2] >= 5) {
				$data[$key]['info'] = 'left';
			} elseif ($data[$key]['info'][1] >= 5) {
				$data[$key]['info'] = 'away';
			} elseif ($winner == 'none' && $data[$key]['gain'] == 0) {
				$data[$key]['info'] = 'none';
			} else {
				$data[$key]['info'] = ($data[$key]['gain'] > 0) ? 'win' : 'lose';
			}
		}
		
		//Follow
		foreach ($data as $key => $pl) {
			
			//Arrondi à l'entier
			$data[$key]['gain'] = round($data[$key]['gain'], 0);
			
			//Suivi
			$query = "	INSERT INTO lg_laddervip_follow (player, game_id, resultat, xp)
						VALUES ('".$data[$key]['player']."', '".$game_id."', '".$data[$key]['info']."', '".$data[$key]['gain']."')";
			mysql_query($query) or die(mysql_error());
			
			//MàJ points
			$query = "	UPDATE lg_users SET pts_vip = pts_vip + ".round($data[$key]['gain'], 0)." WHERE username = '".$data[$key]['player']."'";
			mysql_query($query) or die(mysql_error());
		}
		
		//Report
		$corresp_inv = array(
			'b1' => 'p1',
			'b2' => 'p5',
			'b3' => 'p6',
			'b4' => 'p2',
			'b5' => 'p3',
			'b6' => 'p7',
			'b7' => 'p8',
			'b8' => 'p4',
			'b9' => 'cap1',
			'b10' => 'cap2',
		);
		
		$req = "UPDATE lg_laddervip_games
				SET status = 'closed',
				winner = '".$winner."',
				when_closed = '".time()."',
				b1 = '".$data[$corresp_inv['b1']]['gain']."',
				b2 = '".$data[$corresp_inv['b2']]['gain']."',
				b3 = '".$data[$corresp_inv['b3']]['gain']."',
				b4 = '".$data[$corresp_inv['b4']]['gain']."',
				b5 = '".$data[$corresp_inv['b5']]['gain']."',
				b6 = '".$data[$corresp_inv['b6']]['gain']."',
				b7 = '".$data[$corresp_inv['b7']]['gain']."',
				b8 = '".$data[$corresp_inv['b8']]['gain']."',
				b9 = '".$data[$corresp_inv['b9']]['gain']."',
				b10 = '".$data[$corresp_inv['b10']]['gain']."'
				WHERE id = '".$game_id."'
		";
		mysql_query($req) or die(mysql_error());
		
		$req = "UPDATE lg_laddervip_games
				SET status = 'closed',
				winner = '".$winner."',
				when_closed = '".time()."'
				WHERE id = '".$game_id."'";
		mysql_query($req) or die(mysql_error());

		/* ?
		// Delete dans lg_laddervip_stats
		//mysql_query("DELETE FROM lg_laddervip_stats WHERE game_id = '".$game_id."'");
		*/
	}


	/*
	function blocMsg($game_id, $action) {
	
		//Vérif
		$canPost = false;
		$req = "SELECT * FROM lg_laddervip_games WHERE id = '".$game_id."'";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);
		for ($i = 1; $i <= 10; $i++) {
			$pl = 'p'.$i;
			if (ArghSession::get_username() == $l->$pl) {
				$canPost = true;
				break;
			}
		}
		
		if ($canPost or ArghSession::is_rights(array(RightsMode::VIP_HEADADMIN, RightsMode::VIP_ADMIN))) {
			require_once('FCKeditor/fckeditor.php');
			if (isset($_SESSION['password'])) {
				echo '<table class="simple">
				<tr><td class="top_left"></td><td class="top">Ajouter un message</td><td class="top_right"></td></tr>
				<form method="POST" action="'.$action.'" onSubmit="boutonEnvoi.disabled=true;">
				<input type="hidden" name="game_id" value="'.$game_id.'">
				<tr><td class=left></td><td>';
				
				$oFCKeditor=new FCKeditor('FCKLadder') ;
				$oFCKeditor->BasePath='/ligue/FCKeditor/';
				$oFCKeditor->ToolbarSet='Basic';
				$oFCKeditor->Width ='100%' ;
				$oFCKeditor->Height='150' ;
				$oFCKeditor->Create();
				
				echo '</td><td class=right></td></tr>';
				echo '<tr><td class=left></td><td><center><input type="submit" value="Valider" name="boutonEnvoi"></center></td><td class=right></td></tr></form>';
			} else {
				echo '<tr><td>Vous devez vous logger pour ajouter un message</td></tr>';
			}
			echo '<tr><td class="bottom_left"></td><td class="bottom"></td><td class="bottom_right"></td></tr></table></form>';
		}
	}
	*/
	
	function streak($player) {
		$req = "SELECT * FROM lg_laddervip_follow WHERE player = '".$player."' ORDER BY game_id DESC";
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
	
	function clearGame($game_id) {
		$req = "SELECT * FROM lg_laddervip_games WHERE id = '".$game_id."'";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);
		for ($i = 1; $i <= 8; $i++) {
			$pl = 'pp'.$i;
			$bo = 'b'.$i;
			mysql_query("UPDATE lg_users SET pts_vip = pts_vip - ".$l->$bo." WHERE username = '".$l->$pl."'");
			mysql_query("UPDATE lg_laddervip_games SET ".$bo." = '0' WHERE id = '".$game_id."'");
		}
		//Caps
		mysql_query("UPDATE lg_users SET pts_vip = pts_vip - ".$l->b9." WHERE username = '".$l->cap1."'");
		mysql_query("UPDATE lg_users SET pts_vip = pts_vip - ".$l->b10." WHERE username = '".$l->cap2."'");
		mysql_query("UPDATE lg_laddervip_games SET b9 = '0', b10 = '0' WHERE id = '".$game_id."'");
		
		mysql_query("UPDATE lg_laddervip_games SET status = 'admin_opened' WHERE id = '".$game_id."'");
		mysql_query("DELETE FROM lg_laddervip_follow WHERE game_id = '".$game_id."'");
	}
	
	/*
	function handleMsg($game_id) {
		if (isset($_POST['FCKLadder']) and strlen($_POST['FCKLadder']) >= 1) {
			$texte=eregi_replace("<script[^>]*>(.|\n)*script>(\r\n)?", "", $_POST['FCKLadder']);
			
			$req = "SELECT *
					FROM lg_laddervip_comment
					WHERE game_id = '".$game_id."'
					AND qui = '".ArghSession::get_username()."'
					AND quoi = '".$texte."'";
			$t = mysql_query($req);
			if (mysql_num_rows($t) == 0) {
			$ins = "INSERT INTO lg_laddervip_comment (qui, quand, quoi, game_id)
					VALUES ('".ArghSession::get_username()."', '".time()."', '".$texte."', '".$game_id."')";
			mysql_query($ins);
			}
		}
	}
	
	function listMsg($game_id) {
		$req = "SELECT * FROM lg_laddervip_comment WHERE game_id = '".$game_id."' ORDER BY id ASC";
		$t = mysql_query($req);
		if (mysql_num_rows($t) > 0) {
			echo '	<table class="simple">
					<tr><td class="top_left"></td><td class="top" colspan="2">Messages</td><td class="top_right"></td></tr>';
					
			while ($l = mysql_fetch_object($t)) {
				echo '<tr><td width="150"><b>'.$l->qui.'</b></td><td width=500>'.stripslashes($l->quoi).'</td></tr>';
			}
			
			echo '<tr><td class="bottom_left"></td><td class="bottom" colspan="2"></td><td class="bottom_right"></td></tr></table>';
		}
	}
	*/
	
	function blocLastGames($player, $limit) {
		//derniers matchs
		ArghPanel::begin_tag(Lang::LAST_GAMES);
		echo '<table class="listing">';
		
		$req = "SELECT l.when_closed, f.player, f.resultat, f.xp, l.id
				FROM lg_laddervip_games l, lg_laddervip_follow f
				WHERE l.id = f.game_id
				AND status = 'closed'
				AND f.player = '".$player."'
				ORDER BY id DESC
				LIMIT ".$limit;
				
		$t = mysql_query($req);
		
		if (mysql_num_rows($t) > 0) {
			echo '<colgroup>
					<col width="30%" />
					<col width="30%" />
					<col width="10%" />
					<col width="30%" />
				</colgroup>
				<thead>
					<tr>
						<th>'.Lang::DATE.'</th>
						<th>'.Lang::RESULT.'</th>
						<th>'.Lang::XP.'</th>
						<th>'.Lang::RECAP.'</th>
					</tr>
				</thead>
				<tbody>';
			$i = 0;
			while ($l = mysql_fetch_row($t)) {
				$alt = ($i++ % 2) ? ' class="alternate"' : '';
				if ($l[3] > 0) {
					$result = '<span class="win">'.Lang::WIN.'</span>';
					$score = '<span class="win">+'.$l[3].'</span>';
					//$score = '<span class="win">+</span>';
				} elseif ($l[3] == 0) {
					$result = Lang::CLOSED;
					$score = '0';
					//$score = '';
				} elseif ($l[3] < 0 and $l[2] == 'left') {
					$result = '<span class="draw">'.Lang::LEFT.'</span>';
					$score = '<span class="draw">'.$l[3].'</span>';
					//$score = '<span class="draw">-</span>';
				} elseif ($l[3] < 0 and $l[2] == 'away') {
					$result = '<span class="info">'.Lang::AWAY.'</span>';
					$score = '<span class="info">'.$l[3].'</span>';
					//$score = '<span class="info">/</span>';
				} else {
					$result = '<span class="lose">'.Lang::LOSS.'</span>';
					$score = '<span class="lose">'.$l[3].'</span>';
					//$score = '<span class="lose">-</span>';
				}
				echo '<tr><td'.$alt.'>'.date(Lang::DATE_FORMAT_HOUR, $l[0]).'</td><td'.$alt.'>'.$result.'</td><td'.$alt.'><b>'.$score.'</b></td><td'.$alt.'><a href="?f=laddervip_game&id='.$l[4].'">Game #'.$l[4].'</a></td></tr>';
			}
		} else {
			echo '<tr><td colspan="4"><center>'.Lang::NO_GAME.'</center></td></tr>';
		}
		
		echo '</tbody></table>';
		
		ArghPanel::end_tag();
	}

	function getNextGameInfos() {
		$req = "SELECT id, opened FROM lg_laddervip_games ORDER BY id DESC LIMIT 1";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0].'#'.(time() - $l[1]);
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
		$req = "SELECT status FROM lg_laddervip_games WHERE id='".$game_id."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0];
	}
	
	function XPColor($xp) {
		if ($xp > 4000) {
			$xp_color = '#0099FF';
		} elseif ($xp >= 3000 and $xp < 4000) {
			$xp_color = '#FFCC00';
		} elseif ($xp >= 2400 and $xp < 3000) {
			$xp_color = '#19FF19';
		} elseif ($xp < 2400 and $xp >= 2300) {
			$xp_color = '#32FF32';
		} elseif ($xp < 2300 and $xp >= 2200) {
			$xp_color = '#4BFF4B';
		} elseif ($xp < 2200 and $xp >= 2100) {
			$xp_color = '#64FF64';
		} elseif ($xp < 2100 and $xp >= 2000) {
			$xp_color = '#7DFF7D';
		} elseif ($xp < 2000 and $xp >= 1900) {
			$xp_color = '#96FF96';
		} elseif ($xp < 1900 and $xp >= 1800) {
			$xp_color = '#AFFFAF';
		} elseif ($xp < 1800 and $xp >= 1700) {
			$xp_color = '#C8FFC8';
		} elseif ($xp < 1700 and $xp >= 1600) {
			$xp_color = '#E0FFE0';
		} elseif ($xp < 1600 and $xp >= 1500) {
			$xp_color = '#FFFFFF';
		} elseif ($xp < 1500 and $xp >= 1400) {
			$xp_color = '#FFE0E0';
		} elseif ($xp < 1400 and $xp >= 1300) {
			$xp_color = '#FFC8C8';
		} elseif ($xp < 1300 and $xp >= 1200) {
			$xp_color = '#FFAFAF';
		} elseif ($xp < 1200 and $xp >= 1100) {
			$xp_color = '#FF9696';
		} elseif ($xp < 1100 and $xp >= 1000) {
			$xp_color = '#FF7D7D';
		} elseif ($xp < 1000 and $xp >= 1000) {
			$xp_color = '#FF6464';
		} elseif ($xp < 900 and $xp >= 800) {
			$xp_color = '#FF4B4B';
		} elseif ($xp < 800 and $xp >= 700) {
			$xp_color = '#FF3232';
		} else {
			$xp_color = '#FF1919';
		}
		return $xp_color;
	}
	
	function XPColorize($str) {
		return '<font color="'.XPColor($str).'">'.$str.'</font>';
	}
	
	function getPts($player) {
		$req = "SELECT pts_vip FROM lg_users WHERE username = '".$player."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0];
	}
	
	/*
	function getNbGames($player) {
		$req = "SELECT count(*)
				FROM lg_laddervip_follow
				WHERE player = '".$player."'
				AND xp != 0;
		";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		
		return $l[0];
	}
	
	function XPFactor($nb_games) {
		//®Aglou^3
		if ($nb_games < 100) {
			return ($nb_games + 14)/($nb_games + 10);
		} else {
			return 1;
		}
	}
	*/
	
	function XPCoeff($x) {
		//return pow(1.00273371, $x) * exp(-$x / 60000);
		//return exp(-$x / 60000);
		return exp(-$x / 6000);
	}

	function canJoinDet() {
	
		require_once '/home/www/ligue/classes/CacheManager.php';
	
		/*
		0: Impossible de rejoindre
		1: Deja dedans (Normal)
		2: Peut rejoindre
		3: Deja dedans (VIP)
		*/

		if (!ArghSession::is_logged()) {
			return 0;
		}
		
		if (getStatus(ArghSession::get_username()) != 'ready') {
			return 0;
		}
		
		//Normal
		$content = file(CacheManager::LADDER_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (count($line) == 5 and $line[0] == ArghSession::get_username()) return 1;
		}

		//VIP
		$content = file(CacheManager::LADDER_VIP_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (count($line) == 4 and $line[0] == ArghSession::get_username()) return 3;
		}
		
		return 2;
	}
?>
