<?php

	require_once '/home/www/ligue/classes/ArghPanel.php';
	require_once '/home/www/ligue/classes/RightsMode.php';
	require_once '/home/www/ligue/classes/ArghSession.php';

	ArghSession::begin();

	require_once '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';

	$player = '';
	if (isset($_GET['player'])) {
		$player = substr($_GET['player'], 0, 25);
	}


	if ($player != '') {

		echo '<div id="tabs-ladder-stats">';
		echo '<ul>';
		if (ArghSession::is_gold()) {
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=pie&player='.$player.'"><img src="/ligue/img/pie.png" width="20" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=player&player='.$player.'"><img src="/ligue/img/listing.png" width="20" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=chart&player='.$player.'"><img src="/ligue/img/chart.png" width="20" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=allies_best&player='.$player.'"><img src="/ligue/img/player_with_best.gif" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=allies_worst&player='.$player.'"><img src="/ligue/img/player_with_worst.gif" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=against_worst&player='.$player.'"><img src="/ligue/img/player_against_worst.gif" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=against_best&player='.$player.'"><img src="/ligue/img/player_against_best.gif" align="absmiddle" alt="" border="0" /></a></li>';
		} else if (ArghSession::get_username() == $player) {
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=pie&player='.$player.'"><img src="/ligue/img/pie.png" width="20" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=player&player='.$player.'"><img src="/ligue/img/listing.png" width="20" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/chart.png" width="20" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/player_with_best.gif" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/player_with_worst.gif" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/player_against_worst.gif" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/player_against_best.gif" align="absmiddle" alt="" border="0" /></a></li>';
		} else {
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=pie&player='.$player.'"><img src="/ligue/img/pie.png" width="20" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/listing.png" width="20" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/chart.png" width="20" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/player_with_best.gif" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/player_with_worst.gif" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/player_against_worst.gif" align="absmiddle" alt="" border="0" /></a></li>';
			echo '<li class="ui-state-disabled"><a title="tabs-ladder-stats-container" href="javascript:void(0);"><img src="/ligue/img/player_against_best.gif" align="absmiddle" alt="" border="0" /></a></li>';
		}
		if (ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN, RightsMode::VIP_HEADADMIN, RightsMode::VIP_ADMIN))) {
			echo '<li><a title="tabs-ladder-stats-container" href="ajax/get_ladder_statistics.php?mode=qg&player='.$player.'"><img src="/ligue/img/listing2.png"" width="20" align="absmiddle" alt="" border="0" /></a></li>';
		}
		echo '</ul>';
		echo '<div id="tabs-ladder-stats-container"></div>';
		echo '</div>';
	
	}

?>