<?php
	require '/home/www/ligue/mysql_connect.php';
	require '/home/www/ligue/laddervip_functions.php';
	require '/home/www/ligue/classes/ArghPanel.php';
	require '/home/www/ligue/classes/Alternator.php';
	require '/home/www/ligue/lang/frFR/Lang.php';
	
	$out = ArghPanel::str_begin_tag(Lang::LADDER_PLAYER_RANKING.' - <span class="vip">'.Lang::VIP.'</span>');
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
				<th>#</th>
				<th>'.Lang::USERNAME.'</th>
				<th>'.Lang::TEAM.'</th>
				<th>'.Lang::STREAK.'</th>
				<th>'.Lang::XP.'</th>
			</tr>
		</thead>
		<tbody>';


	$req = "SELECT u.username, c.tag, u.pts_vip, count(f.player), c.id, u.country
			FROM lg_laddervip_follow f, lg_users u LEFT JOIN lg_clans c ON u.clan = c.id
			WHERE u.username = f.player
			AND f.xp != 0
			GROUP BY f.player
			HAVING count(f.player) > 0
			ORDER BY u.pts_vip DESC, count(f.player) DESC, u.username ASC
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
			<td><i>'.$i.'.</i>&nbsp;'.$cup.'</td>
			<td><b>'.$flag.' <a href="?f=player_profile&amp;player='.$l[0].'">'.$l[0].'</a></b></td>
			<td><a href="?f=team_profile&id='.$l[4].'">'.$l[1].'</a></td>
			<td><b>'.$st.' '.WinningStreak($st).'</b></td>
			<td><b>'.XPColorize($l[2]).'</b><span class="info">['.$l[3].']</span></td>
		</tr>';
	}


	$out .= '</tbody></table>';
	$out .= ArghPanel::str_end_tag();

	//Ecriture
	$filename = '/var/www/ligue/laddervip_rank.php';
	$handle = fopen($filename, 'w');
	fwrite($handle, $out);

/*
	require '/home/www/ligue/mysql_connect.php';
	require '/home/www/ligue/laddervip_functions.php';
	require '/home/www/ligue/classes/ArghPanel.php';
	require '/home/www/ligue/classes/Alternator.php';
	require '/home/www/ligue/lang/frFR/Lang.php';
	
	function giveXPRank($rank) {
		//Fibonacci sum
		$values = array(1, 3, 6, 11, 19, 32, 53, 87, 142, 231, 375, 608, 985, 1595, 2582, 4179, 6763, 10944, 17709, 28655);
		foreach ($values as $key => $val) {
			if ($rank <= $val) {
				return $key + 1;
			}
		}
		return 0;
	}
	
	function giveGamesRank($games) {
		$values = array(5, 25, 50, 75, 100, 200, 300, 400 ,500 , 750, 1500, 2000);
		foreach ($values as $key => $val) {
			if ($games <= $val) {
				return '<img src="img/vip_ranks/games/'.($key + 1).'.jpg" alt="" />';
			}
		}
		return '';
	}
	
	function giveWinsRank($wins) {
		$values = array(5, 10, 25, 50, 100, 200, 300 ,400 , 500, 750, 1000, 2000);
		foreach ($values as $key => $val) {
			if ($wins <= $val) {
				return '<img src="img/vip_ranks/wins/'.($key + 1).'.jpg" alt="" />';
			}
		}
		return '';
	}
	
	$out = '<?php ArghPanel::begin_tag(Lang::LADDERVIP_RANK); ?>';
	$out .= '<center><img src="img/rank.jpg" alt="<?php echo Lang::RANKING; ?>" /></center><br />';
	
	$out .= '<table class="listing">
			<colgroup>
				<col width="10%" />
				<col width="20%" />
				<col width="20%" />
				<col width="20%" />
				<col width="20%" />
			</colgroup>
		<thead>
			<tr>
				<td>#</td>
				<?php
					echo \'<td><b>\'.Lang::USERNAME.\'</b></td>
					<td align="center"><b>\'.Lang::GAMES.\'</b></td>
					<td align="center"><b>\'.Lang::WINS.\'</b></td>
					<td align="center"><b>\'.Lang::RANK.\'</b></td>\';
				?>
			</tr>
			<tr>
				<td colspan="5" class="line">&nbsp;</td>
			</tr>
		</thead>
		<tbody>';
			
	$req = "SELECT *
			FROM lg_laddervip_players
			WHERE played > 0
			ORDER BY xp DESC, wins / loses DESC";
			
	$t = mysql_query($req);
	
	$i = 0;
	while ($l = mysql_fetch_object($t)) {
		$alt = $i++ % 2 ? true : false;
		$ranks = array(1 => 'first', 2 => 'second', 3 => 'third');
		if ($i <= 3) {
			$cup = ' <img src="'.$ranks[$i].'.gif" alt="'.$i.'" />';
		} else {
			$cup = '';	
		}
		
		$rank = giveXPRank($i);
		
		$out .= '<tr'.($alt ? ' style="border-top: 1px solid #303036;"' : '').'>
			<td rowspan="2"><i>'.$i.'.</i>'.$cup.'</td>
			<td rowspan="2"><b><a href="?f=player_profile&player='.$l->username.'">'.$l->username.'</a></b></td>
			<td align="center">'.giveGamesRank($l->played).'</td>
			<td align="center">'.giveWinsRank($l->wins).'</td>
			<td align="center"><img src="img/vip_ranks/xp/'.min($rank, 10).'.gif" alt="" /></td>
		</tr>
		<tr'.($alt ? ' style="border-bottom: 1px solid #303036;"' : '').'>
			<td align="center"><b><span class="vip">'.$l->played.'</span></b></td>
			<td align="center"><b><span class="win">'.$l->wins.'</span> / <span class="lose">'.$l->loses.'</span></b></td>
			<td align="center"><b><?php echo Lang::RANK; ?> '.$rank.'</b></td>
		</tr>';
	}


	$out .= '</tbody></table>';
	$out .= ArghPanel::str_end_tag();

	//Ecriture
	$filename = '/var/www/ligue/laddervip_rank.php';
	$handle = fopen($filename, 'w');
	fwrite($handle, $out);
*/
?>
