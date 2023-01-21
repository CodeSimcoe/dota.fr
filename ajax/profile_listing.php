<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/var/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'classes/Alternator.php';
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';

	echo '<table class="listing">
		<colgroup>
			<col width="35%" />
			<col width="25%" />
			<col width="20%" />
			<col width="20%" />
		</colgroup>';
	
	if (isset($_GET['player'])) {
		$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
		
		$limit = ArghSession::is_gold() ? 100 : 25;
		
		echo '<tr><td colspan="4"><center><img src="img/listing_normal.jpg" alt="" /></center></td></tr>';
		echo '<tr><td colspan="4">&nbsp;</td></tr>';
		
		//NORMAL
		$req = "SELECT l.opened, f.player, f.resultat, f.xp, l.id
				FROM lg_laddergames l, lg_ladderfollow f
				WHERE l.id = f.game_id
				AND status = 'closed'
				AND f.player = '".$player."'
				ORDER BY id DESC
				LIMIT ".$limit;
				
		$t = mysql_query($req);
		
		if (mysql_num_rows($t) > 0) {
			echo '<tr>
					<td><b>'.htmlentities(Lang::DATE).'</b></td>
					<td><b>'.htmlentities(Lang::RESULT).'</b></td>
					<td><b>'.htmlentities(Lang::XP).'</b></td>
					<td><b>'.htmlentities(Lang::GAME_ID).'</b></td>
				</tr>';
			echo '<tr><td colspan="4" class="line"></td></tr>';
			$i = 0;
			while ($l = mysql_fetch_row($t)) {
				
				switch ($l[2]) {
					case 'win':
						$result = '<span class="win">'.htmlentities(Lang::PIE_WIN).'</span>';
						$score = '<span class="win">+'.$l[3].'</span>';
						break;
						
					case 'left':
						$result = '<span class="draw">'.htmlentities(Lang::PIE_LEFT).'</span>';
						$score = '<span class="draw">'.($l[3] > 0 ? '+' : '').$l[3].'</span>';
						break;
						
					case 'away':
						$result = '<span class="info">'.htmlentities(Lang::PIE_AWAY).'</span>';
						$score = '<span class="info">'.$l[3].'</span>';
						break;
					
					case 'lose':
						$result = '<span class="lose">'.htmlentities(Lang::PIE_LOSS).'</span>';
						$score = '<span class="lose">'.$l[3].'</span>';
						break;
						
					default:
						$result = htmlentities(Lang::PIE_CLOSED);
						$score = '0';
				}
				
				echo '<tr'.Alternator::get_alternation($i).'>
						<td>'.date(Lang::DATE_FORMAT_HOUR, $l[0]).'</td>
						<td>'.$result.'</td>
						<td><b>'.$score.'</b></td>
						<td><a href="?f=ladder_game&id='.$l[4].'">#'.$l[4].'</a></td>
					</tr>';
			}
		} else {
			echo '<tr><td colspan="4"><center>'.htmlentities(Lang::LADDER_NO_GAME).'</center></td></tr>';
		}
		
		echo '<tr><td colspan="4">&nbsp;</td></tr>';
		echo '<tr><td colspan="4">&nbsp;</td></tr>';
		echo '<tr><td colspan="4"><center><img src="img/listing_vip.jpg" /></center></td></tr>';
		echo '<tr><td colspan="4">&nbsp;</td></tr>';
		
		//VIP
		$req = "SELECT l.opened, f.player, f.resultat, f.xp, l.id
				FROM lg_laddervip_games l, lg_laddervip_follow f
				WHERE l.id = f.game_id
				AND status = 'closed'
				AND f.player = '".$player."'
				ORDER BY id DESC
				LIMIT ".$limit;
				
		$t = mysql_query($req);
		
		if (mysql_num_rows($t) > 0) {
			echo '<tr>
					<td><b>'.htmlentities(Lang::DATE).'</b></td>
					<td><b>'.htmlentities(Lang::RESULT).'</b></td>
					<td><b>'.htmlentities(Lang::XP).'</b></td>
					<td><b>'.htmlentities(Lang::GAME_ID).'</b></td>
				</tr>';
			echo '<tr><td colspan="4" class="line"></td></tr>';
			$i = 0;
			while ($l = mysql_fetch_row($t)) {
				
				switch ($l[2]) {
					case 'win':
						$result = '<span class="win">'.htmlentities(Lang::PIE_WIN).'</span>';
						$score = '<span class="win">+</span>';
						break;
						
					case 'left':
						$result = '<span class="draw">'.htmlentities(Lang::PIE_LEFT).'</span>';
						$score = '<span class="draw">-</span>';
						break;
						
					case 'away':
						$result = '<span class="info">'.htmlentities(Lang::PIE_AWAY).'</span>';
						$score = '<span class="info">/</span>';
						break;
					
					case 'lose':
						$result = '<span class="lose">'.htmlentities(Lang::PIE_LOSS).'</span>';
						$score = '<span class="lose">-</span>';
						break;
						
					default:
						$result = htmlentities(Lang::PIE_CLOSED);
						$score = '=';
				}
				
				echo '<tr'.Alternator::get_alternation($i).'>
						<td>'.date(Lang::DATE_FORMAT_HOUR, $l[0]).'</td>
						<td>'.$result.'</td>
						<td><b>'.$score.'</b></td>
						<td><a href="?f=laddervip_game&id='.$l[4].'">#'.$l[4].'</a></td>
					</tr>';
			}
		} else {
			echo '<tr><td colspan="4"><center>'.htmlentities(Lang::LADDER_NO_GAME).'</center></td></tr>';
		}
		
		echo '</table>';
	}
?>