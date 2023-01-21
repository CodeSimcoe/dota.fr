<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/var/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'classes/GooglePie.php';
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';
	require_once ABSOLUTE_PATH.'ladder_functions.php';
	require_once ABSOLUTE_PATH.'misc.php';
?>
<table class="simple">
<?php
	
	$shortUsername = substr($_GET['player'], 0, 25);
	$player = mysql_real_escape_string($shortUsername);
	if (isset($_GET['player'])) {
		//Début verif
		$req = "SELECT username, pts
				FROM lg_users
				WHERE username='".$player."'";
		$t=mysql_query($req);
		if (mysql_num_rows($t)) {
			$l = mysql_fetch_row($t);
			echo '<tr><td colspan="4"><center><img src="ladder_stats.jpg" alt="'.Lang::PROFILE.'" /></center></td></tr>
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr><td></td><td colspan="3"><b>'.Lang::LADDER_NORMAL.'</b></td></tr>
			<tr><td></td><td class="line" colspan="2"></td><td></td></tr>';
			$rank = getLadderRank($shortUsername, false);
			$rank .= '<sup>'.(($rank == 1) ? 'er' : '&egrave;me').'</sup>';
			echo '<tr><td width="25"></td><td><img src="img/xp.gif" alt=""/> '.Lang::XP.': </td><td><b>'.XPColorize($l[1]).' ('.$rank.')</b></td><td width="25"></td></tr>';
			
			$wins = $leaves = $losses = $aways = 0;
			//Wins, Draws, Losses
			$treq = "SELECT xp, resultat
					FROM lg_ladderfollow
					WHERE player = '".$l[0]."'";
			$tt = mysql_query($treq);
			while ($tl = mysql_fetch_object($tt)) {
				if ($tl->resultat == 'win') {
					$wins++;
				} elseif ($tl->resultat == 'lose') {
					$losses++;
				} elseif ($tl->resultat == 'left') {
					$leaves++;
				} elseif ($tl->resultat == 'away') {
					$aways++;
				}
			}
			
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
			echo '<tr><td></td><td valign="top">'.Lang::LADDER_STATS.': </td><td>
			<b><span class="win">'.$wins.'</span></b> '.strtolower(Lang::WINS).' <span class="info">('.$win_percent.'%)</span><br />
			<b><span class="lose">'.$losses.'</span></b> '.strtolower(Lang::LOSSES).' <span class="info">('.$loss_percent.'%)</span><br />
			<b><span class="draw">'.$leaves.'</span></b> '.strtolower(Lang::LEFTS).' <span class="info">('.$leave_percent.'%)</span><br />
			<b><span class="info">'.$aways.'</span></b> '.strtolower(Lang::TIMES_NOT_SHOW_UP).' <span class="info">('.$away_percent.'%)</span>
			</td><td></td></tr>';
			
			//Camembert
			if ($total > 0) {
				echo '<tr><td colspan="4">&nbsp;</td></tr>';
				echo '<tr><td colspan="4"><center>';
				
				//Google Chart API (PNG)
				$gp = new GooglePie();
				$gp->set_size(375, 150);
				$gp->add_slice(new PieSlice(Lang::PIE_WINS, $win_percent, '66ff66'));
				$gp->add_slice(new PieSlice(Lang::PIE_LOSSES, $loss_percent, 'ff0000'));
				$gp->add_slice(new PieSlice(Lang::PIE_LEFTS, $leave_percent, 'ffff33'));
				$gp->add_slice(new PieSlice(Lang::PIE_AWAYS, $away_percent, '999999'));
				
				$gp->render();
				//....
				
				
				/*
				echo '<img src="http://chart.apis.google.com/chart?cht=p3&
					chd=t:'.$win_percent.','.$loss_percent.','.$leave_percent.','.$away_percent.'&
					chs=375x150&
					chl=Victoires|Defaites|Quittees|Pas%20venu&
					chco=66ff66,ff0000,ffff33,999999&
					chf=bg,s,000000"';
				*/
				echo '</center></td></tr>';
				echo '<tr><td colspan="4">&nbsp;</td><td></td></tr>';
				echo '<tr><td colspan="4">&nbsp;</td><td></td></tr>';
			}
			
		}
		
	} else {
		exit();
	}
	
	//Début verif
	$treq = "
			SELECT username, wins, loses, aways, leaves
			FROM lg_laddervip_players
			WHERE username = '".$player."'";
	$tt = mysql_query($treq) or die(mysql_error());
	if (mysql_num_rows($tt) == 1) {
		$stats = mysql_fetch_object($tt);
		$rank = getLadderRank($player, true);
		$rank .= '<sup>'.(($rank == 1) ? 'er' : '&egrave;me').'</sup>';
		echo '<tr><td></td><td colspan="3"><b>'.Lang::LADDER_VIP.'</b></td></tr>
		<tr><td></td><td class="line" colspan="2"></td><td></td></tr>';
		echo '<tr><td></td><td><img src="img/xp_vip.gif" alt="" /> '.Lang::RANKING.': </td><td><b>'.$rank.'</b></td><td></td></tr>';
		
		$wins = $leaves = $losses = $aways = 0;
		
		$wins = $stats->wins;
		$losses = $stats->loses;
		$leaves = $stats->leaves;
		$aways = $stats->aways;
		
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
		echo '<tr><td></td><td valign="top">'.Lang::LADDER_STATS.': </td><td>
			<b><span class="win">'.$wins.'</span></b> '.strtolower(Lang::WINS).' <span class="info">('.$win_percent.'%)</span><br />
			<b><span class="lose">'.$losses.'</span></b> '.strtolower(Lang::LOSSES).' <span class="info">('.$loss_percent.'%)</span><br />
			<b><span class="draw">'.$leaves.'</span></b> '.strtolower(Lang::LEFTS).' <span class="info">('.$leave_percent.'%)</span><br />
			<b><span class="info">'.$aways.'</span></b> '.strtolower(Lang::TIMES_NOT_SHOW_UP).' <span class="info">('.$away_percent.'%)</span>
		</td><td></td></tr>';
		
		//Camembert
		if ($total > 0) {
			echo '<tr><td colspan="4">&nbsp;</td></tr>';
			echo '<tr><td colspan="4"><center>';
			
			
			$gp = new GooglePie();
			$gp->set_size(375, 150);
			$gp->add_slice(new PieSlice(Lang::PIE_WINS, $win_percent, '66ff66'));
			$gp->add_slice(new PieSlice(Lang::PIE_LOSSES, $loss_percent, 'ff0000'));
			$gp->add_slice(new PieSlice(Lang::PIE_LEFTS, $leave_percent, 'ffff33'));
			$gp->add_slice(new PieSlice(Lang::PIE_AWAYS, $away_percent, '999999'));
			
			$gp->render();
			
			//Google Chart API (PNG)
			//echo '<img src="http://chart.apis.google.com/chart?cht=p3&chd=t:'.$win_percent.','.$loss_percent.','.$leave_percent.','.$away_percent.'&chs=375x150&chl=Victoires|Defaites|Quittees|Pas%20venu&chco=66ff66,ff0000,ffff33,999999&chf=bg,s,000000"';
			
			
			echo '</center></td></tr>';
			echo '<tr><td colspan="4">&nbsp;</td></tr>';
		}
	}
?>
</table>