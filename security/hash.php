<?php
	//Secure Hash (Cookie)
	function cookieHash($string) {
		$seed = '#)D&=9z-:lQ_';
		return sha1(sha1($string).sha1($seed));
	}
	
	//Secure Hash (bdd)
	function passHash($string) {
		$seed = '8Oo)Q/#a__Wr';
		return md5(md5($string).md5($string));
	}
?>