<?php
	require '/home/www/ligue/mysql_connect.php';
	require '/home/www/ligue/ladder_functions.php';
	require '/home/www/ligue/classes/ArghPanel.php';
	require '/home/www/ligue/classes/Alternator.php';
	require '/home/www/ligue/lang/frFR/Lang.php';
	
	$out = '<?php ArghSession::exit_if_not_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN)); ?>';
	$out .= ArghPanel::str_begin_tag(Lang::LADDER_PLAYER_RANKING);
	$out .= '<center><img src="img/rank.jpg" alt="'.Lang::RANKING.'" /></center><br />';
	
	$out .= '<table class="listing">
			<colgroup>
                                <col width="13%" />
                                <col width="32%" />
                                <col width="13%" />
                                <col width="25%" />
                                <col width="17%" />
                        </colgroup>
		<thead>
			<tr>
				<td>#</td>
				<td>'.Lang::USERNAME.'</td>
				<td>'.Lang::TEAM.'</td>
				<td>'.Lang::STREAK.'</td>
				<td>'.Lang::XP.'</td>
			</tr>
			<tr>
				<td colspan="5" class="line">&nbsp;</td>
			</tr>
		</thead>
		<tbody>';


	$req = "SELECT u.username, c.tag, u.pts, count(f.player), c.id, u.country
			FROM lg_ladderfollow f, lg_users u LEFT JOIN lg_clans c ON u.clan = c.id
			WHERE u.username = f.player
			AND f.xp != 0
			GROUP BY f.player
			HAVING count(f.player) > 0
			ORDER BY u.pts ASC, count(f.player) DESC, u.username ASC
			LIMIT 100";
			
	$t = mysql_query($req);
	
	$i = 0;
	while ($l = mysql_fetch_row($t)) {
		$alt = Alternator::get_alternation($i);
		
		$ranks = array(1 => 'first', 2 => 'second', 3 => 'third');
		if ($i <= 3) {
			$cup = '<img src="'.$ranks[$i].'.gif" alt="'.$i.'" />';
		} else {
			$cup = '';	
		}
		$st = streak($l[0]);
		$flag = (strlen($l[5]) > 0) ? '<img src="img/flag/'.$l[5].'.gif" alt="" /> ' : '';
		$out .= '<tr'.$alt.'>
			<td><i>'.$i.'.</i>'.$cup.'</td>
			<td><b>'.$flag.' <a href="?f=player_profile&amp;player='.$l[0].'">'.$l[0].'</a></b></td>
			<td><a href="?f=team_profile&id='.$l[4].'">'.$l[1].'</a></td>
			<td><b>'.$st.' '.WinningStreak($st).'</b></td>
			<td><b>'.XPColorize($l[2]).'</b><span class="info">['.$l[3].']</span></td>
		</tr>';
	}


	$out .= '</tbody></table>';
	$out .= ArghPanel::str_end_tag();

	//Ecriture
	$filename = '/var/www/ligue/ladder_rankp_bott.php';
	$handle = fopen($filename, 'w');
	fwrite($handle, $out);
?>
