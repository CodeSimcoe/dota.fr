
<style type="text/css">
table.chat {
	width: 100%;
}
table.chat td {
	font-size: 8pt; 
	font-family: Verdana;
	padding: 1px 0px;
}
table td div.ochat {
	overflow: hidden;
	overflow-x: hidden;
	overflow-y: auto;
	overflow : -moz-scrollbars-vertical;
	height: 140px; 
}
table.pstats {
	width: 600px; 
	table-layout: fixed; 
	border-collapse: collapse; 
	border: solid 2px #E0B73F;
	overlfow: hidden;
}
table.pstats td {
	font-family: Verdana;
	font-size: 8pt;
	overlfow: hidden;
	cursor: default;
}
table.pstats td.center {
	text-align: center;
}
table.pstats td.legend {
	color: #97CAFC;
	text-indent: 5px;
}
table.pstats td.padding {
	height: 5px;
}
</style>
<?php

	if ($_SESSION['access'] < 101) exit();

	require('rcparser/rcparser.php');
	include('rcparser/rcfunctions.php');

	$preplay = "";
	if (isset($_POST["rcreplay"])) {
		$preplay = $_POST["rcreplay"];
		$path = "rc/rctmp/".$preplay;
	}

	$replay = null;

	if ($preplay != "") {
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
		$rfile = fopen($path.'.txt', 'w');
		fwrite($rfile, $rdatas);
		fclose($rfile);
	}

?>
<table class="simple">
	<tr>
		<td class="top_left"></td>
		<td class="top">Replay Center - Analyse <?php echo $preplay; ?></td>
		<td class="top_right"></td>
	</tr>
	<tr>
		<td class="left"></td>
		<td style="padding: 5px;">
			<br />
			<form action="/ligue/?f=rc/rcmove" method="post" id="rcanalyze" name="rcanalyze">
			<input type="hidden" name="rcreplay" id="rcreplay" value="<?php echo $preplay; ?>" />
			<?php
				if ($replay != null) {
			?>
			<br />
			<table style="width: 100%; table-layout: fixed;" cellpadding="0" cellspacing="0">
				<colgroup>
					<col width="80" />
					<col />
				</colgroup>
			<?php
				echo generateHeader($replay, $preplay);
				echo generateTeams($replay);
				echo generateChat($replay);
				echo '<tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2" class="line"></td></tr><tr><td colspan="2">&nbsp;</td></tr>';
				if (isset($replay->modes['cm'])) {
					echo generatePicks($replay);
					echo '<tr><td colspan="2">&nbsp;</td></tr>';
				}
				echo '<tr><td colspan="2" align="center">';
				echo generateStats($replay->sentinel->players, $replay->scourge->players);
				echo '<br />';
				echo generateStats($replay->scourge->players, $replay->sentinel->players);
				echo '</td></tr>';
			?>
				<tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2" class="line"></td></tr><tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td colspan="2"><input type="submit" id="move" name="move" value="Valider" style="width: 80%; margin: 0px 10%;" /></td>
				</tr>
			</table>
			<?php
				} else {
			?>
			Problème de lecture du replay
			<?php
				}
			?>
			</form>
		</td>
		<td class="right"></td>
	</tr>
	<tr>
		<td class="bottom_left"></td>
		<td class="bottom"></td>
		<td class="bottom_right"></td>
	</tr>
</table>
