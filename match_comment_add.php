<?php
	if (!ArghSession::is_logged()) exit;

	//POST
	$id = (int)$_POST['id'];
	$team1 = (int)$_POST['team1'];
	$team2 = (int)$_POST['team2'];
	$texte = $_POST['FCKeditor2'];
	
	ArghPanel::begin_tag(Lang::MESSAGE_ADDING);
	
	echo '<center>';
	
	if (ArghSession::get_clan() == $team1 
	    or ArghSession::get_clan() == $team2 
		or ArghSession::is_rights(
			array(
				RightsMode::LEAGUE_HEADADMIN, 
				RightsMode::LEAGUE_ADMIN, 
				RightsMode::NEWS_HEADADMIN, 
				RightsMode::NEWS_NEWSER, 
				RightsMode::SHOUTCAST_HEADADMIN, 
				RightsMode::SHOUTCAST_SHOUTCASTER
			)
		)) {
		$req = "SELECT count(*) FROM lg_text WHERE match_id = '".$id."' LIMIT 1";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		$nb = $l[0] + 1;

		$texte = eregi_replace("<script[^>]*>(.|\n)*script>(\r\n)?", "", $texte);
		$req = "INSERT INTO lg_text (match_id, post_id, text, post_date, poster) VALUES ('".$id."', '".$nb."', '".$texte."', '".time()."', '".$_SESSION['username']."')";
		mysql_query($req);
		echo Lang::MESSAGE_ADDED.'<br /><a href="?f=match&team1='.$team1.'&team2='.$team2.'">'.Lang::GO_ON.'</a>';
	} else {
		echo Lang::MESSAGE_CANT_POST;
	}
	
	echo '</center>';
	ArghPanel::end_tag();
?>