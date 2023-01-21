<?php
require '../mysql_connect.php';
require '../classes/ArghSession.php';
ArghSession::begin();
require '../lang/'.ArghSession::get_lang().'/Lang.php';
require '../classes/ArghPanel.php';
require '../classes/Alternator.php';
require '../classes/CacheManager.php';

$delai = time() - 7200;

if (!empty($_GET['div'])) {
	$div = substr($_GET['div'], 0, 2);
	$req = "SELECT m.team1, m.team2, m.divi, m.date_proposee, c1.name, c1.tag, c2.name, c2.tag
			FROM lg_matchs m, lg_clans c1, lg_clans c2
			WHERE qui_accepte != ''
			AND etat = 1
			AND date_proposee > ".$delai."
			AND m.team1 = c1.id
			AND m.team2 = c2.id
			AND m.divi = '".mysql_real_escape_string($div)."'
			ORDER BY date_proposee ASC
			LIMIT 8";
} else {
	$req = "SELECT m.team1, m.team2, m.divi, m.date_proposee, c1.name, c1.tag, c2.name, c2.tag
			FROM lg_matchs m, lg_clans c1, lg_clans c2
			WHERE qui_accepte != ''
			AND etat = 1
			AND date_proposee > ".$delai."
			AND m.team1 = c1.id
			AND m.team2 = c2.id
			ORDER BY date_proposee ASC
			LIMIT 8";
}

	$t = mysql_query($req);
	
	ArghPanel::begin_tag(Lang::NEXT_MATCHES);
?>
<table class="listing">
<colgroup>
	<col width="50px" />
	<col width="150px" />
	<col width="150px" />
	<col width="100px" />
</colgroup>
<thead>
	<tr>
		<th><?php echo Lang::DIVISION; ?></th>
		<th colspan="2"><?php echo Lang::TEAMS; ?></th>
		<th><?php echo Lang::DATE; ?></th>
	</tr>
<tr><td colspan="4" class="line"></td></tr>
</thead>
<?php
	$i = 0;
	if (mysql_num_rows($t)) {
		while ($l = mysql_fetch_row($t)) {
			echo '<tr'.Alternator::get_alternation($i).'>
					<td><em><a href="?f=league_division&div='.$l[2].'">'.$l[2].'</a></em></td>
					<td><a href="?f=team_profile&id='.$l[0].'">'.htmlentities(stripslashes($l[4])).' ['.htmlentities($l[5]).']</a></td>
					<td><a href="?f=team_profile&id='.$l[1].'">'.htmlentities(stripslashes($l[6])).' ['.htmlentities($l[7]).']</a></td>
					<td>'.date(Lang::DATE_FORMAT_HOUR, $l[3]).'</td>
				</tr>';
		}
	} else {
		echo '<tr><td colspan="4">'.Lang::NO_MATCH.'</td></tr>';
	}
?>
</table>
<br />
<?php echo Lang::FILTER_BY_DIVISION; ?>:&nbsp;&nbsp;
<a href="javascript: get_next_matches('');"><?php echo Lang::ALL_DIVISIONS; ?></a>&nbsp;
<?php
	$content = CacheManager::get_division_cache();
	foreach ($content as $val) {
		echo '<a href="javascript: get_next_matches(\''.$val.'\');">'.$val.'</a>&nbsp;';
	}
	
	ArghPanel::end_tag();
?>