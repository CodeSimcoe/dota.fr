<?php
	if ($GOA <= 5/* && ArghSession::display_ad()*/) {
		echo '<br /><center>';
		echo '<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.fr.doubleclick.net/adj/N884.dotanetwork.fr/B4012287;sz=300x250;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.fr.doubleclick.net/jump/N884.dotanetwork.fr/B4012287;sz=300x250;ord=[timestamp]?">
<IMG SRC="http://ad.fr.doubleclick.net/ad/N884.dotanetwork.fr/B4012287;sz=300x250;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=250 ALT="Click Here"></A>
</NOSCRIPT>';
		//echo '<script type="text/javascript" src="http://orange3.solution.weborama.fr/fcgi-bin/adserv.fcgi?tag=283979&f=9&ef=1&clicktag=[URLTRACKING]&rnd=[RANDOM]"></script><noscript><img src="http://cstatic.weborama.fr/weborama/images/transp.gif" width="300" height="250" border="0" alt=""></noscript>';
		echo '</center>';
		$GOA = 0;
	}
?>