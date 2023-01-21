<?php
	require_once 'classes/ArghSession.php';
	require_once 'classes/ClanRanks.php';
	ArghSession::begin();
	require_once 'lang/'.ArghSession::get_lang().'/Lang.php';
	
	require_once 'mysql_connect.php' ;
	include 'refresh.php';
?>
<html>

<head>
	<link rel="stylesheet" href="themes/default/boxes.css" type="text/css">
	<link rel="stylesheet" href="themes/default/default.css" type="text/css">
</head>

<body>
<br /><br /><br />
<center>
<?php
	//Vérif
	ArghSession::exit_if_not_logged();
	if (ArghSession::get_clan_rank() != 1) {
		//Uniquement les TC
		exit(Lang::AUTHORIZATION_REQUIRED);
	}
	
	//Ok
	$req = "SELECT divi, name, id
			FROM lg_clans
			WHERE id = '".ArghSession::get_clan()."' LIMIT 1";
	$t = mysql_query($req);
	if (mysql_num_rows($t) == 1) {
		$l = mysql_fetch_row($t);
		
		if ($l[0] != 0) {
			exit(sprintf(Lang::TEAM_CANT_DISBAND, $l[0]));
		}
		
		if ($_GET['conf'] == 'ok') {
			//Après confirmation
			//On vire les membres
			$upd = "UPDATE lg_users
					SET crank = '".ClanRanks::PEON."',
						clan = '0'
					WHERE clan = '".$l[2]."'";
			mysql_query($upd);
			
			//On vire la team
			$del = "DELETE FROM lg_clans WHERE id = '".$l[2]."'";
			mysql_query($del);
			echo sprintf(Lang::TEAM_DELETED, $l[1]);
			include('refresh.php');
		} else {
			//Avant confirmation
			echo '<b>'.$l[1].'</b> '.Lang::TEAM_ABOUT_TO_BE_DELETED.'<br /><br /><a href="team_delete.php?conf=ok" >'.Lang::CONFIRM.'</a>';
		}
	}
?>
</center>
</body>
</html>