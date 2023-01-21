<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/Alternator.php';
	require_once ABSOLUTE_PATH.'classes/RightsMode.php';
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::LADDER_ADMIN,
			RightsMode::VIP_HEADADMIN,
			RightsMode::VIP_ADMIN
		)
	);
	
	require_once ABSOLUTE_PATH.'mysql_connect.php';
	require_once ABSOLUTE_PATH.'misc.php';
	
	
	$start = empty($_GET['start']) ? 0 : (int)$_GET['start'];
	$max_results = 100;
	//$max_results = empty($_GET['max_results']) ? 100 : (int)$_GET['max_results'];
	
	$req = "SELECT u.username, u.ggc, l.quand, l.par_qui, l.raison, l.duree, l.id
			FROM lg_ladderbans l, lg_users u
			WHERE u.username = l.qui";
			
	//if (!empty($_GET['filter'])) {
		$req .= " AND u.username LIKE '%".mysql_real_escape_string($_GET['filter'])."%' ";
	//}
	
	$req .= "ORDER BY l.id DESC LIMIT $start, $max_results";
			
	$table = mysql_query($req);
	
	echo '<table class="listing">
		<colgroup>
			<col width="25%" />
			<col width="15%" />
			<col width="52%" />
			<col width="8%" />
		</colgroup>
		<thead>
			<tr>
				<th>'.Lang::USERNAME.'</th>
				<th>'.Lang::LADDER_BAN_REMAINING_LENGTH.'</th>
				<th>'.Lang::REASON.'</th>
				<th>'.Lang::ACTION.'</th>
			</tr>
		</thead>
		<tbody>';
	
	$i = 0;
	while ($line = mysql_fetch_row($table)) {
		echo '<tr'.Alternator::get_alternation($i).'>
				<td><a href="?f=player_profile&player='.$line[0].'">'.truncate($line[0], 20).'</a><br /><span class="info"><i>'.truncate($line[1], 20).'</i></span></td>
				<td><span class="draw">'.remainingTime($line[2], $line[5]).'</span></td>
				<td><span class="lose">'.truncate(stripslashes(htmlentities($line[4])), 36).'</span> / <b>'.truncate($line[3], 14).'</b></td>
				<td><a href="?f=admin_ladder_bans&action=delete&id='.$line[6].'"><img src="img/del.png" alt="" /></a></td>
			</tr>';
	}
	
	echo '</tbody></table>';
?>