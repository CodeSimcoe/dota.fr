<?php

	function canVouch($player) {
		$req = "SELECT vouchs FROM lg_users WHERE username = '".mysql_real_escape_string($player)."' AND ((rights & ".RightsMode::VIP_VOUCHER.") = ".RightsMode::VIP_VOUCHER.")";
		$t = mysql_query($req);
		if (mysql_num_rows($t) == 1) {
			$l = mysql_fetch_row($t);
			return ($l[0] > 0);
		} else {
			return false;
		}
	}

	$player = '';
	if (isset($_GET['player'])) {
		$player = substr($_GET['player'], 0, 25);
	}

	if ($player != "") {
	
		//Vouch
		if (isset($_GET['action']) && $_GET['action'] == 'vouch' && ArghSession::is_rights(RightsMode::VIP_VOUCHER)) {
			if (mysql_num_rows(mysql_query("SELECT * FROM lg_vouchs WHERE voucher = '".ArghSession::get_username()."' AND qui = '".mysql_real_escape_string($player)."'")) == 0) {
				//Ok on peut voter
				$nbVchRS = mysql_query("SELECT vouchs FROM lg_users WHERE username = '".ArghSession::get_username()."'");
				$nbVch = mysql_fetch_row($nbVchRS);
				if ($nbVch[0] > 0) {
					mysql_query("INSERT INTO lg_vouchs (voucher, qui, date_vouch) VALUES ('".ArghSession::get_username()."', '".mysql_real_escape_string($player)."', '".time()."')");
					mysql_query("UPDATE lg_users SET vouchs = vouchs - 1 WHERE username = '".ArghSession::get_username()."'") or die(mysql_error());
					//On regarde s'il faut voucher
					if (mysql_num_rows(mysql_query("SELECT * FROM lg_vouchs WHERE qui = '".mysql_real_escape_string($player)."'")) >= 4) {
						if (mysql_num_rows(mysql_query("SELECT * FROM lg_laddervip_vouchlist WHERE username = '".mysql_real_escape_string($player)."'")) == 0) {
							if (canVouch(ArghSession::get_username())) {
								//Vouch !
								mysql_query("INSERT INTO lg_laddervip_vouchlist (username, rank) VALUES ('".mysql_real_escape_string($player)."', '1')") or die(mysql_error());
							}
						}
					}
				}
			}
		}

		$isAdminLadder = ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN, RightsMode::VIP_HEADADMIN, RightsMode::VIP_ADMIN));

		echo '<script type="text/javascript" src="/ligue/javascript/ui.tabs.js"></script>';
		echo '<script type="text/javascript">';
		if ($isAdminLadder) {
			echo 'function swapDisplay(banId) { $.get("ajax/switchbandisplay.php", { banId: banId }, function(data) {}); };';
		}
		echo '$(document).ready(function() { ';
		if (ArghSession::is_gold() || ArghSession::get_username() == $player) {
			echo '$("#tabs-ladder-stats #stats a").live("click", function() { $("#stats_months").load("ajax/get_ladder_statistics.php", { mode: $(this).attr("t"), player: $(this).attr("p"), pwith: $(this).attr("w") }, function() { $("#stats_days, #stats_games").empty(); }); });';
			echo '$("#tabs-ladder-stats #stats_months a").live("click", function() { $("#stats_days").load("ajax/get_ladder_statistics.php", { mode: $(this).attr("t"), player: $(this).attr("p"), pwith: $(this).attr("w"), year: $(this).attr("y"), month: $(this).attr("m") }, function() { $("#stats_games").empty(); }); });';
			echo '$("#tabs-ladder-stats #stats_days a").live("click", function() { $("#stats_games").load("ajax/get_ladder_statistics.php", { mode: $(this).attr("t"), player: $(this).attr("p"), pwith: $(this).attr("w"), year: $(this).attr("y"), month: $(this).attr("m"), day: $(this).attr("d") }); });';
			echo '$("#tabs-laddervip-stats #stats a").live("click", function() { $("#stats_months").load("ajax/get_laddervip_statistics.php", { mode: $(this).attr("t"), player: $(this).attr("p"), pwith: $(this).attr("w") }, function() { $("#stats_days, #stats_games").empty(); }); });';
			echo '$("#tabs-laddervip-stats #stats_pick a").live("click", function() { $("#stats_months").load("ajax/get_laddervip_statistics.php", { mode: $(this).attr("t"), player: $(this).attr("p"), pick: $(this).attr("o") }, function() { $("#stats_days, #stats_games").empty(); }); });';
			echo '$("#tabs-laddervip-stats #stats_months a").live("click", function() { $("#stats_days").load("ajax/get_laddervip_statistics.php", { mode: $(this).attr("t"), player: $(this).attr("p"), pwith: $(this).attr("w"), pick: $(this).attr("o"), year: $(this).attr("y"), month: $(this).attr("m") }, function() { $("#stats_games").empty(); }); });';
			echo '$("#tabs-laddervip-stats #stats_days a").live("click", function() { $("#stats_games").load("ajax/get_laddervip_statistics.php", { mode: $(this).attr("t"), player: $(this).attr("p"), pwith: $(this).attr("w"), pick: $(this).attr("o"), year: $(this).attr("y"), month: $(this).attr("m"), day: $(this).attr("d") }); });';
		}
		echo '$("#tabs").tabs({ cache: false, load: function(event, ui) { if (ui.index == 2) { $("#tabs-laddervip-stats").empty(); $("#tabs-ladder-stats").tabs({ cache: false }); } else if (ui.index == 3) { $("#tabs-ladder-stats").empty(); $("#tabs-laddervip-stats").tabs({ cache: false }); } } });';
		echo ' });</script>';

		ArghPanel::begin_tag(htmlentities($player));
	
		echo '<div id="tabs">';
		echo '<ul>';
		echo '<li><a href="ajax/profile_infos.php?player='.$player.'"><img src="img/info.png" alt="" height="20" align="absmiddle" />&nbsp;'.Lang::INFORMATION.'</a></li>';
		echo '<li><a href="ajax/profile_league.php?player='.$player.'"><img src="img/league.png" alt="" height="20" align="absmiddle" />&nbsp;'.Lang::LEAGUE.'</a></li>';
		echo '<li><a href="ajax/get_ladder.php?mode=chart&player='.$player.'"><img src="img/ladder.png" alt="" height="20" align="absmiddle" />&nbsp;'.Lang::LADDER.'</a></li>';
		echo '<li><a href="ajax/get_laddervip.php?player='.$player.'"><img src="img/laddervip.png" alt="" height="20" align="absmiddle" />&nbsp;'.Lang::LADDER_VIP.'</a></li>';
		if ($isAdminLadder || ArghSession::get_username() == $player) {
			echo '<li><a href="ajax/profile_bans.php?player='.$player.'"><img src="img/warn.png" alt="" height="20" align="absmiddle" />&nbsp;'.Lang::SANCTIONS.'</a></li>';
		}
		echo '</ul>';
		echo '</div>';
	
		ArghPanel::end_tag();
	
		if ($isAdminLadder) {
			// POST WARN ?
			if (isset($_POST['go_warn'])) {
				mysql_query("INSERT INTO lg_ladderbans_follow (username, motif, `force`, quand, admin, type)
				VALUES ('".$player."', '".mysql_real_escape_string($_POST['motif_warn'])."', '".mysql_real_escape_string($_POST['valeur_warn'])."', '".time()."', '".ArghSession::get_username()."', 'warning')") or die(mysql_error());
			}
			// POST BAN ?
			if (isset($_POST['go'])) {
				mysql_query("INSERT INTO lg_ladderbans (qui, par_qui, quand, duree, raison)
				VALUES ('".$player."', '".ArghSession::get_username()."', '".time()."', '".mysql_real_escape_string($_POST['duree'])."', '".mysql_real_escape_string($_POST['motif'])."')");
				
				mysql_query("INSERT INTO lg_ladderbans_follow (username, motif, `force`, quand, admin, type)
				VALUES ('".$player."', '".mysql_real_escape_string($_POST['motif'])."', '".mysql_real_escape_string($_POST['duree'])."', '".time()."', '".ArghSession::get_username()."', 'ban')") or die(mysql_error());
				
				//Kick
				//Récupération contenu
				$file = CacheManager::LADDER_PLAYERLIST;
				$content = file($file);
				
				//Ouverture fichier
				$handle = fopen($file, 'w+');
				
				//Réécriture
				foreach ($content as $val) {
					$removed = false;
					$line = explode(';', $val);
					if ($line[0] != $player) {
						fwrite($handle, $val);
					} else {
						$removed = true;
					}
					
				}
			}
			ArghPanel::begin_tag(Lang::WARNING.' '.$player);
?>
<form action="?f=player_profile&player=<?php echo $player ?>" method="POST">
<table class="simple">
<tr>
	<td style="width: 100px;"><b><?php echo Lang::VALUE; ?></b></td>
	<td><b><?php echo Lang::REASON; ?></b></td><td>&nbsp;</td>
</tr>
<tr><td valign="top">
	<select name="valeur_warn">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
	</select>
</td><td valign="top"><textarea name="motif_warn" rows="3" cols="50">
</textarea></td><td valign="top"><input type="submit" name="go_warn" value="<?php echo Lang::OK; ?>" /></td></tr>
<?php
if (isset($_POST['go_warn'])) {
?>
<tr><td colspan="3"><span style="font-weight: bold; color: Red;"><?php echo sprintf(Lang::WARNING_ADDED_TO, $player); ?></span></td></tr>
<?php
}
?>
</table>
</form>
<?php
			ArghPanel::end_tag();
			ArghPanel::begin_tag(Lang::BAN.' '.$player);
?>
<form action="?f=player_profile&player=<?php echo $player ?>" method="POST">
<table class="simple">
<tr>
	<td style="width: 100px;"><b><?php echo Lang::LENGTH; ?></b></td>
	<td><b><?php echo Lang::REASON; ?></b></td>
	<td>&nbsp;</td>
</tr>
<tr><td valign="top">
	<select name="duree">
		<option value="1">1 <?php echo Lang::DAY; ?></option>
		<?php
			for ($i = 2; $i <= 20; $i++) echo '<option value="'.$i.'">'.$i.' '.Lang::DAYS.'</option>';
			echo '<option value="30">30 '.Lang::DAYS.'</option>';
			echo '<option value="60">60 '.Lang::DAYS.'</option>';
			echo '<option value="90">90 '.Lang::DAYS.'</option>';
			echo '<option value="120">120 '.Lang::DAYS.'</option>';
			echo '<option value="180">180 '.Lang::DAYS.'</option>';
		?>
		<option value="0"><?php echo Lang::UNLIMITED; ?></option>
	</select>
</td><td valign="top"><textarea name="motif" rows="3" cols="50">
</textarea></td><td valign="top"><input type="submit" name="go" value="<?php echo Lang::OK; ?>" /></td></tr>
<?php
if (isset($_POST['go'])) {
?>
<tr><td colspan="3"><span style="font-weight: bold; color: Red;"><?php echo sprintf(Lang::BAN_ADDED_TO, $player); ?></span></td></tr>
<?php
}
?>
</table>
</form>
<?php
			ArghPanel::end_tag();
		}

	}

?>