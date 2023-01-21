<?php
abstract class Alternator {
	
	const CSS_ALTERNATE_CLASS = 'alternate';
	
	public static function get_alternation(&$mod, $modulus = 2) {
		return ($mod++ % $modulus) ? ' class="'.self::CSS_ALTERNATE_CLASS.'"' : '';
	}
}
?>