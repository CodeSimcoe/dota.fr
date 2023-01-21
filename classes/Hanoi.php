<?php
	$i = 0;

	function hanoi($nbr, $dep, $fin, $int) {
		if ($nbr > 0) {
			hanoi($nbr - 1, $dep, $int, $fin);
			echo ($i++).'. '.$dep.'->'.$fin.'<br />';
			hanoi($nbr - 1, $int, $fin, $dep);
		}
	}
	
	hanoi(4, 'A', 'C', 'B');
?>