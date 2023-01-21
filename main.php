<?php
	require "classes/Shoutcast.php";

	ArghPanel::begin_tag(Lang::MAIN_NEWS);

	//SteelSeries Banner
	include 'steelseries/flash_575_75.html';

	//News
	echo '<br /><table class="simple">';
	/*
	echo '<tr>
			<td colspan="2" style="padding-left: 160px;"><img src="img/news/enUS/kota.jpg" title="'.Lang::NEWS_CAT_7.'" /></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left: 160px;">';
	
	News::get_news_by_category(7);
	echo '</td></tr><tr><td colspan="2">&nbsp;</td></tr>';
	*/
	echo '<tr>
			<td><img src="img/news/enUS/coverage.jpg" title="'.Lang::NEWS_CAT_1.'" /></td>
			<td><img src="img/news/enUS/community.jpg" title="'.Lang::NEWS_CAT_2.'" /></td>
		</tr>
		<tr><td>';

	News::get_news_by_category(1);
	echo '</td><td>';
	News::get_news_by_category(2);

	echo '</td></tr><tr><td colspan="2">&nbsp;</td></tr><tr>
			<td><img src="img/news/enUS/inter.jpg" title="'.Lang::NEWS_CAT_3.'" /></td>
			<td><img src="img/news/enUS/downloads.jpg" title="'.Lang::NEWS_CAT_4.'" /></td>
		</tr>';
	echo '<tr><td>';
	News::get_news_by_category(3);
	echo '</td><td>';
	News::get_news_by_category(4);
	echo '</td></tr>';
	
	echo '<tr><td colspan="2">&nbsp;</td></tr><tr>';
	
	//RSS
	echo '<tr><td colspan="2">
			<img src="rss/rss_icon.png" alt="" />
		</td></tr><tr><td colspan="2">';
	News::get_rss_news();
	echo '</td>
	</tr>';
	
	echo '</table>';

	ArghPanel::end_tag();

	$req = "SELECT m.team1, m.team2, m.etat, m.divi, m.j, c1.tag, c2.tag
			FROM lg_matchs m, lg_clans c1, lg_clans c2
			WHERE m.reported != 0
			AND m.etat != 1
			AND m.team1 = c1.id
			AND m.team2 = c2.id
			ORDER BY m.reported DESC
			LIMIT 10";
	$t = mysql_query($req);
	if (mysql_num_rows($t)) {
		ArghPanel::begin_tag(Lang::MAIN_LATEST_MATCHES);
?>
<table class="listing">
	<colgroup>
		<col />
		<col />
		<col />
		<col />
		<col />
		<col />
	</colgroup>
	<thead>
		<tr>
			<th><?php echo Lang::DIVISION; ?></th>
			<th><?php echo Lang::PLAYDAY; ?></th>
			<th colspan="2"><?php echo Lang::TEAMS; ?></th>
			<th><?php echo Lang::SCORE; ?></th>
			<th><?php echo Lang::REPORT; ?></th>
		</tr>
		<tr>
			<td colspan="6" class="line"></td>
		</tr>
	</thead>
   <?php
	$i = 0;
	while ($l = mysql_fetch_row($t)) {
		switch ($l[2]) {
			case 2:
			case 4:
			case 7:
			case 8:
				$score = '2-0';
				break;
			
			case 3:
			case 5:
			case 9:
			case 10:
				$score = '0-2';
				break;
				
			default:
				$score = '1-1';
				break;
		}
		echo '<tr'.Alternator::get_alternation($i).'>
			<td><em><a href="?f=league_division&div='.$l[3].'">'.$l[3].'</a></em></td>
			<td>'.$l[4].'</td>
			<td><a href="?f=team_profile&amp;id='.$l[0].'">'.$l[5].'</a></td>
			<td><a href="?f=team_profile&amp;id='.$l[1].'">'.$l[6].'</a></td>
			<td>'.$score.'</td>
			<td><a href="?f=match&amp;team1='.$l[0].'&team2='.$l[1].'">'.Lang::REPORT.'</a></td>
			</tr>';
	}
?>
</table>
<?php

		ArghPanel::end_tag();
	}
?>
<script language="javascript">
	$(document).ready(function() {
		get_next_matches("");
	});
	
	function get_next_matches(div) {
		$.get('ajax/get_next_matches.php',
			{
				div: div,
				anticache: new Date().getTime()
			}, function(data) {
				$("#next_matches").html(data);
		});
	}
</script>

<div id="next_matches">
</div>
<?php
	$shoutcasts = Shoutcast::get_next_shoutcasts();
	
	if (count($shoutcasts) > 0) {
		ArghPanel::begin_tag(Lang::MAIN_NEXT_SHOUTCASTS);
		echo '<table class="listing">
			<tr><td colspan="2"><b>'.Lang::TEAMS.'</b></td><td><b>'.Lang::DATE.'</b></td><td><b>'.Lang::INFO.'</b></td></tr>
			<tr><td colspan="4" class="line"></td></tr>';
		$i = 0;
		foreach($shoutcasts as $shout) {
			$alt = Alternator::get_alternation($i);
			echo '<tr><td>'.$shout->_team1_tag.'</td>
					<td>'.$shout->_team2_tag.'</td>
					<td>'.$shout->_date_shoutcast.'</td>
					<td>'.$shout->_comment.'</td>
					</tr>';
		}
		
		echo '</table>';
		ArghPanel::end_tag();
	}
?>