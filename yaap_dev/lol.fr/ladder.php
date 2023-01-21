<?php

	require_once '__local.config.php';

	if (!ALLOW_LADDER) header('Location: /index.php');

	$menu_left_current = 'menu_left_ladder.php';

	include_once '__local.template.php';

?>