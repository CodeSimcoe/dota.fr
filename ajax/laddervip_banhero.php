<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require ABSOLUTE_PATH.'classes/ArghSession.php';
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
		if ($redStatus == 'banning') {
			$step = $status[strlen($status)-1];
			if ($status[0] == 'h') {
			
				$banned_heroes = array();
				for ($j = 1; $j <= $step; $j++) {
					$hero = 'ban'.$j;
					$banned_heroes[] = $l->$hero;
				}
				
				//Hero already banned
				if (in_array($_GET['hero'], $banned_heroes)) exit;
				
				//Caps
				if (ArghSession::get_username() == $l->cap1) {
					switch ($step) {
						case 1:
							mysql_query("UPDATE lg_laddervip_games SET ban1 = '".$_GET['hero']."', status = 'h_banning2', actiontime = '".time()."' WHERE id = '".$game_id."'");
							break;
							
						case 3:
							mysql_query("UPDATE lg_laddervip_games SET ban3 = '".$_GET['hero']."', status = 'h_banning4', actiontime = '".time()."' WHERE id = '".$game_id."'");
							break;
							
						case 5:
							mysql_query("UPDATE lg_laddervip_games SET ban5 = '".$_GET['hero']."', status = 'h_banning6', actiontime = '".time()."' WHERE id = '".$game_id."'");
							break;
							
						case 7:
							mysql_query("UPDATE lg_laddervip_games SET ban7 = '".$_GET['hero']."', status = 'h_banning8', actiontime = '".time()."' WHERE id = '".$game_id."'");
							break;
							
						default:
							exit();
					}
					
				}
				if (ArghSession::get_username() == $l->cap2) {
					switch ($step) {
						case 2:
							mysql_query("UPDATE lg_laddervip_games SET ban2 = '".$_GET['hero']."', status = 'h_banning3', actiontime = '".time()."' WHERE id = '".$game_id."'");
							break;
							
						case 4:
							mysql_query("UPDATE lg_laddervip_games SET ban4 = '".$_GET['hero']."', status = 'h_banning5', actiontime = '".time()."' WHERE id = '".$game_id."'");
							break;
							
						case 6:
							mysql_query("UPDATE lg_laddervip_games SET ban6 = '".$_GET['hero']."', status = 'h_banning7', actiontime = '".time()."' WHERE id = '".$game_id."'");
							break;
							
						case 8:
							mysql_query("UPDATE lg_laddervip_games SET ban8 = '".$_GET['hero']."', status = 'h_picking1', actiontime = '".time()."' WHERE id = '".$game_id."'");
							break;
							
						default:
							exit;
					}
				}
			}
		}
	}
?>