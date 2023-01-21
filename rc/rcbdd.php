<?php

	if ($_SESSION['access'] < 101) exit();
	
	require('rcparser/rcparser.php');
	include('rcparser/rcfunctions.php');
	
	$path = "rc/rcdwld";
	$folder = opendir($path);
	while ($file = readdir($folder)) {
		if ($file != "." && $file != "..") {
			$path = "rc/rcdwld/".$file;
			$parser = new DotaParser($path);
			$replay = new DotaReplay();
			$replay->version = $parser->game['version'];
			if (isset($parser->game['time'])) {
				$replay->time = $parser->game['time'];
			} else {
				$replay->time = $parser->header['length'];
			}
			$replay->mode = $parser->game['mode'];
			if ($replay->mode !== '') {
				$replay->modes = $parser->game['modes'];
			}
			foreach ($parser->players as $key => $player) {
				$dplayer = new DotaPlayer($player['name']);
				$dplayer->id = $player['player_id'];
				$dplayer->color = $player['color'];
				$dplayer->hero = $player['hero'];
				if ($player['kills']) $dplayer->kills = $player['kills'];
				if ($player['deaths']) $dplayer->deaths = $player['deaths'];
				if ($player['creepskills']) $dplayer->creepskills = $player['creepskills'];
				if ($player['creepsdenies']) $dplayer->creepsdenies = $player['creepsdenies'];
				if ($player['assists']) $dplayer->assists = $player['assists'];
				if ($player['gold']) $dplayer->gold = $player['gold'];
				if ($player['neutrals']) $dplayer->neutrals = $player['neutrals'];
				if ($player['tkill']) $dplayer->tkill = $player['tkill'];
				if ($player['tdeny']) $dplayer->tdeny = $player['tdeny'];
				if ($player['rkill']) $dplayer->rkill = $player['rkill'];
				if ($player['rdeny']) $dplayer->rdeny = $player['rdeny'];
				if ($player['kstats']) $dplayer->kstats = $player['kstats'];
				if ($player['items']) $dplayer->items = $player['items'];
				if ($player['endtime']) $dplayer->endtime = $player['endtime'];
				if ($player['endway']) $dplayer->endway = $player['endway'];
				if ($player['team'] == 0) {
					$replay->sentinel->players[] = $dplayer;
				} else if ($player['team'] == 1) {
					$replay->scourge->players[] = $dplayer;
				} else {
					$replay->observers[] = $dplayer;
				}
			}
			$replay->sentinel->bans = $parser->bsen;
			$replay->sentinel->picks = $parser->psen;
			$replay->scourge->bans = $parser->bsco;
			$replay->scourge->picks = $parser->psco;
			$replay->chat = $parser->chat;
			$rdatas = serialize($replay);
			$rfile = fopen(str_replace('/rcdwld/', '/rcdefs/', $path).'.txt', 'w');
			fwrite($rfile, $rdatas);
			fclose($rfile);
		}
	}

?>