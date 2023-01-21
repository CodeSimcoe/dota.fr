<?php
	require 'classes/RulesCategories.php';
	
	ArghPanel::begin_tag(Lang::LEAGUE_RULES);
	
	$req = "SELECT rules FROM lg_rules WHERE type = '".RulesCategories::LEAGUE."' ORDER BY season DESC, version DESC LIMIT 1";
	
	$t = mysql_query($req);
	$l = mysql_fetch_row($t);
	
	echo stripslashes($l[0]);
	
	ArghPanel::end_tag();
?>