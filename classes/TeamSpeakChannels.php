<?php
abstract class TeamSpeakChannels {
	
	static $LADDER_CHANNELS = array(
		'A',
		'B',
		'C',
		'D',
		'E',
		'F',
		'G',
		'H',
		'I',
		'J',
		'K',
		'L',
		'M',
		'N',
		'O',
		'P',
		'Q',
		'R',
		'S'
	);
	
	static $LADDERVIP_CHANNELS = array(
		'A',
		'B',
		'C',
		'D'
	);
	
	public static function get_ladder_channel($game_id) {
		return self::$LADDER_CHANNELS[$game_id % count(self::$LADDER_CHANNELS)];
	}
	
	public static function get_laddervip_channel($game_id) {
		return self::$LADDERVIP_CHANNELS[$game_id % count(self::$LADDERVIP_CHANNELS)];
	}
}
?>