<?php
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::NEWS_HEADADMIN, 
			RightsMode::NEWS_NEWSER 
		)
	);
?>
<script language="javascript">
    function delete_news(id) {
        if (confirm('<?php echo Lang::NEWS_WISH_TO_DELETE; ?>')) {
            document.location.href = '?f=admin_news_list&action=delete&news_id=' + id;
        }
    }
</script>
<?php

	//Suppression de news par un admin
	if (ArghSession::is_rights(RightsMode::NEWS_HEADADMIN) && $_GET['action'] == 'delete') {
		$news_id = (int)$_GET['news_id'];
		mysql_query("UPDATE lg_newsmod SET deleted = '1' WHERE id = '".$news_id."'");
		$sentence = sprintf(Lang::ADMIN_LOG_NEWS_REMOVED, $news_id);
		$al = new AdminLog($sentence, AdminLog::TYPE_NEWS);
		$al->save_log();
	}

	$month = (isset($_POST['month'])) ? (int)$_POST['month'] : date("n");
	$year = (isset($_POST['year'])) ? (int)$_POST['year'] : date("Y");

	ArghPanel::begin_tag(Lang::NEWS_MODULE);
	echo '<form method="POST" action="?f=admin_news_list">'.Lang::NEWS_CHOOSE_PERIOD.':&nbsp;<select name="month">';

	//Mois
	for ($i = 1; $i <= 12; $i++) {
		echo '<option'.attr_($i, $month).' value="'.$i.'">'.Lang::$MONTHS_ARRAY[$i - 1].'</option>';
	}
	echo '</select> <select name="year">';
    
	//Année
	for ($i = 2007; $i <= date("Y"); $i++) {
		echo '<option'.attr_($i, $year).' value="'.$i.'">'.$i.'</option>';
	}
	echo '</select> <input type="submit" value="'.Lang::VALIDATE.'">
	</form>
	<br />
	<table class="listing">
		<colgroup>
			<col width="20%" />
			<col width="20%" />
			<col width="45%" />
			<col width="10%" />
			<col width="5%" />
		</colgroup>
		<thead>
			<tr>
				<th>'.Lang::DATE.'</th>
				<th>'.Lang::POSTED_BY.'</th>
				<th>'.Lang::NEWS_TITLE.'</th>
				<th colspan="2"><b>'.Lang::ACTION.'</th>
			</tr>
		</thead>';
    
	$temps1 = mktime(0, 0, 0, $month, 1, $year);
	if ($month == 12) {
		$temps2 = mktime(23, 59, 59, 1, 0, $year + 1);
	} else {
		$temps2 = mktime(23, 59, 59, $month + 1, 0, $year);
	}

	$req = "SELECT *
			FROM lg_newsmod
			WHERE daten > ".$temps1."
			AND daten < ".$temps2."
			AND deleted = 0
			ORDER BY id DESC";
	$t = mysql_query($req);
	$j = 0;
	while ($l = mysql_fetch_object($t)) {
		$alt = Alternator::get_alternation($j);
		$alt .= ($l->afficher == 0) ? ' style="color: grey;"' : '';
		$no_edit = ($l->author_lock == 1 && ArghSession::get_username() != $l->poster) ? true : false;
		echo '<tr>
			<td'.$alt.'>'.date(Lang::DATE_FORMAT_HOUR, $l->daten).'</td>
			<td'.$alt.'><b>'.$l->poster.'</b></td>
			<td'.$alt.'>'.($l->author_lock == 1 ? '<img src="img/icons/lock.png" alt="" />&nbsp;' : '').'<a href="?f=news&id='.$l->id.'">'.stripslashes($l->titre).'</a></td>
			<td'.$alt.'>'.($no_edit ? '<img src="img/icons/pencil_delete.png" alt="" />' : '<a href="?f=admin_news_add&id='.$l->id.'"><img src="img/icons/pencil.png" alt="" /></a>').'</td>
			<td'.$alt.'>'.(ArghSession::is_rights(RightsMode::NEWS_HEADADMIN) ? '<a href="#"><img src="img/icons/cross.png" onClick="javascript:delete_news('.$l->id.');" /></a>' : '').'</td>
			</tr>';
    }
    echo '</table>';
    
    ArghPanel::end_tag();
    ArghPanel::begin_tag(Lang::NEWS_ADDING);
    echo '<center><a href="?f=admin_news_add"><img src="/img/icons/add.png" alt="" />&nbsp;'.Lang::NEWS_ADD_NEW_ONE.'</a></center>';
    ArghPanel::end_tag();
?>
