<?php
	if (isset($_POST['mode'])) {
		$mode = mysql_real_escape_string($_POST['mode']);
	} elseif (isset($_GET['mode'])) {
		$mode = mysql_real_escape_string($_GET['mode']);
	} else {
		$mode = 'name';
	}

	if (isset($_POST['order'])) {
		if ($_POST['order'] == 'DESC') {
			$order = 'DESC';
		}
	} else {
		$order = 'ASC';
	}
	
	$research = mysql_real_escape_string(substr($_POST['research'], 0, 14));
	$search = mysql_real_escape_string($_POST['search']);
	$start = (int)$_GET['start'];
	
	$start = empty($start) ? 0 : $start - 1;
	
	//Nombre de résultats à afficher par page
	$nb_aff = 50;
	
	//Requêtes
	if (isset($_POST['rech'])) {
		$req = "SELECT * 
				FROM lg_clans 
				WHERE ".$research." LIKE '%".$search."%' 
				AND id > 0 
				ORDER BY ".$research." ASC 
				LIMIT ".$start.", ".$nb_aff;
		$t = mysql_query($req);
		$nb_players = mysql_num_rows(mysql_query("SELECT * FROM lg_clans WHERE ".$research." LIKE '%".$search."%' AND id != 0"));
	} else {
		$req = "SELECT * 
				FROM lg_clans 
				WHERE id > 0 
				ORDER BY ".$mode." ".$order." 
				LIMIT ".$start.", ".$nb_aff;
		$nb_players = mysql_num_rows(mysql_query("SELECT * FROM lg_clans WHERE id != 0"));
	}
	
	//Divisions en plusieurs pages
	$nb_pages = floor($nb_players / $nb_aff);
	if (floor($nb_players / $nb_aff) != $nb_players / $nb_aff) {
		$nb_pages++;
	}
	
	ArghPanel::begin_tag(Lang::TEAMS);
?>
<form method="POST" action="?f=teams_list" id="searchForm">
<table class="listing">
	<colgroup>
		<col width="10%" />
		<col width="36%" />
		<col width="12%" />
		<col width="24%" />
		<col width="18%" />
	</colgroup>
	<tr><td colspan="5">
	<?php echo Lang::SORT_BY; ?>: 
	<select name="mode">
		<?php
			echo '<option value="tag"'.attr_($mode, 'tag').'>'.Lang::TAG.'</option>';
			echo '<option value="name"'.attr_($mode, 'name').'>'.Lang::TEAM.'</option>';
			echo '<option value="created"'.attr_($mode, 'created').'>'.Lang::CREATION_DATE.'</option>';
		?>
	</select>

	<select name="order">
		<?php
			echo '<option value="ASC"'.attr_($order, 'ASC').'>'.Lang::ASCENDING.'</option>';
			echo '<option value="DESC"'.attr_($order, 'DESC').'>'.Lang::DESCENDING.'</option>';
		?>
	</select>
	<input type="submit" value="<?php echo Lang::VALIDATE; ?>" onClick="document.getElementById('searchForm').action=?f=teams_list&mode=recherche">
	<br />
	<?php echo Lang::FIND; ?>: 
	<select name="research">
		<?php
			echo '<option value="tag"'.attr_($research, 'tag').'>'.Lang::TAG.'</option>';
			echo '<option value="name"'.attr_($research, 'name').'>'.Lang::NAME.'</option>';
		?>
	</select>
	<?php echo Lang::CONTAINING; ?> <input type="text" size="20" name="search" maxlength="20" value="<?php echo htmlentities($search); ?>" />
	<input name="rech" type="submit" value="<?php echo Lang::FIND; ?>" />
	</td></tr>

	<tr>
		<td><b>#</b></td>
		<td><b><?php echo Lang::TEAM; ?></b></td>
		<td><b><?php echo Lang::TAG; ?></b></td>
		<td><b><?php echo Lang::LEADER; ?></b></td>
		<td><b><?php echo Lang::CREATION_DATE; ?></b></td>
	</tr>
	<tr><td class="line" colspan="5">&nbsp;</td></tr>
	<?
		$t = mysql_query($req);
		$i = $start;
		while ($l = mysql_fetch_object($t)) {
			$req2 = "SELECT username FROM lg_users WHERE clan = '".$l->id."' AND crank = '".ClanRanks::TAUREN."' LIMIT 1";
			$t2 = mysql_query($req2);
			if (mysql_num_rows($t2) > 0) {
				$l2 = mysql_fetch_row($t2);
				$usern = $l2[0];
			} else {
				$usern = '-';
			}
			$alt = Alternator::get_alternation($i);
			echo '<tr'.$alt.'>
				<td>'.$i.'</td>
				<td><a href="?f=team_profile&id='.$l->id.'">'.$l->name.'</a></td>
				<td>'.$l->tag.'</td>
				<td><a href="?f=player_profile&player='.$usern.'">'.$usern.'</a></td>
				<td>'.date(Lang::DATE_FORMAT_DAY, $l->created).'</td>
				</tr>';
		}
	?>
	<tr><td colspan="5">
	<?php
		echo Lang::PAGES.': ';
		for ($k = 1; $k <= $nb_pages; $k++) {
			$a = ($k - 1) * $nb_aff + 1;
			$b = $k * $nb_aff;
			if ($k == $nb_pages) {
				$b = $nb_teams;
			}
			if ($start+1 != $a) {
				echo '<a href="?f=teams_list&mode='.$mode.'&order='.$order.'&start='.$a.'">'.$k.'</a> ';
			} else {
				echo '<b>'.$k.'</b> ';
			}
		}
	?>
	</td></tr>
</table>
</form>
<?php
	ArghPanel::end_tag();
?>