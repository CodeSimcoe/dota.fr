<?php
	include 'ajax/get_division_recap.php';
	
	ArghPanel::begin_tag(Lang::CALENDAR);
	echo '<table class="listing">';

	//
	//$two_weeks = 1209600;
	
	for ($i = 1; $i <= 10; $i++) {
		$requ = "SELECT date_defaut FROM lg_matchs WHERE j='".$i."' AND divi='".$divi."' LIMIT 1";
		$ta = mysql_query($requ);
		if (mysql_num_rows($ta)) {
			while ($li = mysql_fetch_row($ta)) {
				//echo '<tr><td><strong>'.Lang::PLAYDAY.' '.$i.'</strong></td><td colspan="2"><span class="info">'.date(Lang::DATE_FORMAT_HOUR, $li[0]).' - '.date(Lang::DATE_FORMAT_HOUR, $li[0] + $two_weeks).'</span></td></tr><tr class="line" style="height: 1px;"><td colspan="3"></td></tr>';
				echo '<tr><td><strong>'.Lang::PLAYDAY.' '.$i.'</strong></td><td colspan="2"><span class="info">'.date(Lang::DATE_FORMAT_HOUR, $li[0]).'</span></td></tr><tr class="line" style="height: 1px;"><td colspan="3"></td></tr>';
			}
		
			$request = "SELECT m.*, c1.tag AS tag1, c2.tag AS tag2
						FROM lg_matchs m, lg_clans c1, lg_clans c2
						WHERE j = '".$i."'
						AND c1.id = m.team1
						AND c2.id = m.team2
						AND m.divi = '".$divi."'
						AND etat > 0";
			$table = mysql_query($request);
			$a = 0;
			while ($line = @mysql_fetch_object($table)) {
				$alt = Alternator::get_alternation($a);
				echo '<tr style="height: 24px;"><td'.$alt.'>';
				switch ($line->etat) {
					case MatchStates::TEAM_ONE_DEFAULT_WIN:
					case MatchStates::TEAM_ONE_REGULAR_WIN:
					case MatchStates::TEAM_ONE_WINS_WITH_SCOURGE_DEFWIN:
					case MatchStates::TEAM_ONE_WINS_WITH_SENTINEL_DEFWIN:
						echo '<span class="win">'.$line->tag1.'</span> - '.$line->tag2;
						break;				
					case MatchStates::DRAW_REGULAR_SENTINEL:
					case MatchStates::DRAW_REGULAR_SCOURGE:
						echo '<span class="draw">'.$line->tag1.'</span> - <span class="draw">'.$line->tag2.'</span>';
						break;
					case MatchStates::TEAM_TWO_DEFAULT_WIN:
					case MatchStates::TEAM_TWO_REGULAR_WIN:
					case MatchStates::TEAM_TWO_WINS_WITH_SENTINEL_DEFWIN:
					case MatchStates::TEAM_TWO_WINS_WITH_SCOURGE_DEFWIN:
						echo $line->tag1.' - <span class="win">'.$line->tag2.'</span>';
						break;
					case MatchStates::NOT_PLAYED_YET:
						echo $line->tag1.' - '.$line->tag2;
						break;
					case MatchStates::ADMIN_CLOSED:
						echo '<span class="info">'.$line->tag1.'</span> - <span class="info">'.$line->tag2.'</span>';
					}
						echo '</td><td'.$alt.'><a href="?f=match&team1='.$line->team1.'&team2='.$line->team2.'">'.Lang::MATCH_SHEET.'</a>';
						
						$r = "SELECT * FROM lg_uploads WHERE match_id = '".$line->id."'";
						$t = mysql_query($r);
						if (mysql_num_rows($t) > 0) {
							$replay = false;
							$screen = false;
							$nb_replay = 0;
							$nb_screen = 0;
							while ($l = mysql_fetch_object($t)) {
								if (substr($l->fichier,-3) == 'w3g') {
									$replay = true;
									$nb_replay++;
								} else {
									$screen = true;
									$nb_screen++;
								}
							}
							if ($replay) {
								echo ' <img src="icon_w3g.jpg" alt="" /> ('.$nb_replay.')';
							}
							
							if ($screen) {
								echo ' <img src="icon_jpg.jpg" alt="" /> ('.$nb_screen.')';
							}
						}
						echo '</td><td'.$alt.'>'.Lang::STATUS.': <b>';
						if ($line->etat == 1) {
							if ($line->team_propose == -1) {
								echo '<span class="red">'.Lang::PLANIFIED.' ('.Lang::ADMIN.')</span>';
							} else if ($line->date_acceptation > 0) {
								echo '<span class="red">'.Lang::PLANIFIED.'</span>';
							} else if ($line->date_proposee > 0) {
								echo '<span class="draw">'.Lang::PROPOSED_DATE.'</span>';
							} else {
								echo '<span class="win">'.Lang::OPEN.'</span>';
							}
						} else {
							echo '<span class="lose">'.Lang::CLOSED.'</span>';
						}
						echo '</b></td></tr>';
			}
			echo '<tr><td colspan="3">&nbsp;</td></tr>';
		}
	}
	
	echo '</table>';
	ArghPanel::end_tag();