<?php

	echo '<a href="/index.php">Home</a>';
	if (ALLOW_LEAGUE) echo '<a href="/league.php">League</a>';
	if (ALLOW_LADDER) echo '<a href="/ladder.php">Ladder</a>';
	echo '<a href="#">Forum</a>';

?>