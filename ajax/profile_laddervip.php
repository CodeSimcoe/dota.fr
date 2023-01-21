<table>
<?php
	if (isset($_GET['player'])) {
		//Début verif
		$req = "SELECT username, pts_vip
				FROM lg_users
				WHERE username='".$_GET['player']."'";
		$t=mysql_query($req);
		if (mysql_num_rows($t)) {
			$l = mysql_fetch_row($t);
			echo '<tr><td><img src="img/xp_vip.gif"> XP: </td><td><span class="vip"><b>'.$l[1].'</b></span></td></tr>';
			
			//Wins, Draws, Losses
			$treq = "SELECT count(id)
					FROM lg_laddervip_follow
					WHERE player = '".$l[0]."'
					AND xp > 0";
			$tt = mysql_query($treq);
			$tl = mysql_fetch_row($tt);
			$wins = $tl[0];
			
			$treq = "SELECT count(id)
					FROM lg_laddervip_follow
					WHERE player = '".$l[0]."'
					AND resultat = 'left'";
			$tt = mysql_query($treq);
			$tl = mysql_fetch_row($tt);
			$leaves = $tl[0];
			
			$treq = "SELECT count(id)
					FROM lg_laddervip_follow
					WHERE player = '".$l[0]."'
					AND xp < 0
					AND resultat = 'lose'";
			$tt = mysql_query($treq);
			$tl = mysql_fetch_row($tt);
			$losses = $tl[0];
			
			$treq = "SELECT count(id)
					FROM lg_laddervip_follow
					WHERE player = '".$l[0]."'
					AND xp < 0
					AND resultat = 'away'";
			$tt = mysql_query($treq);
			$tl = mysql_fetch_row($tt);
			$aways = $tl[0];
			
			$total = $wins + $losses + $leaves + $aways;
			if ($total > 0) {
				$win_percent = round(100*$wins/$total, 2);
				$loss_percent = round(100*$losses/$total, 2);
				$leave_percent = round(100*$leaves/$total, 2);
				$away_percent = round(100*$aways/$total, 2);
			} else {
				$win_percent = '-';
				$loss_percent = '-';
				$leave_percent = '-';
				$away_percent = '-';
			}
			
			$results = '<span class="win">'.$wins.'</span> - <span class="draw">'.$draws.'</span> - <span class="lose">'.$losses.'</span> - <span class="info">'.$others.'</span>';
			echo '<tr><td valign="top">Stats Ladder: </td><td>
			<b><span class="win">'.$wins.'</span></b> victoires <span class="info">('.$win_percent.'%)</span><br />
			<b><span class="lose">'.$losses.'</span></b> défaites <span class="info">('.$loss_percent.'%)</span><br />
			<b><span class="draw">'.$leaves.'</span></b> parties quittées <span class="info">('.$leave_percent.'%)</span><br />
			<b><span class="info">'.$aways.'</span></b> fois non venu <span class="info">('.$away_percent.'%)</span>
			</td></tr>';
			
			//Camembert
			if ($total > 0) {
				echo '<tr><td colspan="2">&nbsp;</td></tr>';
				echo '<tr><td colspan="2"><center>';
				//Google Chart API (PNG)
				echo '<img src="http://chart.apis.google.com/chart?cht=p3&chd=t:'.$wins.'.0,'.$losses.'.0,'.$leaves.'.0,'.$aways.'.0&chs=450x150&chl=Victoires|Defaites|Quittees|Pas%20venu&chco=66ff66,ff0000,ffff33,999999&chf=bg,s,000000"';
				echo '</center></td></tr>';
				echo '<tr><td colspan="2">&nbsp;</td></tr>';
			}
		}
	}
?>
</table>