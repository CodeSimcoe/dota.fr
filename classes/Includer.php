<?php
abstract class Includer {
	
	const HOME = 'main';
	
	public static $ALLOWED_PAGES = array();
	
	public static function is_valid_page($page) {
		return in_array($page, self::$ALLOWED_PAGES) ? true : false;
	}
}
?>