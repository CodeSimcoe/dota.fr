<?php
	//Mode de tri
	$modes = array(
		'username' => Lang::USERNAME,
		'ggc' => Lang::GARENA_ACCOUNT,
		'bnet' => Lang::BNET_ACCOUNT,
		'clan' => Lang::TEAM,
		'joined' => Lang::REGISTERATION_DATE
	);
	

	if (empty($_POST['mode'])) {
		$mode = 'username';
	} else {
		if (array_key_exists($_POST['mode'], $modes)) {
			//Valeur POST ok
			$mode = $_POST['mode'];
		} else {
			$mode = 'username';
		}
	}
	
	//Order
	$orders = array(
		'ASC' => Lang::ASCENDING,
		'DESC' => Lang::DESCENDING
	);
	
	if (empty($_POST['order'])) {
		$order = 'ASC';
	} else {
		if (array_key_exists($_POST['order'], $orders)) {
			//Valeur POST ok
			$order = $_POST['mode'];
		} else {
			$mode = 'ASC';
		}
	}
	
	//Criteres de recherche
	$researches = array(
		'username' => Lang::USERNAME,
		'ggc' => Lang::GARENA_ACCOUNT,
		'bnet' => Lang::BNET_ACCOUNT,
		'clan' => Lang::TEAM
	);

	if (empty($_POST['research'])) {
		$research = 'username';
	} else {
		if (array_key_exists($_POST['research'], $researches)) {
			//Valeur POST ok
			$research = $_POST['research'];
		} else {
			$research = 'username';
		}
	}

	$search = mysql_real_escape_string($_POST['search']);
	$start = (int)$_GET['start'];
	
	$start = empty($start) ? 0 : $start - 1;
	
	//Nombre de résultats a afficher par page
	$nb_aff = 100;
	
	//Requêtes
	if (isset($_POST['rech'])) {
		$req = "SELECT *
				FROM lg_users u LEFT JOIN lg_clans c ON u.clan = c.id
				WHERE `".$research."` LIKE '%".$search."%'
				ORDER BY `".$research."` ASC
				LIMIT ".$start.", ".$nb_aff."
				";
		$nb_players = mysql_num_rows(mysql_query("SELECT * FROM lg_users WHERE `".$research."` LIKE '%".$search."%'"));
		
	} else {
		$req = "SELECT *
				FROM lg_users u LEFT JOIN lg_clans c ON u.clan = c.id
				ORDER BY u.`".$mode."` ".$order."
				LIMIT ".$start.", ".$nb_aff."";
		$nb_players = mysql_num_rows(mysql_query("SELECT * FROM lg_users"));
	}
	
	
	
	//Divisions en plusieurs pages
	$nb_pages = floor($nb_players / $nb_aff);
	if (floor($nb_players / $nb_aff) != $nb_players / $nb_aff) {
		$nb_pages++;
	}
	
	ArghPanel::begin_tag(Lang::RESEARCH_CRITERIAS);
?>
<form method="POST" action="?f=players_list">
<table class="simple">
	<tr><td><center>
	<?php echo Lang::SORT_BY; ?>: 
	<select name="mode">
		<?php
			foreach ($modes as $field => $descr) {
				echo '<option value="'.$field.'"'.attr_($mode, $field).'>'.$descr.'</option>';
			}
		?>
	</select>

	<select name="order">
		<?php
			foreach ($orders as $field => $descr) {
				echo '<option value="'.$field.'"'.attr_($order, $field).'>'.$descr.'</option>';
			}
		?>
	</select>
	<input type="submit" value="Valider">
	</center></td></tr>
	</form>
	<form method="POST" action="?f=players_list&mode=recherche">
	<tr><td><center>
	<?php echo Lang::FIND; ?>:
	<select name="research">
		<?php
			foreach ($researches as $field => $descr) {
				echo '<option value="'.$field.'"'.attr_($research, $field).'>'.$descr.'</option>';
			}
		?>
	</select>
	<?php echo Lang::CONTAINING; ?> <input type="text" size="20" name="search" maxlength="20" />
	<input name="rech" type="submit" value="<?php echo Lang::CONTAINING; ?>" /></center>
	</td></tr>
</table>
</form>
<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::PLAYERS);
?>
<table class="listing">
	<colgroup>
		<col width="10%" />
		<col width="24%" />
		<col width="24%" />
		<col width="22%" />
		<col width="20%" />
	</colgroup>
	<tr>
		<td><b>#</b></td>
		<td><b><?php echo Lang::USERNAME; ?></b></td>
		<td><b><?php echo Lang::GARENA_ACCOUNT; ?></b></td>
		<td><b><?php echo Lang::TEAM; ?></b></td>
		<td><b><?php echo Lang::REGISTERATION_DATE; ?></b></td>
	</tr>
	<tr><td colspan="5" class="line">&nbsp;</td></tr>
	<?php
		$t = mysql_query($req) or die(mysql_error());
		$i = $start;
		while ($l = mysql_fetch_object($t)) {
			echo '<tr'.Alternator::get_alternation($i).'>
					<td><i>'.$i.'.</i></td>
					<td><a href="?f=player_profile&amp;player='.$l->username.'">'.$l->username.'</a></td>
					<td>'.$l->ggc.'</td>
					<td><a href="?f=team_profile&amp;id='.$l->clan.'">'.$l->name.'</a></td>
					<td>'.date(Lang::DATE_FORMAT_DAY, $l->joined).'</td>
				</tr>';
		}
	?>
	<tr><td colspan="5" class="line"></td></tr>
	<tr><td colspan="5">
	<?php
		echo Lang::PAGE.': <select name="page" onChange="javascript: jump(this.value);">';
		for ($k = 1; $k <= $nb_pages; $k++) {
			$a = ($k - 1) * $nb_aff + 1;
			$b = $k * $nb_aff;
			if ($k == $nb_pages) {
				$b = $nb_teams;
			}
			echo '<option value="'.$a.'">'.$k.' ('.$a.' - '.($a + 99).')</option>';
			/*
			if ($start + 1 != $a) {
				if ($k % 24 == 0) echo '<br />';
				echo '<a href="?f=players_list&amp;mode='.$mode.'&amp;order='.$order.'&amp;start='.$a.'">'.$k.'</a>&nbsp;';
			} else {
				echo '<b>'.$k.'</b>&nbsp;';
			}
			*/
		}
		echo '</select>';
	?>
	</td></tr>
</table>
<?php
	ArghPanel::end_tag();
?>
<script language="javascript">
function jump(page) {
	window.location = '?f=players_list&mode=<?php echo $mode; ?>&order=<?php echo $order; ?>&start=' + page;
}
</script>