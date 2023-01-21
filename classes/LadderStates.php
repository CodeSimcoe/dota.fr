<?php
abstract class LadderStates {
	
	//Players
	const READY = 'ready';
	const IN_NORMAL_GAME = 'busy_norm';
	const IN_VIP_GAME = 'busy_vip';
	
	//Players' results
	const LEAVER = 1;
	const AWAY = 2;
	const BEHAVIOR = 3;
	
	static $PLAYERS_INFOS = array(
		self::LEAVER => Lang::LEAVER,
		self::AWAY => Lang::AWAY,
		self::BEHAVIOR => Lang::BEHAVIOR
	);
	
	//Games
	const CLOSED = 'closed';
	const REPORTING = 'reporting';
	const ADMIN_OPENED = 'admin_opened';
	const PLAYING = 'playing';
	const OPENED = 'opened';
	const OPEN = 'open';
	const CHOOSING = 'choosing';
	const PICKING = 'picking';
	const BANNING = 'banning';
}
?>