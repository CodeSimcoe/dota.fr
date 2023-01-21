<?php
	ArghPanel::begin_tag(Lang::NEWS_ARCHIVES);
	
	/*
	function getStart($posts) {
		if ($posts == 0) {
			return 1;
		}
		if ($posts%10 == 0) {
			return $posts - 9;
		} else {
			return $posts - $posts%10 + 1;
		}
	}
	*/
	
	if (isset($_POST['month'])) {
		$month = (int)$_POST['month'];
	} else {
		$month = date("n");
	}
	if (isset($_POST['year'])) {
		$year = (int)$_POST['year'];
	} else {
		$year = date("Y");
	}
	if (isset($_POST['month2'])) {
		$month2 = (int)$_POST['month2'];
	} else {
		$month2 = date("n");
	}
	if (isset($_POST['year2'])) {
		$year2 = (int)$_POST['year2'];
	} else {
		$year2 = date("Y");
	}
	
	//Listing newsers
	$req = "SELECT DISTINCT poster FROM lg_newsmod ORDER BY poster ASC";
	$t = mysql_query($req);
	$newsers = array();
	while ($l = mysql_fetch_row($t)) {
		$newsers[] = $l[0];
	}
	
	$temps1 = mktime(0, 0, 0, $month, 1, $year);
	if ($month2 == 12) {
		$temps2 = mktime(23, 59, 59, 1, 0, $year2 + 1);
	} else {
		$temps2 = mktime(23, 59, 59, $month2 + 1, 0, $year2);
	}
	
	echo '<form method="POST" action="?f=news_list">'.Lang::NEWS_PERIOD.': <select name="month">';
	//Mois
	foreach (Lang::$MONTHS_ARRAY as $month_int => $month_str) {
		echo '<option'.attr_($month_int + 1, $month).' value='.($month_int + 1).'>'.$month_str.'</option>';
	}
	echo '</select> <select name="year">';
	//Année
	for ($i = 2007; $i <= date("Y"); $i++) {
		echo '<option'.attr_($i, $year).' value="'.$i.'">'.$i.'</option>';
	}
	echo '</select> '.Lang::UNTIL.' <select name="month2">';
	//Mois
	foreach (Lang::$MONTHS_ARRAY as $month_int => $month_str) {
		echo '<option'.attr_($month_int + 1, $month2).' value='.($month_int + 1).'>'.$month_str.'</option>';
	}
	/*
	//Mois
	for ($i=1;$i<=12;$i++) {
		echo '<option'.attr_($fra[$i-1], $fra[$month2-1]).' value='.$i.'>'.$fra[$i-1].'</option>';
	}
	*/
	echo '</select> <select name="year2">';
	//Année
	for ($i = 2007; $i <= date("Y"); $i++) {
		echo '<option'.attr_($i, $year2).' value="'.$i.'">'.$i.'</option>';
	}
	echo '</select><br />
	'.Lang::NEWSER.': <select name="newser">
	<option value="*"'.attr_($_POST['newser'], '*').'>'.Lang::ALL_LENGTHS.'</option>';
	//Listing de tous les newsers
	foreach ($newsers as $val) echo '<option'.attr_($_POST['newser'], $val).' value="'.$val.'">'.$val.'</option>';
	echo '</select><br />';
	
	//Categories
	/*
	$result = mysql_query("SHOW COLUMNS FROM lg_newsmod WHERE Field LIKE 'categorie'"); 
	if (mysql_num_rows($result) > 0) {
	   $row = mysql_fetch_array($result); 
	   preg_match_all("/'(.*?)'/", $row['Type'], $matches); 
	   $arrayEnum = $matches[1]; 
	}
	*/
	
	$categs = array(
		1 => Lang::NEWS_CAT_1,
		2 => Lang::NEWS_CAT_2,
		3 => Lang::NEWS_CAT_3,
		4 => Lang::NEWS_CAT_4,
		5 => Lang::NEWS_CAT_5,
		6 => Lang::NEWS_CAT_6,
		7 => Lang::NEWS_CAT_7
	);
	
	echo Lang::CATEGORY.': <select name="categ">
	<option value="*"'.attr_($_POST['categ'], '*').'>'.Lang::ALL_CATEGORIES.'</option>';
	foreach ($categs as $key => $val) {
		echo '<option'.attr_($_POST['categ'], $key).' value="'.$key.'">'.$val.'</option>';
	}
	
	echo '</select><br /><input type="submit" value="'.Lang::VALIDATE.'" /></form><br />';
	
	$req = "	SELECT n.id, n.poster, n.daten, n.titre, n.categorie, count( c.id )
				FROM lg_newsmod n, lg_comment c
				WHERE c.news_id = n.id
				AND n.afficher =1
				AND n.daten > ".$temps1."
				AND n.daten < ".$temps2."
				AND deleted = 0
			";
	
	if (isset($_POST['categ']) and isset($_POST['newser'])) {	
		if ($_POST['categ'] != '*') $req .= "AND n.categorie = '".$_POST['categ']."'\n";
		if ($_POST['newser'] != '*') $req .= "AND n.poster = '".$_POST['newser']."'\n";
	}
			
	$req .= "	GROUP BY n.id
				ORDER BY n.id DESC";
				
	$t = mysql_query($req);
	echo '<table>';
	while ($l = mysql_fetch_row($t)) {
		echo '<tr>
				<td style="padding: 0px 10px;"><img src="img/news/icons/news_scroll.jpg" alt="" /></td>
				<td><a href="?f=news&id='.$l[0].'">'.stripslashes($l[3]).'</a> (<a href="?f=news&amp;id='.$l[0].'&start='.getStart($l[5]).'#comment">'.$l[5].'</a>)<br />le '.date("d/m",$l[2]).' par <b>'.$l[1].'</b></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>';
	}
	echo '</table>';
	
	ArghPanel::end_tag();
?>