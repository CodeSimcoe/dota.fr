<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::LADDER_ADMIN
		)
	);

	ArghPanel::begin_tag('Ladder - Top Leavers');
	
	function percentage_color($perc) {
		//$red = (int)($perc * 2.55);
		$red = min((int)($perc * 4), 255);
		$code = dechex(255 - $red);
		return 'ff'.$code.$code;
	}
	
	$query = "	SELECT player,
					COUNT(*) AS TotalGames,
					ROUND(100*SUM(CASE resultat WHEN 'left' THEN 1 ELSE 0 END)/COUNT(*), 1) AS LeavePercentage,
					ROUND(100*SUM(CASE resultat WHEN 'away' THEN 1 ELSE 0 END)/COUNT(*), 1) AS AwayPercentage
				FROM lg_ladderfollow
				WHERE resultat != ''
				GROUP BY player
				HAVING TotalGames > 10
				ORDER BY LeavePercentage DESC
				LIMIT 50";
	$result = mysql_query($query);
	
	echo '<table class="listing">
		<colgroup>
			<col width="34%" />
			<col width="22%" />
			<col width="22%" />
			<col width="22%" />
		</colgroup>
		<thead>
			<tr>
				<th>'.Lang::PLAYER.'</th>
				<th>'.Lang::NB_GAMES.'</th>
				<th>'.ucfirst(Lang::LEFTS).'</th>
				<th>'.ucfirst(Lang::TIMES_NOT_SHOW_UP).'</th>
			</tr>
		</thead>
		<tbody>';
	$i = 0;
	while ($line = mysql_fetch_object($result)) {
		echo '<tr'.Alternator::get_alternation($i).'>
				<td><a href="?f=player_profile&player='.$line->player.'">'.$line->player.'</a></td>
				<td>'.$line->TotalGames.'</td>
				<td style="color: #'.percentage_color($line->LeavePercentage).';">'.$line->LeavePercentage.'%</td>
				<td>'.$line->AwayPercentage.'%</td>
			</tr>';
	}
	echo '</tbody></table>';
	
	ArghPanel::end_tag();
?>