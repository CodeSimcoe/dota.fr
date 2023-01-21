<?php

	ArghSession::exit_if_not_logged();

	include 'refresh.php';

	//Fonctions
	require 'ladder_functions.php';
	
	if (ArghSession::is_rights(RightsMode::WEBMASTER) and $_GET['action'] == 'flush') {
		//Vidage cache
		$handle = fopen(CacheManager::LADDER_PLAYERLIST, 'w');
		fwrite($handle, '');
	}
	
	//Credits ?
	if (!ArghSession::has_credits()) {
		ArghPanel::begin_tag(Lang::LADDER);
		echo '<center>'.Lang::GOLD_NO_MORE_CREDITS.'</center>';
		ArghPanel::end_tag();
	}
	
	$banned = isBanned(ArghSession::get_username());
	
	if (!$banned) {
?>
<script language="javascript">

	var waitTime = <?php echo (ArghSession::is_gold() ? '100' : '3000'); ?>

	function Refresh(doLock) {
		if (doLock == 1) lockIcons();
		setTimeout("DoRefresh(" + doLock + ")", waitTime);
	}


	function DoRefresh(doLock) {
		
		$('#ladder10').load('ajax/ladder_10.php');
	}
	
	function Leave() {
		lockIcons();
		$.get('ajax/ladder_left.php');
		Refresh(0);
	}
	
	function Join() {
		lockIcons();
	    $.get('ajax/ladder_joined.php');
	    Refresh(0);
	}
	
	function lockIcons() {
		$('#btn_refresh').html('<center><img src="ladder/btn_norefresh.jpg" alt="" /></center>');
		
		switch ($('#icon').html()) {
			case '1':
				$('#btn_2').html('<img src="ladder/btn_noleave.jpg" alt="" />');
				break;
			case '2':
				$('#btn_2').html('<img src="ladder/btn_nojoin.jpg" alt="" />');
				break;
			default:
				break;
		}

		$('#ajax_loading').html('<img src="img/ajax-loader.gif" alt="" />');
	}
	
	function releaseLock() {}
	
	<?php
	if (ArghSession::is_gold()) {
		echo 'setInterval(\'Refresh(1)\', 15000)';
	}
	?>

</script>

<?php

	}
	ArghPanel::begin_tag(Lang::LADDER);
	echo '<div id="ladder10">';
	
	//Banned
	if ($banned) {
		$req = "SELECT * FROM lg_ladderbans WHERE qui = '".ArghSession::get_username()."'";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);
		
		//Unban si ban termine
		if (isFinished($l->quand, $l->duree)) {
			BanManager::unban($l->id);
			
			//mysql_query("DELETE FROM lg_ladderbans WHERE id='".$l->id."'");
			//Admin Log
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_UNBAN_USER, $l->qui), AdminLog::TYPE_LADDER, 'LadderGuardian');
			$al->save_log();
			/*
			$admin_req="INSERT INTO lg_adminlog (qui, quand, quoi) VALUES ('LadderGuardian', '".time()."', 'Unban ".$l->qui."')";
			mysql_query($admin_req);
			*/
		} else {
			$remain = remainingTime($l->quand, $l->duree);
			
			echo '<center>'.sprintf(Lang::LADDER_BANNED_ACCOUNT, $l->par_qui, $l->raison).'<br />';
			if ($remain != Lang::UNLIMITED) {
				if ($remain == 0) {
					echo Lang::LADDER_UNBAN_LESS_1_HOUR;
				} else {
					echo sprintf(Lang::LADDER_DELAY_UNTIL_UNBAN, $remain);
				}
			}
			echo '</center>';
			
			$banned = true;
		}
	}
	
	if (!$banned) {
		include 'ajax/ladder_10.php';
	}
	
	echo '</div>';
	ArghPanel::end_tag();

	if (ArghSession::is_rights(RightsMode::WEBMASTER)) {
		/*
		echo '<script type="text/javascript" src="ligue/javascript/jquery.ladder.js"></script>';
		ArghPanel::begin_tag(Lang::LADDER);
		echo '<div id="ladder_listing">';
		include 'ajax/ladder_listing.php';
		echo '</div>';
		ArghPanel::end_tag();
		*/
	}
?>