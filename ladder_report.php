<link rel="stylesheet" href="themes/default/parser.css" type="text/css">
<?php

	require 'classes/ReportModule.php';
	require_once 'FCKeditor/fckeditor.php';
	require 'ladder_functions.php';
	
	function player_index($array, $val) {
		foreach ($array as $key => $value) {
			if ($value == $val) return $key;
		}
	}
	
	$error_img = '<img src="img/icons/exclamation.png" alt="" />&nbsp;';
	$game_id = (int)$_GET['id'];
	
	$is_vip = ($_GET['vip'] == 'true');
	$get_vip = ($is_vip) ? '&vip=true' : '';
	$rm = new ReportModule($is_vip);
	
	$ladder_game_table = $is_vip ? 'laddervip_game' : 'ladder_game';
	
	//Rights
	$is_ladder_rights = ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN)) && !$is_vip;//Ladder admin
	$is_ladder_vip_rights = ArghSession::is_rights(array(RightsMode::VIP_HEADADMIN, RightsMode::VIP_ADMIN)) && $is_vip;//VIP Ladder admin
	$is_any_ladder_rights = $is_ladder_rights || $is_ladder_vip_rights;
	
	//RE-OPEN
	if ($is_any_ladder_rights && $_GET['action'] == 'reopen') {
		$query = "UPDATE ".$rm->_table." SET status = '".Report::STATUS_BEING_HANDLED."' WHERE game_id = '".$game_id."' AND status = '".Report::STATUS_REPORT_CLOSED."'";
		mysql_query($query);
	}
	
	//DELETE REPLAY
	if ($is_any_ladder_rights && $_GET['action'] == 'delete_replay') {
		$report = new Report($is_vip);
		$report->_game_id = $game_id;
		
		$report->delete_replay();
		
		ArghPanel::begin_tag(Lang::INFORMATION_SINGULAR);
		echo '<center><img src="img/icons/information.png" alt="" /> '.Lang::REPORT_REPLAY_REMOVED.'</center>';
		ArghPanel::end_tag();
	} else if ($is_any_ladder_rights && $_GET['action'] == 'parse_replay') {
		$report = new Report($is_vip);
		$report->_game_id = $game_id;
		$report->parse_replay();
	}
	
	//ADD REPLAY
	if (isset($_POST['add_replay'])) {
		//Replay
		$max_size = 4194304; //4 * 1024 * 1024
		$extension = 'w3g';
		
		$msg = '';
		
		if (!empty($_FILES['replay']['name'])) {
			$type = strrchr($_FILES['replay']['name'], '.');
			$type = substr($type, 1);
			if ($type == $extension) {
				if ($_FILES['replay']['size'] <= $max_size) {
					$name = $game_id.'.'.$extension;
					if ($is_vip) $name = 'vip_'.$name;
					if (move_uploaded_file($_FILES['replay']['tmp_name'], Report::REPLAY_PATH.$name)) {
						$replay_ok = true;
					} else {
						$global_error = true;
						$replay_error_info .= Lang::FILE_UPLOAD_ERROR.'<br />'.Lang::ERROR.': '.$_FILES['replay']['error'];
					}
				} else {
					$global_error = true;
					$replay_error_info .= sprintf(Lang::FILE_MAX_WEIGHT_EXCEEDED, round($max_size / (1024 * 1024), 2));
				}
			} else {
				$global_error = true;
				$replay_error_info .= Lang::FILE_EXTENSION_ERROR_REPLAY_ONLY;
			}
		}
		
		if (!$global_error) {
			$report = new Report($is_vip);
			$report->_game_id = $game_id;
			$report->parse_replay();
			$report->add_replay();
		} else {
			echo $replay_error_info;
		}
	}
	
	$query = "SELECT * FROM ".$rm->_games_table." WHERE id = '".$game_id."'";
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 1) {
	
		$ogame = mysql_fetch_object($result);
	
		$game_players = array();
		for ($i = 1; $i <= 8; $i++) {
			$player = 'p'.$i;
			$game_players[] = $ogame->$player;
		}
		if ($is_vip) {
			$game_players[] = $ogame->cap1;
			$game_players[] = $ogame->cap2;
		} else {
			$game_players[] = $ogame->p9;
			$game_players[] = $ogame->p10;
		}
		
		
		//CLOSE
		if ($is_any_ladder_rights && isset($_POST['close_report'])) {
		
			//echo '<pre>';
			//print_r($_POST);
			//echo '</pre>';
		
			$time = time();
		
			foreach ($_POST as $key => $value) {
				$info = explode('@', $key);
				if ($info[0] == 'sanction') {
					$player_index = $info[1];
					$concerned_player = $game_players[$info[1]];
					$sanction = explode('@', $value);
					$ban_type = (int)$sanction[0];
					$ban_length = (int)$sanction[1];
					
					
					//Custom
					if (!empty($_POST['custom@'.$player_index])) {
						$ban_length = (int)$_POST['custom@'.$player_index];
					}
					
					
					//Type
					$type = ($_POST['ban_type@'.$player_index] == 1) ? 'warning' : 'ban';
					
					if ($ban_type != 0 && $ban_length != 0) {
						switch ($ban_type) {
							case Report::BANTYPE_FLAME:
								$motif = Lang::BANTYPE_FLAME;
								break;
							
							case Report::BANTYPE_RUINING:
								$motif = Lang::BANTYPE_RUINING;
								break;
							
							case Report::BANTYPE_RULES_ABUSE:
								$motif = Lang::BANTYPE_RULES_ABUSE;
								break;
							
							case Report::BANTYPE_RAGE_LEAVE:
								$motif = Lang::BANTYPE_RAGE_LEAVE;
								break;
							
							case Report::BANTYPE_BAD_RESULT:
								$motif = Lang::BANTYPE_BAD_RESULT;
								break;
							
							case Report::BANTYPE_USELESS_REPORT:
								$motif = Lang::BANTYPE_USELESS_REPORT;
								break;
							
							case Report::BANTYPE_GGC_ACCOUNT:
								$motif = Lang::BANTYPE_GGC_ACCOUNT;
								break;
								
							case Report::BANTYPE_BUG_ABUSE:
								$motif = Lang::BANTYPE_BUG_ABUSE;
								break;
							
							case Report::BANTYPE_CHEATING:
								$motif = Lang::BANTYPE_CHEATING;
								break;
							
							case Report::BANTYPE_CAP_DISOBEY:
								$motif = Lang::REPORT_CAP_DISOBEY_3_DAYS;
								break;
								
							case Report::BANTYPE_FF_BEFORE_10_MINS:
								$motif = Lang::REPORT_FF_BEFORE_10_MINS;
								break;
						}
					
						$player_to_ban = mysql_real_escape_string($concerned_player);
						$query = "INSERT INTO lg_ladderbans_follow (
								username,
								motif,
								`force`,
								admin,
								quand,
								type,
								afficher,
								ban_categorie,
								game_id,
								is_vip
							) VALUES (
								'".$player_to_ban."',
								'".$motif."',
								'".$ban_length."',
								'".ArghSession::get_username()."',
								'".$time."',
								'".$type."',
								'1',
								'".$ban_type."',
								'".$game_id."',
								".($is_vip ? 1 : 0)."
							)";
						//echo $query;
						//echo $info[1].' - '.$ban_type.' / '.$ban_length.'<br />';
						//id 	qui 	par_qui 	quand 	duree 	raison
						
						mysql_query($query) or die (mysql_error());
						
						if ($type == 'ban') {
							$query = "INSERT INTO lg_ladderbans (
								qui,
								par_qui,
								quand,
								duree,
								raison
							) VALUES (
								'".$player_to_ban."',
								'".ArghSession::get_username()."',
								'".$time."',
								'".$ban_length."',
								'".$motif."'
							)";
							
							mysql_query($query) or die(mysql_query());
							
							$sentence = sprintf(Lang::REPORT_BAN, $game_id);
						} else {
							$sentence = sprintf(Lang::REPORT_WARN, $game_id);
						}
						
						$notif = new Notification();
						$notif->_destinator = $player_to_ban;
						$notif->_link = '?f=ladder_report&id='.$game_id.$get_vip;
						$notif->_message = $sentence;
						$notif->_notif_time = time();
						
						$notif->save();
					}
				}
			}
			
			$query = "UPDATE ".$rm->_table."
					SET
						status='".Report::STATUS_REPORT_CLOSED."',
						close_time='".$time."',
						admin_comment='".mysql_real_escape_string($_POST['admin_comment'])."'
					WHERE game_id = '".$game_id."'";
			mysql_query($query);
			
			foreach ($game_players as $player) {
				$notif = new Notification();
				$notif->_destinator = $player;
				$notif->_link = '?f=ladder_report&id='.$game_id.$get_vip;
				$notif->_message = sprintf(Lang::REPORT_NOTIFICATION_CLOSED, $game_id);
				$notif->_notif_time = time();
				
				$notif->save();
			}
		}
		
		if ($is_any_ladder_rights || in_array(ArghSession::get_username(), $game_players)) {
			if (isset($_POST['sent'])) {
				$game_id = (int)$_POST['game_id'];
				$global_error = false;
			
				//Aucun motif saisi
				if (count($_POST['reasons']) == 0) {
					$reason_error = $error_img;
					$global_error = true;
				}
				
				//Aucun joueur
				$mode_condition = is_array($_POST['reasons']) && (in_array('flame', $_POST['reasons']) || in_array('ruining', $_POST['reasons']) || in_array('leaver', $_POST['reasons']));
				$player_condition = count($_POST['players']) == 0;
				
				if ($mode_condition && $player_condition) {
					$player_error = $error_img;
					$global_error = true;
				}
				
				//Replay
				$mode_condition = !empty($_POST['flame']) || !empty($_POST['ruining']);
				if ($mode_condition && empty($_POST['replay'])) {
					$replay_error = $error_img;
					$global_error = true;
				}
				
				//Commentaire
				if (empty($_POST['comment'])) {
					$comment_error = $error_img;
					$global_error = true;
				}
				
				//Accord règlement
				if (empty($_POST['rules_acknowledge'])) {
					$error_acknowledge = $error_img;
					$global_error = true;
				}
				
				//Replay
				$max_size = 4194304; //4 * 1024 * 1024
				$extension = 'w3g';
				
				$msg = '';
				
				if (!empty($_FILES['replay']['name'])) {
					$type = strrchr($_FILES['replay']['name'], '.');
					$type = substr($type, 1);
					if ($type == $extension) {
						if ($_FILES['replay']['size'] <= $max_size) {
							$name = $game_id.'.'.$extension;
							if (move_uploaded_file($_FILES['replay']['tmp_name'], Report::REPLAY_PATH.$name)) {
								$replay_ok = true;
							} else {
								$global_error = true;
								$replay_error_info .= Lang::FILE_UPLOAD_ERROR.'<br />'.Lang::ERROR.': '.$_FILES['replay']['error'];
							}
						} else {
							$global_error = true;
							$replay_error_info .= sprintf(Lang::FILE_MAX_WEIGHT_EXCEEDED, round($max_size / (1024 * 1024), 2));
						}
					} else {
						$global_error = true;
						$replay_error_info .= Lang::FILE_EXTENSION_ERROR_REPLAY_ONLY;
					}
					
					
				}
				
				if (!$global_error) {
					//Verif réclam non ouverte
					if ($rm->report_exists($game_id)) {
						ArghPanel::begin_tag(ERROR);
						echo '<center>'.ALREADY_CREATED.'</center>';
						ArghPanel::end_tag();
					} else {
						$report = new Report($is_vip);
						$report->_game_id = $game_id;
						$report->_opening_date = time();
						$report->_initiator = ArghSession::get_username();
						$report->_opening_reasons = $_POST['reasons'];
						$report->_concerned_players = $_POST['players'];
						$report->_comment = $_POST['comment'];
						$report->_status = Report::STATUS_OPENED;
						
						if ($replay_ok) {
							$report->_replay = true;
							$report->parse_replay();
						} else {
							$report->_replay = false;
						}
						
						$report->save();
						
						foreach ($game_players as $player) {
							$notif = new Notification();
							$notif->_destinator = $player;
							$notif->_link = '?f=ladder_report&id='.$game_id.$get_vip;
							$notif->_message = Lang::REPORT_NOTIFICATION;
							$notif->_notif_time = time();
							
							$notif->save();
						}
					}
					
				}
			}
			
			//NEW REPORT
			if (!$rm->report_exists($game_id)) {
				ArghPanel::begin_tag(Lang::REPORT_OPEN);
				
				echo '<form enctype="multipart/form-data" method="POST" action="?f=ladder_report&id='.$game_id.$get_vip.'">';
				echo '<input type="hidden" name="game_id" value="'.$game_id.'" />
				<table class="listing">
					<colgroup>
						<col width="24%" />
						<col width="38%" />
						<col width="38%" />
					</colgroup>
					<tr>
						<td>'.Lang::GAME_ID.'</td>
						<td colspan="2"><a href="?f='.$ladder_game_table.'&id='.$game_id.'">'.$game_id.'</a></td>
					</tr>
					<tr>
						<td>'.Lang::REPORT_INITIATOR.'</td>
						<td colspan="2"><a href="?f=player_profile&player='.ArghSession::get_username().'">'.ArghSession::get_username().'</a></td>
					</tr>
					<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td valign="top">'.$reason_error.Lang::REPORT_OPENING_REASONS.'</td>
						<td colspan="2"><input type="checkbox" name="reasons[]" value="flame"'.check_box_array('flame', 'reasons').' /> '.Lang::REPORT_FLAMING.'
						<br />
						<span class="info">'.Lang::REPORT_FLAMING_INFO.'</span><br />&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2"><input type="checkbox"  name="reasons[]" value="ruining"'.check_box_array('ruining', 'reasons').' /> '.Lang::REPORT_GAME_RUINING.'
						<br />
						<span class="info">'.Lang::REPORT_GAME_RUINING_INFO.'</span><br />&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2"><input type="checkbox"  name="reasons[]" value="leaver"'.check_box_array('leaver', 'reasons').' /> '.Lang::REPORT_LEAVER_S_.'
						<br />
						<span class="info">'.Lang::REPORT_LEAVER_S_INFO.'</span><br />&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2"><input type="checkbox"  name="reasons[]" value="result"'.check_box_array('result', 'reasons').' /> '.Lang::REPORT_BAD_RESULT.'
						<br />
						<span class="info">'.Lang::REPORT_BAD_RESULT_INFO.'</span><br />&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2"><input type="checkbox"  name="reasons[]" value="other"'.check_box_array('other', 'reasons').' /> '.Lang::REPORT_OTHER.'
						<br />
						<span class="info">'.Lang::REPORT_OTHER_INFO.'</span></td>
					</tr>
					<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td>'.$player_error.Lang::REPORT_CONCERNED_PLAYERS.'</td>
						<td><input type="checkbox" name="players[]" value="'.$ogame->p1.'"'.check_box_array($ogame->p1, 'players').' /> '.$ogame->p1.' <span class="info"><i>'.getGGC($ogame->p1).'</i></span></td>
						<td><input type="checkbox" name="players[]" value="'.$ogame->p6.'"'.check_box_array($ogame->p6, 'players').' /> '.$ogame->p6.' <span class="info"><i>'.getGGC($ogame->p6).'</i></span></td>
					</tr>';
					for ($i = 2; $i <= 5; $i++) {
						$player_a = 'p'.$i;
						$player_b = 'p'.($i + 5);
						
						if ($is_vip) {
							if ($i == 4) $player_b = 'cap1';
							if ($i == 5) $player_b = 'cap2';
						}
						
						echo '<tr>
							<td></td>
							<td><input type="checkbox" name="players[]" value="'.$ogame->$player_a.'"'.check_box_array($ogame->$player_a, 'players').' /> '.$ogame->$player_a.' <span class="info"><i>'.getGGC($ogame->$player_a).'</i></span></td>
							<td><input type="checkbox" name="players[]" value="'.$ogame->$player_b.'"'.check_box_array($ogame->$player_b, 'players').' /> '.$ogame->$player_b.' <span class="info"><i>'.getGGC($ogame->$player_b).'</i></span></td>
						</tr>';
					}
					echo '<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td>'.$replay_error.Lang::REPLAY.'</td>
						<td colspan="2"><input type="file" name="replay" /></td>
					</tr>';
					if (isset($replay_error_info)) {
						echo '<tr>
							<td colspan="3" align="center"><center>'.$replay_error_info.'</center></td>

						</tr>';
					}
					/*
					echo '<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td>'.Lang::SCREENSHOT_S_.'</td>
						<td colspan="2"><input type="file" name="screen1" /></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2"><input type="file" name="screen2" /></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2"><input type="file" name="screen3" /></td>
					</tr>';
					*/
					echo '<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td>'.Lang::REPORT_IMPORTANT_RULES_TITLE.'</td>
					</tr>
					<tr>
						<td colspan="3"><span class="info"><ul>
						<li style="margin-bottom: 5px;">'.Lang::REPORT_IMPORTANT_RULES_1.'</li>
						<li style="margin-bottom: 5px;">'.Lang::REPORT_IMPORTANT_RULES_2.'</li>
						<li style="margin-bottom: 5px;">'.Lang::REPORT_IMPORTANT_RULES_3.'</li>
						<li style="margin-bottom: 5px;">'.Lang::REPORT_IMPORTANT_RULES_4.'</li>
						<li style="margin-bottom: 5px;">'.Lang::REPORT_IMPORTANT_RULES_5.'</li>
						<li style="margin-bottom: 5px;">'.Lang::REPORT_IMPORTANT_RULES_6.'</li>
						<ul></span></td>
					</tr>
					<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td colspan="3">'.$error_acknowledge.'<input type="checkbox" name="rules_acknowledge"'.check_box('rules_acknowledge').' />&nbsp;'.Lang::REPORT_RULES_ACKNOWLEDGE.'</td>
					</tr>
					<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td valign="top">'.$comment_error.Lang::COMMENT.'</td>
						<td colspan="2">';
						$oFCKeditor = new FCKeditor('comment') ;
						$oFCKeditor->BasePath = '/ligue/FCKeditor/';
						$oFCKeditor->ToolbarSet = 'Basic';
						$oFCKeditor->Width = '100%';
						$oFCKeditor->Height = '200';
						$oFCKeditor->Create();
							//<textarea rows="8" style="width: 400px;" name="comment">'.stripslashes($_POST['comment']).'</textarea>
					echo '</td>
					</tr>';
				echo '</table><br /><center><input type="submit" name="sent" value="'.Lang::VALIDATE.'" /></center></form>';
				
				
				ArghPanel::end_tag();
				
			//} elseif ($report->_status == Report::STATUS_OPENED || $report->_status == Report::STATUS_BEING_HANDLED) {
			} else {
			
				if ($report->_status == Report::STATUS_REPORT_CLOSED) {
					ArghPanel::begin_tag(Lang::REPORT_CLOSED);
				} else {
					ArghPanel::begin_tag(Lang::REPORT_OPENED);
				}
				//echo '<center>'.Lang::REPORT_VIEW_REPORT.'</center><br />';
				
				$report = new Report($is_vip);
				$report->_game_id = $game_id;
				$report->load();
				
				echo '<table class="listing">
					<colgroup>
						<col width="30%" />
						<col width="70%" />
					</colgroup>
					<tr>
						<td>'.Lang::GAME_ID.'</td>
						<td><a href="?f='.$ladder_game_table.'&id='.$report->_game_id.'">'.$report->_game_id.'</a></td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>';
				
				if ($report->_status == Report::STATUS_REPORT_CLOSED) {
					echo '<tr>
						<td><span class="vip">'.Lang::REPORT_CLOSED_ON.'</span></td>
						<td><span class="vip">'.date(Lang::DATE_FORMAT_HOUR, $report->_close_time).'</span></td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td valign="top"><span class="vip">'.Lang::REPORT_ADMIN_COMMENT.'</span></td>
						<td><span class="vip">'.$report->_admin_comment.'</span></td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td valign="top"><span class="vip">'.Lang::SANCTIONS.'</span></td>
						<td>';
						
					$query = "SELECT * FROM lg_ladderbans_follow WHERE game_id = '".$report->_game_id."' AND is_vip = ".($is_vip ? 1 : 0)."";
					//echo $query;
					$result = mysql_query($query);
					if (mysql_num_rows($result)) {
						while ($obj = mysql_fetch_object($result)) {
							echo '<span class="'.($obj->type == 'ban' ? 'lose">'.Lang::BAN : 'vip">'.Lang::WARNING).'</span>&nbsp;&nbsp;&nbsp;'.$obj->username.' - '.$obj->force.($obj->type == 'ban' ? Lang::DAY_LETTER : '').'<br />';
						}
					} else {
						echo '-';
					}
						
					echo '</td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>';
				}
				
				echo '<tr>
						<td>'.Lang::REPORT_INITIATOR.'</td>
						<td><a href="?f=player_profile&player='.$report->_initiator.'">'.$report->_initiator.'</a> <span class="info"><i>'.getGGC($report->_initiator).'</i></span></td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>';
				if (!empty($report->_admin)) {
					echo '<tr>
						<td>'.Lang::ADMIN.'</td>
						<td><a href="?f=player_profile&player='.$report->_admin.'">'.$report->_admin.'</a></td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>';
				}
				echo '<tr>
						<td valign="top">'.Lang::REPORT_OPENING_REASONS.'</td>
						<td><b><ul>';
				if (in_array('flame', $report->_opening_reasons)) {
					echo '<li>'.Lang::REPORT_FLAMING.'</li>';
				}
				if (in_array('ruining', $report->_opening_reasons)) {
					echo '<li>'.Lang::REPORT_GAME_RUINING.'</li>';
				}
				if (in_array('leaver', $report->_opening_reasons)) {
					echo '<li>'.Lang::REPORT_LEAVER_S_.'</li>';
				}
				if (in_array('result', $report->_opening_reasons)) {
					echo '<li>'.Lang::REPORT_BAD_RESULT.'</li>';
				}
				if (in_array('other', $report->_opening_reasons)) {
					echo '<li>'.Lang::REPORT_OTHER.'</li>';
				}
				echo '</ul></b></td></tr>';
				if (count($report->_concerned_players) > 0) {
					echo '<tr>
							<td colspan="2"></td>
						</tr>
						<tr>
							<td valign="top">'.Lang::REPORT_CONCERNED_PLAYERS.'</td>
							<td><b><ul>';
					foreach ($report->_concerned_players as $player) {
						echo '<li><a href="?f=player_profile&player='.$player.'">'.$player.'</a> <span class="info"><i>'.getGGC($player).'</i></span></li>';
					}
					echo '</ul></b></td></tr>';
				}
				if ($report->_replay) {
					echo '<tr>
							<td colspan="2"></td>
						</tr>
						<tr>
							<td>'.Lang::REPLAY.'</td>
							<td><a href="'.$report->get_replay_link().'">'.Lang::REPLAY.'</a>'.($is_any_ladder_rights ? ' - [ <a href="?f=ladder_report&id='.$game_id.'&action=delete_replay'.$get_vip.'">'.Lang::DELETE.'</a> ] - [ <a href="?f=ladder_report&id='.$game_id.'&action=parse_replay'.$get_vip.'">'.Lang::PARSER.'</a> ]' : '').'</td>
						</tr>
						<tr>
							<td colspan="2"></td>
						</tr><tr><td colspan="2">';
						$report->show_chatlog();
						echo '</td></tr>';
				} else {
					echo '<tr>
							<td colspan="2"></td>
						</tr>
						<tr>
							<td>'.Lang::REPLAY.'</td>
							<td>
								<form enctype="multipart/form-data" method="POST" action="?f=ladder_report&id='.$game_id.$get_vip.'">
									<input type="file" name="replay" /> <input type="submit" name="add_replay" value="'.Lang::VALIDATE.'" />
								</form>
							</td>
						</tr>';
				}
				/*
				echo '<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td>'.Lang::SCREENSHOT_S_.'</td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
					</tr>';
				*/
				echo '<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td valign="top">'.Lang::COMMENT.'</td>
						<td colspan="2">'.$report->_comment.'</td>
					</tr>
					</table>';
				
				ArghPanel::end_tag();
				
				/*
				//ADD MESSAGE
				if (isset($_POST['message']) && ArghSession::is_logged()) {
					$msg = new GenericMessage(Tables::REPORT_MESSAGES);
					$msg->_author = ArghSession::get_username();
					$msg->_message = $_POST['message'];
					$msg->_reference_id = $game_id;
					
					$msg->save();
				}
				*/
				/*
				if (isset($_POST['add_message'])) {
					
					if ($is_any_ladder_rights || in_array(ArghSession::get_username(), $game_players)) {
						$report->add_message($_POST['message']);
					}
				}
				*/
				
				//$report->get_messages();
				$name = 'ckreport';
				$messager = new Messager($name, $is_vip ? Tables::REPORT_MESSAGES_VIP : Tables::REPORT_MESSAGES, $game_id);
				$messager->deploy();
				
				/*
				GenericMessageManager::display_messages($name, $report->_messages);
				GenericMessageManager::display_message_adding_box($name, Tables::REPORT_MESSAGES, $game_id);
				*/
				
				/*
				ArghPanel::begin_tag(Lang::MESSAGES);
				
				$messages = $report->get_messages();
				if (mysql_num_rows($messages) == 0) {
					echo '<center>'.Lang::NO_MESSAGE.'</center>';
				} else {
					echo '<div style="overflow: auto; max-height: 450px; border: 1px solid white; padding: 2px;">
						<table class="listing" id="message_list">
						<colgroup>
							<col width="10%" />
							<col width="25%" />
							<col width="65%" />
						</colgroup>';
					$i = 0;
					while ($msg = mysql_fetch_object($messages)) {
						$text = stripslashes($msg->comment);
						if ($msg->is_admin == 1) {
							$text = '<span class="vip">'.$text.'</span>';
						}
						echo '<tr'.Alternator::get_alternation($i).'>
								<td align="center" valign="top" style="padding-top: 15px;"><i>'.$i.'.</i></td>
								<td valign="top" style="padding-top: 15px;"><a href="?f=player_profile&player='.$msg->poster.'">'.$msg->poster.'</a><br /><span class="info">'.date(Lang::DATE_FORMAT_HOUR, $msg->post_date).'</span></td>
								<td>'.$text.'</td>
							</tr>';
					}
					echo '</table></div>';
				}
				
				ArghPanel::end_tag();
				
				require 'FCKeditor/fckeditor.php';
				ArghPanel::begin_tag(Lang::MESSAGE_ADDING);
				echo '<center>';
				echo '<form method="POST" action="?f=ladder_report&id='.$report->_game_id.'#message_list">';
				$oFCKeditor = new FCKeditor('message') ;
				$oFCKeditor->BasePath = '/ligue/FCKeditor/';
				$oFCKeditor->ToolbarSet = 'Basic';
				$oFCKeditor->Width = '100%';
				$oFCKeditor->Height = '200';
				$oFCKeditor->Create();	
				echo '<br /><input type="submit" value="'.Lang::VALIDATE.'" name="add_message" style="width: 150px;" /></form>';
				echo '</center>';
				ArghPanel::end_tag();
				*/
				
				//Admin Ladder
				if ($is_any_ladder_rights) {
				
					ArghPanel::begin_tag(Lang::LADDER_ADMIN);
				
					if ($report->_status != Report::STATUS_REPORT_CLOSED) {
					
						/*
						echo '<pre>';
						print_r($report);
						echo '</pre>';
						*/
					
						//HANDLE
						if ($_GET['action'] == 'administrate') {
							$report->_status = Report::STATUS_BEING_HANDLED;
							$report->_admin = ArghSession::get_username();
							$query = "UPDATE ".$rm->_table." SET admin = '".$report->_admin."', status='".$report->_status."' WHERE game_id = '".$report->_game_id."' AND admin = ''";
							mysql_query($query);
						}
					
						//La game est en cours de traitement
						if ($report->_status == Report::STATUS_BEING_HANDLED) {
						
							echo '<center><span class="win">'.sprintf(Lang::REPORT_BEING_HANDLED_BY, $report->_admin).'</span></center><br />';
					
							$options = '
								<option value="@">'.Lang::REPORT_NO_SANCTION.'</option>
								<option value="'.Report::HOST_LEAVER.'@1">'.Lang::REPORT_HOST_LEAVER.'</option>
								<option value="'.Report::BANTYPE_FLAME.'@3">'.Lang::REPORT_FLAME_3_DAYS.'</option>
								<option value="'.Report::BANTYPE_CAP_DISOBEY.'@3">'.Lang::REPORT_CAP_DISOBEY_3_DAYS.'</option>
								<option value="'.Report::BANTYPE_FLAME.'@7">'.Lang::REPORT_FLAME_7_DAYS.'</option>
								<option value="'.Report::BANTYPE_RUINING.'@3">'.Lang::REPORT_GAME_RUINING_3_DAYS.'</option>
								<option value="'.Report::BANTYPE_RULES_ABUSE.'@7">'.Lang::REPORT_RULES_ABUSE_7_DAYS.'</option>
								<option value="'.Report::BANTYPE_RAGE_LEAVE.'@3">'.Lang::REPORT_RAGE_LEAVE_3_DAYS.'</option>
								<option value="'.Report::BANTYPE_BAD_RESULT.'@1">'.Lang::REPORT_BAD_RESULT_1_DAY.'</option>
								<option value="'.Report::BANTYPE_GGC_ACCOUNT.'@1">'.Lang::REPORT_GGC_ACCOUNT_1_DAY.'</option>
								<option value="'.Report::BANTYPE_USELESS_REPORT.'@1">'.Lang::REPORT_USELESS_REPORT_1_DAY.'</option>
								<option value="'.Report::BANTYPE_FF_BEFORE_10_MINS.'@1">'.Lang::REPORT_FF_BEFORE_10_MINS.'</option>
								<option value="'.Report::BANTYPE_BUG_ABUSE.'@20">'.Lang::REPORT_BUG_ABUSE_20_DAYS.'</option>
								<option value="'.Report::BANTYPE_CHEATING.'@120">'.Lang::REPORT_CHEATING_120_DAYS.'</option>';
								
							$query = "SELECT * FROM lg_ladderbans_follow WHERE username IN ('".implode("','", $game_players)."') ORDER BY quand DESC";
							$result = mysql_query($query);
							
							$bans = array();
							while ($player_bans = mysql_fetch_object($result)) {
								$bans[$player_bans->username][] = array(
									'motif' => $player_bans->motif,
									'force' => $player_bans->force,
									'admin' => $player_bans->admin,
									'quand' => $player_bans->quand,
									'type' => $player_bans->type,
									'afficher' => $player_bans->afficher
								);
							}
						
							echo '<form method="POST" action="?f=ladder_report&id='.$report->_game_id.$get_vip.'">
								<table class="listing">
								<colgroup>
									<col width="20%" />
									<col width="80%" />
								</colgroup>
								<thead>
									<tr>
										<th>'.Lang::PLAYER.'</th>
										<th>'.Lang::SANCTION.'</th>
									</tr>
								</thead>
								<tbody>';
							$i = 0;
							foreach ($game_players as $player) {
								$player_index = player_index($game_players, $player);
								$player_index = player_index($game_players, $player);
								echo '<tr'.Alternator::get_alternation($i).'>
										<td valign="top"><b><a href="?f=player_profile&player='.$player.'">'.$player.'</a></b><br /><span class="info"><i>'.getGGC($player).'</i></span></td>
										<td>
											<select name="ban_type@'.$player_index.'">
												<option value="0">'.Lang::BAN.'</option>
												<option value="1">'.Lang::WARNING.'</option>
											</select>&nbsp;
											<select name="sanction@'.$player_index.'">'.$options.'</select>&nbsp;|&nbsp;'.Lang::CUSTOM.' <input type="text" name="custom@'.$player_index.'" style="width: 40px;" />&nbsp;'.Lang::DAYS.'<br /><br />';
								if (array_key_exists($player, $bans)) {
									echo '<table class="listing">
										<colgroup>
											<col width="10%" />
											<col width="20%" />
											<col width="70%" />
										</colgroup>
										<thead>
											<tr>
												<th>'.Lang::VALUE.'</th>
												<th>'.Lang::DATE.'</th>
												<th>'.Lang::REASON.'</th>
											</tr>
										</thead>
										<tbody>';
									foreach ($bans[$player] as $ban) {
										if ($ban['type'] == 'warning') {
											$force = '<img src="img/'.(($ban['force'] == 4) ? 'red' : $ban['force'].'yellow').'card.gif" alt="'.$ban['force'].'" />';
										} else {
											$force = ($ban['force'] == 0) ? '<img src="img/infini.gif" alt="'.Lang::UNDEFINED.'" />' : $ban['force'].'j';
										}
										$motif = ($ban['afficher'] == 0) ? '<span class="info">'.stripslashes($ban['motif']).'</span>' : stripslashes($ban['motif']);
										echo '<tr>
												<td><b>'.$force.'</b></td>
												<td>'.date(Lang::DATE_FORMAT_DAY, $ban['quand']).'</td>
												<td>'.$motif.'</td>
											</tr>';
									}
									echo '</tbody></table>';
								} else {
									echo Lang::REPORT_NO_SANCTION.'<br />';
								}
								echo '<br /></td></tr>';
							}
							echo '</tbody></table><br />
							<center>
								<b>'.Lang::COMMENT.'</b><br />';
								$oFCKeditor = new FCKeditor('admin_comment') ;
								$oFCKeditor->BasePath = '/ligue/FCKeditor/';
								$oFCKeditor->ToolbarSet = 'Basic';
								$oFCKeditor->Value = $report->_admin_comment;
								$oFCKeditor->Width = '100%';
								$oFCKeditor->Height = '200';
								$oFCKeditor->Create();	
								//<textarea rows="8" style="width: 500px;" name="admin_comment">'.$report->_admin_comment.'</textarea>
								echo '<br /><br />
								<input type="submit" name="close_report" value="'.Lang::REPORT_CLOSE.'" />
							</center>
							</form>';
						} else {
							echo '<center>'.Lang::REPORT_WAITING_FOR_ADMIN.' <a href="?f=ladder_report&id='.$report->_game_id.$get_vip.'&action=administrate">'.Lang::REPORT_HANDLE.'</a></center>';
						}
					} else {

						echo '<center><a href="?f=ladder_report&id='.$game_id.$get_vip.'&action=reopen">'.Lang::REPORT_REOPEN.'</a></center>';
					}
					ArghPanel::end_tag();
				}
			}
		} else {
			ArghPanel::begin_tag(Lang::ERROR);
			echo '<center>'.Lang::REPORT_ERROR_NOT_IN_GAME.'</center>';
			ArghPanel::end_tag();
		}
		
	} else {
		ArghPanel::begin_tag(Lang::ERROR);
		echo '<center>'.Lang::REPORT_GAME_DOESNT_EXIST.'</center>';
		ArghPanel::end_tag();
	}
?>