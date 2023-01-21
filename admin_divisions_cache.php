<?php

	ArghSession::exit_if_not_rights(
		RightsMode::LEAGUE_HEADADMIN
	);
	
	$file = CacheManager::DIVISION_CACHE;
	
	if (isset($_POST['sync'])) {
		$div2 = array();
		$req = "SELECT nom FROM lg_divisions ORDER BY nom ASC";
		$t = mysql_query($req);
		$divisions = '';
		while ($row = mysql_fetch_row($t)) {
			$divisions .= $row[0]."\n";
		}
		$handle = fopen($file, 'w');
		fwrite($handle, $divisions);
		fclose($handle);
		
		$al = new AdminLog(Lang::ADMIN_LOG_DIVISION_CACHE_UPDATED, AdminLog::TYPE_LEAGUE);
		$al->save_log();
	}
	
	function array_match($array1, $array2) {
		
		if (count($array1) != count($array2)) {
			return false;
		} else {
			for ($i = 0; $i < count($array1); $i++) {
				if ($array1[$i] != $array2[$i]) {
					echo $array1[$i].'-'.$i.'-'.$array2[$i];
					return false;
				}
			}
		}
		
		return true;
	}
	
	ArghPanel::begin_tag(Lang::ADMIN_DIVISION_CACHE_MANAGEMENT);
	echo '<table class="simple">
		<tr><td valign="top"><span class="vip"><b>'.Lang::ADMIN_CURRENT_CACHE.'</b></span><br /><ul>';

	$div1 = array();
	$content = file($file);
	foreach ($content as $line) {
		echo '<li>'.$line.'</li>';
		$div1[] = trim($line);
	}

	echo '</ul></td><td valign="top"><span class="vip"><b>'.Lang::ADMIN_CURRENT_DIVISIONS.'</b></span><br /><ul>';

	$div2 = array();
	$req = "SELECT nom FROM lg_divisions ORDER BY nom ASC";
	$t = mysql_query($req);
	while ($row = mysql_fetch_row($t)) {
		$div2[] = $row[0];
		echo '<li>'.$row[0].'</li>';
	}
	
	echo '</ul></td></tr></table><br />';
	if (array_match($div1, $div2)) {
		echo '<center><b><span class="win">'.Lang::ADMIN_CACHE_UP_TO_DATE.'</span></b></center>';
	} else {
		echo '<center><b><span class="lose">'.Lang::ADMIN_CACHE_OUT_OF_DATE.'</span></b></center>';
		echo '<br /><br /><form action="?f=admin_divisions_cache" method="POST"><input type="submit" name="sync" value="'.Lang::SYNCHRONISE.'" /></form></center>';
	}
	ArghPanel::end_tag();
?>