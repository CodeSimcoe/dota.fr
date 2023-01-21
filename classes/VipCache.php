<?php

define('VIP_CACHE_PATH', '/home/www/dota/cache/');

class VipCache
{

	var $game_id;

	var $players_pool = array();
	var $cap_a, $cap_b;
	var $players_sentinel = array();
	var $players_scourge = array();

	var $order_pick;
	var $side_pick;
	
	var $bans_sentinel;
	var $bans_scourge;
	
	var $heros_sentinel;
	var $heros_scourge;

	var $current_step = 0;
	var $action_time;

	static function load($game_id) {
		if (is_file(VIP_CACHE_PATH.$game_id.'.txt')) {
			$content = file(VIP_CACHE_PATH.$game_id.'.txt');
			return unserialize($content[0]);
		} else {
			return new VipCache($game_id);
		}
	}

	static function is_in_cache($game_id) {
		return is_file(VIP_CACHE_PATH.$game_id.'.txt');
	}

	function __construct($game_id) {
		$this->game_id = $game_id;
		$this->save();
	}

	function change_step($step) {
		$this->current_step = $step;
		$this->action_time = time();
		$this->save();
	}
	
	function set_players($players) {
		$this->players_pool = $players;
		$this->save();
	}
	
	function set_caps($cap_a, $cap_b) {
		$this->cap_a = $cap_a;
		$this->cap_b = $cap_b;
		$this->save();
	}
	
	function save() {
		$handle = fopen(VIP_CACHE_PATH.$this->game_id.'.txt', 'w');
		$content = serialize($this);
		fwrite($handle, $content);
		fclose($handle);
	}

}

?>