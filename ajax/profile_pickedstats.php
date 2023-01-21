<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/var/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'classes/LadderStates.php';
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';
	
	if (isset($_GET['player'])) {
	
		$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
		
		$infos = array();
		$infos['cap'] = 0;
		$infos['pick1'] = 0;
		$infos['pick2'] = 0;
		$infos['pick3'] = 0;
		$infos['pick4'] = 0;
		$infos['pick5'] = 0;
		
		$req = "SELECT *
				FROM lg_laddervip_games
				WHERE (
					cap1 = '$player'
					OR cap2 = '$player'
					OR p1 = '$player'
					OR p2 = '$player'
					OR p3 = '$player'
					OR p4 = '$player'
					OR p5 = '$player'
					OR p6 = '$player'
					OR p7 = '$player'
					OR p8 = '$player'
				) AND winner != 'none'
				AND status = 'closed'";
		$t = mysql_query($req);
		while ($l = mysql_fetch_object($t)) {
			if ($l->cap1 == $player or $l->cap2 == $player) {
				//Capitaine
				$infos['cap']++;
			} else {
				//Non capitaine
				if ($l->pp1 == $player) {
					$infos['pick1']++;
				} elseif ($l->pp2 == $player or $l->pp3 == $player) {
					$infos['pick2']++;
				} elseif ($l->pp4 == $player or $l->pp5 == $player) {
					$infos['pick3']++;
				} elseif ($l->pp6 == $player or $l->pp7 == $player) {
					$infos['pick4']++;
				} else {
					$infos['pick5']++;
				}
			}
		}
		
		$gamesPlayed = array_sum($infos);
		
		echo '<table class="simple">';
		
		if ($gamesPlayed > 0) {
		
			echo '<tr><td width="25"></td><td colspan="3"><b>Statistiques de pick en Ladder VIP</b></td><td width="25"></td></tr>';
			echo '<tr><td></td><td colspan="3" class="line"></td><td></td></tr>';
			
			
			echo '<tr><td></td>
					<td colspan="2">Parties jou&eacute;es</td>
					<td align="right"><b>'.$gamesPlayed.'</b></td>
				<td></td></tr>';
			echo '<tr><td></td>
					<td colspan="2" class="alternate">Capitaine</td>
					<td class="alternate" align="right">'.$infos['cap'].'</td>
				<td></td></tr>';
			echo '<tr><td></td>
					<td colspan="2">1<sup>st</sup> picked</td>
					<td align="right">'.$infos['pick1'].'</td>
				<td></td></tr>';
			echo '<tr><td></td>
					<td colspan="2" class="alternate">2<sup>nd</sup> picked:</td>
					<td class="alternate" align="right">'.$infos['pick2'].'</td>
				<td></td></tr>';
			echo '<tr><td></td>
					<td colspan="2">3<sup>rd</sup> picked</td>
					<td align="right">'.$infos['pick3'].'</td>
				<td></td></tr>';
			echo '<tr><td></td>
					<td colspan="2" class="alternate">4<sup>th</sup> picked:</td>
					<td class="alternate" align="right">'.$infos['pick4'].'</td>
				<td></td></tr>';
			echo '<tr><td></td>
					<td colspan="2">Last picked</td>
					<td align="right">'.$infos['pick5'].'</td>
				<td></td></tr>';
				
			echo '<tr><td></td><td colspan="3">&nbsp;</td><td></td></tr>';
			$img = 'http://chart.apis.google.com/chart?cht=p3&chd=t:'.$infos['cap'].','.$infos['pick1'].','.$infos['pick2'].','.$infos['pick3'].','.$infos['pick4'].','.$infos['pick5'].'&chs=450x150&chl=capitaine|1st%20picked|2nd%20picked|3rd%20picked|4th%20picked|last%20picked&chco=0066ff&chf=bg,s,000000';
			echo '<tr><td></td><td colspan="3"><center><img src="'.$img.'" alt="" /></center></td><td></td></tr>';
		}
		
		if ($infos['cap'] > 0) {
			
			$heroes = array();
			$wins = array();
			$losses = array();
			
			$players = array();
			$pwins = array();
			$plosses = array();
			
			//Récupération des parties
			$req = "SELECT * FROM lg_laddervip_games WHERE cap1 = '".$player."' AND winner != 'none'";
			$t = mysql_query($req);
			while ($l = mysql_fetch_object($t)) {
				$heroes[$l->h1]++;
				$heroes[$l->h4]++;
				$heroes[$l->h5]++;
				$heroes[$l->h8]++;
				$heroes[$l->h9]++;
				
				$players[$l->pp1]++;
				$players[$l->pp4]++;
				$players[$l->pp5]++;
				$players[$l->pp8]++;
				
				if ($l->winner == 'se') {
					$wins[$l->h1]++;
					$wins[$l->h4]++;
					$wins[$l->h5]++;
					$wins[$l->h8]++;
					$wins[$l->h9]++;
					
					$pwins[$l->pp1]++;
					$pwins[$l->pp4]++;
					$pwins[$l->pp5]++;
					$pwins[$l->pp8]++;
				} else {
					$losses[$l->h1]++;
					$losses[$l->h4]++;
					$losses[$l->h5]++;
					$losses[$l->h8]++;
					$losses[$l->h9]++;
					
					$plosses[$l->pp1]++;
					$plosses[$l->pp4]++;
					$plosses[$l->pp5]++;
					$plosses[$l->pp8]++;
				}
			}
			
			$req = "SELECT * FROM lg_laddervip_games WHERE cap2 = '".$player."' AND winner != 'none'";
			$t = mysql_query($req);
			while ($l = mysql_fetch_object($t)) {
				$heroes[$l->h2]++;
				$heroes[$l->h3]++;
				$heroes[$l->h6]++;
				$heroes[$l->h7]++;
				$heroes[$l->h10]++;
				
				$players[$l->pp2]++;
				$players[$l->pp3]++;
				$players[$l->pp6]++;
				$players[$l->pp7]++;
				
				if ($l->winner == 'sc') {
					$wins[$l->h2]++;
					$wins[$l->h3]++;
					$wins[$l->h6]++;
					$wins[$l->h7]++;
					$wins[$l->h10]++;
					
					$pwins[$l->pp2]++;
					$pwins[$l->pp3]++;
					$pwins[$l->pp6]++;
					$pwins[$l->pp7]++;
				} else {
					$losses[$l->h2]++;
					$losses[$l->h3]++;
					$losses[$l->h6]++;
					$losses[$l->h7]++;
					$losses[$l->h10]++;
					
					$plosses[$l->pp2]++;
					$plosses[$l->pp3]++;
					$plosses[$l->pp6]++;
					$plosses[$l->pp7]++;
				}
			}
			
			arsort($heroes);
			arsort($players);
			
			//Listing
			echo '<tr><td></td><td colspan="3">&nbsp;</td><td></td></tr>';
			echo '<tr><td></td><td colspan="3">&nbsp;</td><td></td></tr>';
			echo '<tr><td width="25"></td><td colspan="3"><b>Statistiques de picks de h&eacute;ros en tant que capitaine</b></td><td width="25"></td></tr>';
			echo '<tr><td></td><td colspan="3" class="line"></td><td></td></tr>';
			$i = 0;
			foreach ($heroes as $hero => $nbPicks) {
				if (strlen($hero) > 1) {
					$i++;
					$alt = ($i%2 == 0) ? ' class="alternate"' : '';
					echo '<tr><td></td>
							<td'.$alt.' width="32"><img src="/ligue/img/heroes/'.$hero.'.gif" width="32" height="32" title="'.$hero.'" /></td>
							<td'.$alt.' valign="middle">'.$hero.'</td>
							<td'.$alt.' align="right" valign="middle"><b>'.$nbPicks.' - <span class="win">'.(empty($wins[$hero]) ? 0 : $wins[$hero]).'</span></b> / <b><span class="lose">'.(empty($losses[$hero]) ? 0 : $losses[$hero]).'</span></b></td>
						<td></td></tr>';
				}
			}
			
			echo '<tr><td></td><td colspan="3">&nbsp;</td><td></td></tr>';
			echo '<tr><td></td><td colspan="3">&nbsp;</td><td></td></tr>';
			echo '<tr><td width="25"></td><td colspan="3"><b>Statistiques de picks de joueurs en tant que capitaine</b></td><td width="25"></td></tr>';
			echo '<tr><td></td><td colspan="3" class="line"></td><td></td></tr>';
			$i = 0;
			foreach ($players as $pl => $nbPicks) {
				if (strlen($pl) > 1) {
					$i++;
					$alt = ($i%2 == 0) ? ' class="alternate"' : '';
					echo '<tr><td></td>
							<td'.$alt.' colspan="2"><a href="?f=player_profile&player='.$pl.'">'.$pl.'</a></td>
							<td'.$alt.' align="right"><b>'.$nbPicks.' - <span class="win">'.(empty($pwins[$pl]) ? 0 : $pwins[$pl]).'</span></b> / <b><span class="lose">'.(empty($plosses[$pl]) ? 0 : $plosses[$pl]).'</span></b></td>
						<td></td></tr>';
				}
			}
		}
		/*
		echo '<pre>';
		print_r($players);
		echo '</pre>';
		*/
		
		echo '</table>';
	}
?>