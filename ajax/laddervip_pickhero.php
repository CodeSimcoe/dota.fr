<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require ABSOLUTE_PATH.'classes/ArghSession.php';
	require ABSOLUTE_PATH.'classes/LadderStates.php';
	ArghSession::begin();
	ArghSession::exit_if_not_logged();
	
	require ABSOLUTE_PATH.'mysql_connect.php';
	
	$game_id = (int) $_GET['id'];
	
	$req = "SELECT * FROM lg_laddervip_games WHERE id = '".$game_id."'";
	$t = mysql_query($req);
	if (mysql_num_rows($t) > 0) {
		$l = mysql_fetch_object($t);
	
		$status = (string)$l->status;
		$redStatus = substr($status, 2, 7);
		
		//Picking
		if ($redStatus == 'picking') {
			$step = $status[strlen($status) - 1];
			if ($step == 0) $step = 10;
			
			if ($status[0] == 'h') {
				//Héros
				$unavailable = array($l->ban1, $l->ban2, $l->ban3, $l->ban4, $l->ban5, $l->ban6, $l->ban7, $l->ban8);
				for ($i = 1; $i < $step; $i++) {
					$hero = 'h'.$i;
					$unavailable[] = $l->$hero;
				}
				
				if (in_array($_GET['hero'], $unavailable)) {
					//Hero non dispo
					exit;
				}
				
				//No timer refresh when 1st of 2 heroes is picked
				switch ($step) {
					case 2:
					case 4:
					case 6:
					case 8:
						$time = $l->actiontime;
						break;
						
					default:
						$time = time();
						break;
				}
				
				//cap1
				if (ArghSession::get_username() == $l->cap1) {
					if ($step == 1 || $step == 4 || $step == 5 || $step == 8 || $step = 9) {
						$next = ($step == 9) ? 0 : $step + 1;
						mysql_query("UPDATE lg_laddervip_games SET h".$step." = '".$_GET['hero']."', status = 'h_picking".$next."', actiontime = '".$time."' WHERE id = '".$game_id."'");
					}
				}
				if (ArghSession::get_username() == $l->cap2) {
					if ($step == 2 || $step == 3 || $step == 6 || $step == 7 || $step = 10) {
						$next = ($step == 10) ? LadderStates::PLAYING : 'h_picking'.($step + 1);
						mysql_query("UPDATE lg_laddervip_games SET h".$step." = '".$_GET['hero']."', status = '".$next."', actiontime = '".$time."' WHERE id = '".$game_id."'");
					}
				}
			}
		}
	}
?>