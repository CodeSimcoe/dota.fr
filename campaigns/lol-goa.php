<?php
	if ($GOA == 2 && ArghSession::display_ad()) {
		echo '<br /><center>';
		echo '<script type="text/javascript" src="http://orange3.solution.weborama.fr/fcgi-bin/adserv.fcgi?tag=283979&f=9&ef=1&clicktag=[URLTRACKING]&rnd=[RANDOM]"></script><noscript><img src="http://cstatic.weborama.fr/weborama/images/transp.gif" width="300" height="250" border="0" alt=""></noscript>';
		echo '</center>';
		$GOA = 0;
	}
?>