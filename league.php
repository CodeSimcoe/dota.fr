<?php
	//ArghPanel::begin_tag(Lang::LEAGUE);
	
	$divs = CacheManager::get_division_cache();
	
	foreach ($divs as $div) {
		$_GET['div'] = $div;
		include 'ajax/get_division_recap.php';
	}
	
	//ArghPanel::end_tag();
?>