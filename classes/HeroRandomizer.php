<?php
abstract class HeroRandomizer {
	
	static $HERO_ARRAY = array(
		'alchemist',
		'beastmaster',
		'brewmaster',
		'firelord',
		'ranger',
		'tinker',
		'seawitch',
		'pitlord'
	);
	
	public static function display_random_picture() {
		$index = rand(0, count(self::$HERO_ARRAY) - 1);
		$path = 'pics/'.self::$HERO_ARRAY[$index].'-static.gif';
		echo '<img src="'.$path.'" title="League Time" alt="'.self::$HERO_ARRAY[$index].'" />';
	}
}
?>