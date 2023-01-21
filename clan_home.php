<?php
	//Include
	include_once '/home/www/ligue/classes/MatchStates.php';
	include_once '/home/www/ligue/classes/Availabilities.php';
	include_once '/home/www/ligue/classes/NotificationManager.php';

	//Team ID
	$team_id = ArghSession::get_clan();
	
	//
	//$two_weeks = 1209600;
	
	if ($team_id != 0) {
	
		//JS
		?>
		<script type="text/javascript" src="/ligue/javascript/ui.datepicker.js"></script>
		<script language="javascript">
			$(document).ready(function() {
				$('#datepicker').datepicker();
				$('#datepicker').datepicker('option', {dateFormat: 'yy-mm-dd'});
			});

			function update_symbol(selector) {
				$('#container_' + selector.id).fadeOut();
				
				var img;
				switch (parseInt(selector.value)) {
					case <?php echo Availabilities::AVAILABLE; ?>:
						img = 'tick';
						break;
					case <?php echo Availabilities::UNSURE; ?>:
						img = 'flag_yellow';
						break;
					case <?php echo Availabilities::UNAVAILABLE; ?>:
						img = 'cross';
						break;
					case <?php echo Availabilities::NOT_ANSWERED_YET; ?>:
					default:
						img = 'clock';
						break;
				}
				
				$.get("/ligue/ajax/update_availability.php",
					{
						id: selector.id,
						status: selector.value
					}, function (data) {
						$('#' + selector.id).css('background', 'url(\'/ligue/img/icons/' + img + '.png\') no-repeat');
						
					}
				);
				
				$('#container_' + selector.id).fadeIn();
			}
		</script>
		<?php
	
		//Rights
		$hasAdminRights = ((ArghSession::get_clan_rank() == ClanRanks::TAUREN) || (ArghSession::get_clan_rank() == ClanRanks::SHAMAN));
	
		//Team
		$team = new Team($team_id);
		$team->load_players();
	
		//Some admin stuff
		if ($hasAdminRights) {
			//Update MotD
			if (isset($_POST['save_motd'])) {
				$team->update_motd($_POST['motd']);
			}
		
			//Create date proposal
			if (isset($_POST['create_date_proposal'])) {
				$exploded_date = explode('-', $_POST['date']);
				$time = mktime($_POST['hour'], $_POST['minute'], 0, (int)$exploded_date[1], (int)$exploded_date[2], (int)$exploded_date[0]);
				$team->create_date_proposal($_POST['playday'], $time);
				
				//Notifications
				$notif = new Notification();
				$notif->_link = '?f=clan_home';
				$notif->_message = sprintf(Lang::TEAM_DATE_PROPOSITION_ADDED, ArghSession::get_username());
				foreach ($team->_players as $player) {
					if ($player != ArghSession::get_username()) {
						$notif->_destinator = $player;
						$notif->save();
					}
				}
			}
			
			//Delete date proposal
			if (isset($_GET['delete_proposal'])) {
				$team->delete_date_proposal($_GET['delete_proposal'], ArghSession::get_clan());
			}
		}
	
		//Get team infos
		$team->load_infos();
		$team->load_date_proposals();
		
		ArghPanel::begin_tag(Lang::TEAM.' '.$team->_name.' ['.$team->_tag.']');
		
		//Logo
		echo '<center><img src="'.$team->_logo.'" alt="" /></center>';
		
		//MotD
		echo '<b>'.Lang::TEAM_MOTD.'</b><br /><div style="border: 1px solid #333333; background-color: #161616; padding: 5px; margin-top: 5px;">';
		if ($hasAdminRights) {
			echo '<form method="POST" action="?f=clan_home"><textarea rows="5" name="motd" style="border: 0px; background-color: #161616; width: 100%;">'.$team->_motd.'</textarea><br />';
		} else {
			echo $team->_motd;
		}
		echo '</div><br />';
		
		if ($hasAdminRights) {
			echo '<center><input type="submit" value="'.Lang::UPDATE.'" name="save_motd" style="width: 100px;" /></center></form>';
		}
		
		echo '<br /><br />';
		
		if ($team->_division != 0) {
		
			$count = count($team->_players);
		
			//League Planner
			echo '<b>'.Lang::TEAM_LEAGUE_PLANNER.' - '.Lang::DIVISION.' <a href="?f=league_division&div='.$team->_division.'">'.$team->_division.'</a></b><br /><br />';
			
			//Loading league planning
			$req = "SELECT m.j, m.date_defaut, m.etat, c1.id, c1.name, c2.id, c2.name
					FROM lg_matchs m, lg_clans c1, lg_clans c2
					WHERE c1.id = m.team1
					AND c2.id = m.team2
					AND m.divi = '".$team->_division."'
					AND (m.team1 = '".$team->_id."' OR m.team2 = '".$team->_id."')
					AND m.etat = '".MatchStates::NOT_PLAYED_YET."'
					ORDER BY m.j ASC";
			$result = mysql_query($req) or die(mysql_error());
			
			//Store for a later use (date proposal adding)
			$playdays = array();
			
			while ($row = mysql_fetch_row($result)) {
			
				$opponent = ($row[3] == $team->_id) ? $row[6] : $row[4];
				$playdays[$row[0]] = $opponent;
			
				echo '<div style="overflow: auto; width: 600px;">';
				//echo Lang::PLAYDAY.' <b><span class="vip">'.$row[0].'</span></b> : <span class="win">'.date(Lang::DATE_FORMAT_DAY, $row[1]).'</span> - <span class="draw">'.date(Lang::DATE_FORMAT_DAY, $row[1] + $two_weeks).'</span> - '.$opponent.'<br />';
				echo Lang::PLAYDAY.' <b><span class="vip">'.$row[0].'</span></b> : <span class="win">'.date(Lang::DATE_FORMAT_DAY, $row[1]).'</span> - '.$opponent.'<br />';
				echo '<table style="width: 100%; border: 1px solid #333333; background-color: #161616; margin-top: 5px;">';
				
				$done = false;
				
				//Date proposals
				foreach ($team->_date_proposals as $proposal_id => $data) {
				
					$availabilities = $data[2];
				
					if ($data[0] == $row[0]) {
					
						//Display once
						if (!$done) {
							echo '<tr><td></td><td>&nbsp;&nbsp;#&nbsp;&nbsp;</td>';
							
							//Players
							foreach ($team->_players as $player) {
								echo '<td style="padding: 5px; height: 25px; border-bottom: 1px solid #EEEEEE;">'.$player.'</td>';
							}
							echo '</tr>';
							
							$done = true;
						}
					
						//Valid players
						$valid_players = 0;
						foreach ($availabilities as $key => $val) {
							//Potential leak if the player is not in team anymore
							if ($val == Availabilities::AVAILABLE && in_array($key, $team->_players)) $valid_players++;
						}
					
						echo '<tr><td>'.date(Lang::DATE_FORMAT_HOUR, $data[1]);
						
						if ($hasAdminRights) {
							echo '<br /><center>[<a href="?f=clan_home&delete_proposal='.$proposal_id.'">'.strtolower(Lang::DELETE).'</a>]</center><br />';
						}
						
						echo '</td><td>&nbsp;&nbsp;<span class="'.($valid_players >= 5 ? 'win' : 'lose').'">'.$valid_players.'</span>&nbsp;&nbsp;</td>';
						
						//Players' availabilities
						foreach ($team->_players as $player) {
						
							echo '<td style="padding: 5px;"><center>';
						
							if (array_key_exists($player, $availabilities)) {
								//Player has given its availability
								switch ($availabilities[$player]) {
									case Availabilities::AVAILABLE:
										$img = 'tick';
										break;
									case Availabilities::UNSURE:
										$img = 'flag_yellow';
										break;
									case Availabilities::UNAVAILABLE:
										$img = 'cross';
										break;
								}
								
							} else {
								//No availability given yet
								$img = 'clock';
							}
						
							if ($player == ArghSession::get_username()) {
							
								//Hey, that's me !
								echo '<div id="container_'.$proposal_id.'"><select name="" style="background: url(\'/ligue/img/icons/'.$img.'.png\') no-repeat;" id="'.$proposal_id.'" onChange="update_symbol(this);">
										<option'.attr_($availabilities[$player], Availabilities::NOT_ANSWERED_YET).' value="'.Availabilities::NOT_ANSWERED_YET.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;?</option>
										<option'.attr_($availabilities[$player], Availabilities::AVAILABLE).' value="'.Availabilities::AVAILABLE.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.Lang::OK.'</option>
										<option'.attr_($availabilities[$player], Availabilities::UNSURE).' value="'.Availabilities::UNSURE.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.Lang::UNSURE.'</option>
										<option'.attr_($availabilities[$player], Availabilities::UNAVAILABLE).' value="'.Availabilities::UNAVAILABLE.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.Lang::UNAVAILABLE.'</option>
									</select></div>';
								
							} else {

								echo '<img src="/ligue/img/icons/'.$img.'.png" />';
							}
							
							echo '</center></td>';
						}
						
						echo '</tr>';
					}
				}
				echo '</table><br />';
				echo '</div><br />';
			}
			
			//Date proposal management
			if ($hasAdminRights) {
				ArghPanel::end_tag();
				ArghPanel::begin_tag(Lang::TEAM_ADD_DATE_PROPOSITION);
				
				echo '<form method="POST" action="?f=clan_home"><table>
					<colgroup>
						<col width="150" />
						<col />
					</colgroup>
					<tr>
						<td>'.Lang::PLAYDAY.'</td>
							<td><select name="playday">';
				foreach ($playdays as $playday => $opponent) {
					echo '<option value="'.$playday.'">'.$playday.' - '.$opponent.'</option>';
				}
				echo '</select></td>
					</tr>
					<tr>
						<td>'.Lang::DATE.'</td>
						<td><input name="date" id="datepicker" type="text" />&nbsp;<input name="hour" type="text" size="2" />'.Lang::HOUR_LETTER.'&nbsp;<input name="minute" type="text" size="2" />'.Lang::MINUTE_LETTER.'</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
					</tr>
				</table><br />
				<center><input type="submit" value="'.Lang::CREATE.'" name="create_date_proposal" style="width: 100px;" /></center></form>';
			}
		}
		
		ArghPanel::end_tag();
		
	} else {
		//No clan
	}
	
	
?>