<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require ABSOLUTE_PATH.'classes/ArghSession.php';
	require ABSOLUTE_PATH.'classes/LadderStates.php';
	ArghSession::begin();
	ArghSession::exit_if_not_logged();
	
	require ABSOLUTE_PATH.'mysql_connect.php';
	
	$game_id = (int) $_GET['id'];
	
	$req = "SELECT * FROM lg_laddervip_games WHERE id = '".(int)$game_id."'";
	$t = mysql_query($req);
	if (mysql_num_rows($t) > 0) {
		$l = mysql_fetch_object($t);
	
		$status = (string)$l->status;
		$redStatus = substr($status, 2, 7);
		
		//Picking
		if ($redStatus == 'picking') {
			$step = $status[strlen($status)-1];
			if ($status[0] == 'p') {
			
				/*
				* Schéma
				1	A1
				2	B1
				3	B2
				4	A2
				5	A3
				6	B3
				7	B4
				8	A4
				*/
				
				//Joueurs pickés
				$pickedPlayers = array();
				for ($i = 1; $i < $step; $i++) {
					$ppl = 'pp'.$i;
					if ($l->$ppl != '') $pickedPlayers[] = $l->$ppl;
				}
			
				//Joueurs
				if (ArghSession::get_username() == $l->cap1 and ($step == 1 or $step == 4 or $step == 5 or $step == 8)) {
					//Ajout du joueur
					switch ($step) {
						case 1:
							if (!in_array($_GET['player'], $pickedPlayers)) mysql_query("
								UPDATE lg_laddervip_games
								SET pp1 = '".$_GET['player']."',
								actiontime = '".time()."',
								status = 'p_picking2'
								WHERE id = '".$game_id."'
							");
							break;	
							
						case 4:
							if (!in_array($_GET['player'], $pickedPlayers)) mysql_query("
								UPDATE lg_laddervip_games
								SET pp4 = '".$_GET['player']."',
								actiontime = '".time()."',
								status = 'p_picking5'
								WHERE id = '".$game_id."'
							");
							break;	
							
						case 5:
							if (!in_array($_GET['player'], $pickedPlayers)) mysql_query("
								UPDATE lg_laddervip_games
								SET pp5 = '".$_GET['player']."',
								actiontime = '".time()."',
								status = 'p_picking6'
								WHERE id = '".$game_id."'
							");
							break;
					}
					
				}
				if (ArghSession::get_username() == $l->cap2 and ($step == 2 or $step == 3 or $step == 6 or $step == 7)) {
					//Ajout du joueur
					switch ($step) {
						case 2:
							if (!in_array($_GET['player'], $pickedPlayers)) mysql_query("
								UPDATE lg_laddervip_games
								SET pp2 = '".$_GET['player']."',
								actiontime = '".time()."',
								status = 'p_picking3'
								WHERE id = '".$game_id."'
							");
							break;	
							
						case 3:
							if (!in_array($_GET['player'], $pickedPlayers)) mysql_query("
								UPDATE lg_laddervip_games
								SET pp3 = '".$_GET['player']."',
								actiontime = '".time()."',
								status = 'p_picking4'
								WHERE id = '".$game_id."'
							");
							break;	
							
						case 6:
							if (!in_array($_GET['player'], $pickedPlayers)) mysql_query("
								UPDATE lg_laddervip_games
								SET pp6 = '".$_GET['player']."',
								actiontime = '".time()."',
								status = 'p_picking7'
								WHERE id = '".$game_id."'
							");
							break;
							
						case 7:
							//Pick 7 + Pick  8 automatique
							if (!in_array($_GET['player'], $pickedPlayers)) {
								
								//Ok, le pick 7 est valide
								//Dernier joueur
								$pickedPlayers[] = $_GET['player'];
								$lastPlayer = array_diff(array($l->p1, $l->p2, $l->p3, $l->p4, $l->p5, $l->p6, $l->p7, $l->p8), $pickedPlayers);
								foreach ($lastPlayer as $pl) $lastPlayer = $pl;
								
								mysql_query("
									UPDATE lg_laddervip_games
									SET pp7 = '".$_GET['player']."',
									pp8 = '".$lastPlayer."',
									actiontime = '".time()."',
									status = 'h_banning1'
									WHERE id = '".$game_id."'
								");
							}
							break;
					}
				}
			} else{
				//Héros
				
			}
		}
	}
?>