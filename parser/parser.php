<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>
  <body>
<pre>
<?php

	require('dota_replay.php');
	
	$replay_file = "DTFR vs NUB.w3g";
	
	$parser = new DotaParser($replay_file);
	
	$replay = new DotaReplay();
	$replay->version = $parser->game['version'];
	$replay->mode = $parser->game['mode'];
	if ($replay->mode !== '') {
		$replay->modes = $parser->game['modes'];
	}
	foreach ($parser->players as $key => $player) {
		$dplayer = new DotaPlayer($player['name']);
		$dplayer->color = $player['color'];
		$dplayer->hero = $player['hero'];
		if ($player['kills']) $dplayer->kills = $player['kills'];
		if ($player['deaths']) $dplayer->deaths = $player['deaths'];
		if ($player['creepskills']) $dplayer->creepskills = $player['creepskills'];
		if ($player['creepsdenies']) $dplayer->creepsdenies = $player['creepsdenies'];
		if ($player['team'] == 0) {
			$replay->sentinel->players[] = $dplayer;
		} else if ($player['team'] == 1) {
			$replay->scourge->players[] = $dplayer;
		} else {
			$replay->observers[] = $dplayer;
		}
	}
	$replay->sentinel->bans = $parser->bsen;
	$replay->scourge->bans = $parser->bsco;
	$replay->chat = $parser->chat;
	
	echo serialize($replay)."\n\n";
	
	echo print_r($replay)."\n\n";

?>
</pre>
  </body>
</html>