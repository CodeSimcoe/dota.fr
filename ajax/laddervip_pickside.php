<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	ArghSession::exit_if_not_logged();
	
	require ABSOLUTE_PATH.'mysql_connect.php';
	
	$game_id = (int)$_GET['id'];
	
	$req = "SELECT * FROM lg_laddervip_games WHERE id = '".$game_id."'";
			
	$t = mysql_query($req);
	if (mysql_num_rows($t) > 0) {
		$l = mysql_fetch_object($t);
	
		$status = (string)$l->status;
		$redStatus = substr($status, 2, 7);
		$step = $status[strlen($status) - 1];
		
		//Picking
		if ($redStatus == 'picking') {
			$step = $status[strlen($status)-1];
			if ($status[0] == 's') {
			
				if ($step == 1) {
					//Cap1
					if (ArghSession::get_username() != $l->cap1) {
						exit;
						
					} else {
					
						//Premier choix : les 4 choix sont disponibles
						$availabilities = array('fp', 'sp', 'se', 'sc');
						
						if (in_array($_GET['side'], $availabilities)) {
							//Choix valide
							$upd = "UPDATE lg_laddervip_games SET cap1_side = '".$_GET['side']."', status = 's_picking2', actiontime = '".time()."' WHERE id = '".$game_id."'";
							mysql_query($upd);
						}
					}
				} else if ($step == 2) {
					//Cap2
					if (ArghSession::get_username() != $l->cap2) {
						exit;
						
					} else {
					
						//Second choix : 2 choix sont disponibles
						if ($l->cap1_side == 'fp' || $l->cap1_side == 'sp') {
							$availabilities = array('se', 'sc');
						} else {
							$availabilities = array('fp', 'sp');
						}
						
						if (in_array($_GET['side'], $availabilities)) {
							//Choix valide
							
							if ($l->cap1_side == 'sp' || $_GET['side'] == 'fp') {
								//On swap car c'est le cap1 qui FP
								$upd = "UPDATE lg_laddervip_games SET cap1_side = '".$_GET['side']."', cap2_side = '".$l->cap1_side."', status = 'p_picking1', cap1 = '".$l->cap2."', cap2 = '".$l->cap1."', actiontime = '".time()."' WHERE id = '".$game_id."'";
							} else {
								$upd = "UPDATE lg_laddervip_games SET cap2_side = '".$_GET['side']."', status = 'p_picking1', actiontime = '".time()."' WHERE id = '".$game_id."'";
							}
							mysql_query($upd);
							
							
						}
					}
				}
			}
		}
	}
?>