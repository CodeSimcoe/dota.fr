<?php
	$clan_id = (int)$_GET['id'];
	
	require_once('ladder_functions.php');
	
	//Récup infos clan
	$req = "SELECT
				c.*,
				CASE WHEN SUM(w.valeur) IS NULL
					THEN 0
					ELSE SUM(w.valeur)
				END AS Warns
			FROM lg_clans c
			LEFT JOIN lg_warns w ON c.id = w.team
			WHERE c.id = '".$clan_id."'
			GROUP BY c.id";
	
	$t = mysql_query($req);
	
	if (!mysql_num_rows($t)) {
		//Team Invalide
		exit;
	}
	$l = mysql_fetch_object($t);
	/*
	$name = $l->name;
	$tag = $l->tag;
	$created = $l->created;
	$website = $l->website;
	$divi = $l->divi;
	$warns = $l->Warns;
	*/
	$logo = $l->logo;
	
	//Leader
	$req2 = "SELECT username
			FROM lg_users
			WHERE crank = '1'
			AND clan = '".$clan_id."'
			LIMIT 1";
	$t2 = mysql_query($req2);
	$l2 = mysql_fetch_row($t2);
	$leader = $l2[0];
	
	//logo du clan
	if (list($width, $height) = getimagesize($logo)) {
		if ($width > 300) {
			$height = round($height * 300 / $width);
			$width = 300;
		}
		if ($height > 150) {
			$width = round($width * 150 / $height);
			$height = 150;
		}
	} else {
		$logo = "nologo.jpg";
	}
	
	ArghPanel::begin_tag(stripslashes($l->name).' ['.$l->tag.']');
	echo '<br /><center><img src="'.$logo.'" width="'.$width.'" height="'.$height.'" alt="'.$l->name.'" /></center><br />';
	ArghPanel::end_tag();
	
	ArghPanel::begin_tag(Lang::INFORMATION);
?>
<table class="simple">
	<tr>
		<td colspan="3"><?php echo Lang::TEAM_CREATED_ON; ?></td>
		<td colspan="3"><?php echo date(Lang::DATE_FORMAT_DAY, $l->created); ?></td>
	</tr>
	<tr>
		<td colspan="3"><?php echo Lang::TEAM_LEADER; ?></td>
		<td colspan="3"><?php echo '<a href="?f=player_profile&player='.$l->leader.'">'.$l->leader.'</a>'; ?></td>
	</tr>
	<tr>
		<td colspan="3"><?php echo Lang::WEBSITE; ?></td>
		<td colspan="3"><?php echo '<a href="'.$l->website.'">'.$l->website.'</a>'; ?></td>
	</tr>
	<tr>
		<td colspan="3"><?php echo Lang::DIVISION; ?></td>
		<td colspan="3"><?php
		if ($l->divi != 0) {
			echo '<a href="?f=league_division&div='.$l->divi.'">'.Lang::DIVISION.' '.$l->divi.'</a>';
		} else {
			echo Lang::NO_DIVISION;
		}
		?></td>
	</tr>
	<tr>
		<td colspan="3"><a href="?f=league_warns"><?php echo Lang::LEAGUE_WARNINGS; ?></a></td>
		<td colspan="3"><?php echo empty($l->warns) ? '0' : $l->warns; ?></td>
	</tr>
	<tr><td colspan="6">&nbsp;</td></tr>
	<tr>
		<td><b>#</b></td>
		<td><b><?php echo Lang::RANK; ?></b></td>
		<td><b><?php echo Lang::USERNAME; ?></b></td>
		<td><b><?php echo Lang::GARENA; ?></b></td>
		<td><b><?php echo Lang::LADDER_XP; ?></b></td>
		<td><b><?php echo Lang::TEAM_JOINED_ON; ?></b></td>
	</tr>
	<tr><td colspan="6" class="line"></td></tr>
<?php
	$req = "SELECT *
			FROM lg_users
			WHERE clan='".$clan_id."'
			ORDER BY crank ASC, jclan DESC";
	$t = mysql_query($req);
	$j = 0;
	while ($l = mysql_fetch_object($t)) {
		$alt = Alternator::get_alternation($j);
		echo '<tr'.$alt.'>
			<td>'.$j.'</td>
			<td><img src="'.$l->crank.'.gif" alt="'.Lang::CLAN_RANK.'" /></td>
			<td><a href="?f=player_profile&player='.$l->username.'">'.$l->username.'</a></td>
			<td>'.$l->ggc.'</td>
			<td><b>'.XPColorize($l->pts).'</b></td>
			<td>'.date(Lang::DATE_FORMAT_DAY, $l->jclan).'</td>
		</tr>';
	}
?>
	<tr>
	<td colspan="6">&nbsp;</td>
	</tr>
	<tr>
	<td colspan="6"><b><?php echo Lang::JOIN_TEAM; ?></b></td>
	</tr>
	<tr>
		<td colspan="6">
		<form method="POST" action="?f=team_join">
			<?php echo Lang::PASSWORD; ?>:
			<input type="hidden" name="teamid" value="<?php echo $clan_id; ?>" />
			<input type="text" size="25" name="joinpass" maxlength="50" />
			<input type="submit" value="<?php echo Lang::VALIDATE; ?>" />
		</form>
		</td>
	</tr>
</table>
<?php
	ArghPanel::end_tag();
?>
