<?php
	require 'ladder_functions.php';
	
	ArghPanel::begin_tag(Lang::LADDER);
	echo '<center>
			<a href="?f=ladder_rules"><img src="img/lang/'.ArghSession::get_lang().'/ladder_rules.png" alt="" /></a>
			&nbsp;&nbsp;&nbsp;
			<a href="?f=ladder_join"><img src="img/lang/'.ArghSession::get_lang().'/play.png" alt="" /></a>
			<br /><br />
			<a href="?f=ladder_rankp"><img src="img/lang/'.ArghSession::get_lang().'/rankingp.png" alt="" /></a>
			&nbsp;&nbsp;&nbsp;
			<a href="?f=ladder_rankc"><img src="img/lang/'.ArghSession::get_lang().'/rankingt.png" alt="" /></a>
		</center>';
	ArghPanel::end_tag();

	//LADDER
	ArghPanel::begin_tag(Lang::LADDER_TOP_PLAYERS);
	
	$query = "SELECT player, played, win, lose, xp FROM lg_ladder_stats_ranks ORDER BY xp DESC LIMIT 5";
	$result = mysql_query($query);
	
	if (mysql_num_rows($result)) {
		echo '<table class="listing">
			<colgroup>
				<col width="9%" />
				<col width="20%" />
				<col width="17%" />
				<col width="17%" />
				<col width="17%" />
				<col width="20%" />
			</colgroup>
			<thead>
				<tr>
					<th>#</th>
					<th>'.Lang::USERNAME.'</th>
					<th>'.Lang::GAMES.'</th>
					<th>'.Lang::WINS.'</th>
					<th>'.Lang::LOSSES.'</th>
					<th>'.Lang::XP.'</th>
				</tr>
			</thead>
			<tbody>';
		
		$i = 0;
		while ($obj = mysql_fetch_object($result)) {
			echo '<tr '.Alternator::get_alternation($i).'>
					<td><i>'.$i.'.</i></td>
					<td><a href="?f=player_profile&player='.$obj->player.'">'.$obj->player.'</a></td>
					<td><span class="vip">'.$obj->played.'</span></td>
					<td><span class="win">'.$obj->win.'</span></td>
					<td><span class="lose">'.$obj->lose.'</span></td>
					<td>'.XPColorize($obj->xp).'</td>
				</tr>';
		}
		
		echo '</tbody>
			</table>';
	}
	
	ArghPanel::end_tag();
	
	//LADDER VIP
	ArghPanel::begin_tag(Lang::LADDERVIP_TOP_PLAYERS);
	
	$query = "SELECT username, played, wins, loses FROM lg_laddervip_players ORDER BY xp DESC LIMIT 5";
	$result = mysql_query($query);
	
	if (mysql_num_rows($result)) {
		echo '<table class="listing">
			<colgroup>
				<col width="9%" />
				<col width="25%" />
				<col width="22%" />
				<col width="22%" />
				<col width="22%" />
			</colgroup>
			<thead>
				<tr>
					<th>#</th>
					<th>'.Lang::USERNAME.'</th>
					<th>'.Lang::GAMES.'</th>
					<th>'.Lang::WINS.'</th>
					<th>'.Lang::LOSSES.'</th>
				</tr>
			</thead>
			<tbody>';
		
		$i = 0;
		while ($obj = mysql_fetch_object($result)) {
			echo '<tr '.Alternator::get_alternation($i).'>
					<td><i>'.$i.'.</i></td>
					<td><a href="?f=player_profile&player='.$obj->username.'">'.$obj->username.'</a></td>
					<td><span class="vip">'.$obj->played.'</span></td>
					<td><span class="win">'.$obj->wins.'</span></td>
					<td><span class="lose">'.$obj->loses.'</span></td>
				</tr>';
		}
		
		echo '</tbody>
			</table>';
	}
	
	ArghPanel::end_tag();
?>