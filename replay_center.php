<link rel="stylesheet" href="themes/default/parser.css" type="text/css">
<?php

	require_once '/home/www/dota/classes/ReplayParser.php';

	$replay_path = '/home/www/dota/replaycenter/';

	$id = 0;
	if (isset($_GET['id'])) {
		$id = (int)$_GET['id'];
	}

	if ($id != 0) {
		$req = "
			SELECT
				competition,
				fichier,
				team1,
				team2
			FROM lg_replaycenter
			WHERE id = ".$id;
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			$obj = mysql_fetch_object($res);
			ArghPanel::begin_tag($obj->competition.' | '.$obj->team1.' vs '.$obj->team2);
			if (!is_file($replay_path.$obj->fichier.'.txt')) {
				$parser = new ReplayParser($replay_path.$obj->fichier);
				$parser->txt_serialize();
			}
			$replay = DotaReplay::load_from_txt($replay_path.$obj->fichier.'.txt');
			echo ReplayFunctions::html_header($replay);
			echo '<div style="width:100%;text-align:center;font-weight:bold;padding:0;margin:0;"><a href="http://www.dota.fr/replaycenter/'.$obj->fichier.'">DOWNLOAD</a></div>';
			echo '<hr /><br />';
			if (array_key_exists('cm', $replay->modes)) {
				echo ReplayFunctions::html_picks($replay).'<br /><hr /><br />';
			}
			echo ReplayFunctions::html_teams($replay, false).'<br /><hr /><br />';
			echo ReplayFunctions::html_stats($replay).'<br /><hr /><br />';
			echo ReplayFunctions::html_chat($replay, false);
			ArghPanel::end_tag();
		}
	}

?>