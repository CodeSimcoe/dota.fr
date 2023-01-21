<?php
	require '/home/www/ligue/mysql_connect.php';
	require '/home/www/ligue/ladder_functions.php';
	require '/home/www/ligue/classes/ArghPanel.php';
	require '/home/www/ligue/classes/Alternator.php';
	require '/home/www/ligue/lang/frFR/Lang.php';

	$out = ArghPanel::str_begin_tag(Lang::LADDER_TEAM_RANKING);
	$out .= '<center><img src="img/rank.jpg" alt="'.Lang::RANKING.'" /></center><br />';
	
	$out .= '<table class="listing">
		<colgroup>
			<col width="15%" />
			<col width="35%" />
			<col width="25%" />
			<col width="25%" />
		</colgroup>
		<thead>
			<tr>
				<th>#</td>
				<th>'.Lang::TEAM.'</td>
				<th>'.Lang::TAG.'</td>
				<th>'.Lang::LADDER_XP_MEAN.' <sub>'.Lang::LADDER_XP_MAX.'</sub></td>
			</tr>
			<tr>
				<td colspan="4" class="line">&nbsp;</td>
			</tr>
		</thead>
		<tbody>';


	$req = "
		SELECT c.id, c.name, ROUND(AVG(u.pts)) AS Moyenne, c.tag, MAX(u.pts)
		FROM lg_users u, lg_clans c
		WHERE u.clan = c.id
		GROUP BY u.clan
		HAVING (COUNT(u.username) >= 5)
		ORDER BY Moyenne DESC
		LIMIT 25";
	
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
		
		$out .= '<tr'.$alt.'>
				<td><i>'.$i.'.</i>'.$cup.'</td>
				<td><a href="?f=team_profile&id='.$l[0].'">'.$l[1].'</a></td>
				<td><a href="?f=team_profile&id='.$l[0].'">'.$l[3].'</a></td>
				<td>'.XPColorize($l[2]).' &nbsp; <sub>'.XPColorize($l[4]).'</sub></td>
			</tr>';
	}

	$out .= '</tbody></table>';
	$out .= ArghPanel::str_end_tag();
	
	//Ecriture
	$filename = '/var/www/ligue/ladder_rankc.php';
	$handle = fopen($filename, 'w');
	fwrite($handle, $out);
?>
