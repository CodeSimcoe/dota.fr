<link rel="stylesheet" href="themes/default/parser.css" type="text/css">
<?php

	require_once '/home/www/ligue/classes/LeagueStatisticsModule.php';

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::WEBMASTER,
			RightsMode::GUARDIAN_ADMIN
		)
	);

	ArghPanel::begin_tag('');
	
	echo '<style type="text/css">.ui-tabs .ui-tabs-nav LI A { padding: 2px 4px; font-size: 10pt; }</style>';
	echo '<script type="text/javascript" src="/ligue/javascript/ui.tabs.js"></script>';
	echo '<script type="text/javascript">';
	echo 'var tabs_league = ["picks", "bans", "kills_desc", "deaths_desc", "assists_desc", "creeps_desc", "denies_desc", "neutrals_desc", "towers_desc", "towers_denies_desc"];';
	echo '$(document).ready(function() { ';
	echo '$("#tabs").tabs({ cache: false, select: function(event, ui) {';
	echo 'var u = "ajax/get_league_statistics.php?mode=" + tabs_league[ui.index];';
	echo 'if ($("#tabs-container select").length == 1) u += "&divi=" + $("#tabs-container select").val();';
	echo '$("#tabs").tabs("url", ui.index, u);';
	echo '} });';
	echo ' });</script>';
	echo '<div id="tabs">';
	echo '<ul>';
	echo '<li><a href="ajax/get_league_statistics.php?mode=picks" title="tabs-container">Picks</a></li>';
	echo '<li><a href="ajax/get_league_statistics.php?mode=bans" title="tabs-container">Bans</a></li>';
	echo '<li><a href="ajax/get_league_statistics.php?mode=kills_desc" title="tabs-container">Kills</a></li>';
	echo '<li><a href="ajax/get_league_statistics.php?mode=deaths_desc" title="tabs-container">Deaths</a></li>';
	echo '<li><a href="ajax/get_league_statistics.php?mode=assists_desc" title="tabs-container">Assists</a></li>';
	echo '<li><a href="ajax/get_league_statistics.php?mode=creeps_desc" title="tabs-container">Creeps</a></li>';
	echo '<li><a href="ajax/get_league_statistics.php?mode=denies_desc" title="tabs-container">Denies</a></li>';
	echo '<li><a href="ajax/get_league_statistics.php?mode=neutrals_desc" title="tabs-container">Neutrals</a></li>';
	echo '<li><a href="ajax/get_league_statistics.php?mode=towers_desc" title="tabs-container">Towers</a></li>';
	echo '<li><a href="ajax/get_league_statistics.php?mode=towers_denies_desc" title="tabs-container">Towers denies</a></li>';
	echo '</ul>';
	echo '<div id="tabs-container"></div>';
	echo '</div>';

	ArghPanel::end_tag();
?>