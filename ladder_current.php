<?php
	function Minutes($started) {
		return round((time() - $started) / 60, 0);
	}
	
	include('ladder_functions.php');
	
	ArghPanel::begin_tag(Lang::LADDER_RUNNING_GAMES);
	
	include 'campaigns/lol-goa.php';
	echo '<br />';
	
	$query = "SELECT * FROM lg_laddergames WHERE status = 'playing' ORDER BY id DESC";
	$table = mysql_query($query);
	if (mysql_num_rows($table) > 0) {
?>
	<table class="listing">
		<colgroup>
			<col width="15%" />
			<col width="15%" />
			<col width="70%" />
		</colgroup>
		<thead>
			<tr>
				<th><?php echo Lang::GAME_SHARP; ?></th>
				<th><?php echo Lang::LADDER_GAME_DURATION; ?></th>
				<th><?php echo Lang::PLAYERS; ?></th>
			</tr>
			<tr>
				<td colspan="3" class="line">&nbsp;</td>
			</tr>
		</thead>
		<tbody>
<?php
		//Gold -> show friends playing
		$friends = array();
		
		function display_player($player, $list) {
			$out = '<a href="?f=player_profile&player='.$player.'">'.$player.'</a>';
			
			if (in_array($player, $list)) {
				$out = '<img src="img/icons/group.png" alt="" />&nbsp;'.$out.'';
			}
			
			return $out;
		}
		
		if (ArghSession::is_gold()) {
			//Friends + online status
			$query = "SELECT friend FROM lg_friendlist WHERE username = '".ArghSession::get_username()."'";
			$result = mysql_query($query);
			
			while ($row = mysql_fetch_row($result)) $friends[] = $row[0];
		}

		$i = 0;
		$time = time();
		while ($line = mysql_fetch_object($table)) {
			echo '<tr'.Alternator::get_alternation($i).'>
				<td><a href="?f=ladder_game&id='.$line->id.'">#'.$line->id.'</a></td>
				<td>'.round(($time - $line->opened) / 60).' '.Lang::MINUTES_SHORT.'</td>
				<td>';
			
			$players1 = '';
			$players2 = '';
			
			for ($j = 1; $j <= 10; $j++) {
				$pl = 'p'.$j;
				if ($j <= 5) {
					$players1 .= display_player($line->$pl, $friends);
					if ($j != 5) {
						$players1 .= ' / ';
					}
				} else {
					$players2 .=  display_player($line->$pl, $friends);
					if ($j != 10) {
						$players2 .= ' / ';
					}
				}
			}
			
			/*
			for ($j = 1; $j <= 10; $j++) {
				$pl = 'p'.$j;
				if ($j <= 5) {
					$players1 .=  '<a href="?f=player_profile&player='.$line->$pl.'">'.$line->$pl.'</a>';
					if ($j != 5) {
						$players1 .= ' / ';
					}
				} else {
					$players2 .=  '<a href="?f=player_profile&player='.$line->$pl.'">'.$line->$pl.'</a>';
					if ($j != 10) {
						$players2 .= ' / ';
					}
				}
			}
			*/
			echo $players1.'<br />'.$players2.'</td></tr>';
			echo '<tr><td colspan="3">&nbsp;</td></tr>';
		}
	} else {
		echo '<tr><td colspan="3"><center>'.Lang::NO_RUNNING_GAME.'</center></td></tr>';
	}
?>
		</tbody>
	</table>
<?php
	ArghPanel::end_tag();
?>