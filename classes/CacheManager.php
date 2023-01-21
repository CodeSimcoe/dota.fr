<?php
class CacheManager {
	
	const DIVISION_CACHE = '/var/www/ligue/cache/division_cache.txt';
	const LADDER_VERSION_CACHE = '/var/www/ligue/cache/version.txt';
	const LADDER_MODE_CACHE_ODD = '/var/www/ligue/cache/mode_odd.txt';
	const LADDER_MODE_CACHE_EVEN = '/var/www/ligue/cache/mode_even.txt';
	const WARCRAFT_VERSION_CACHE = '/var/www/ligue/cache/w3_version.txt';
	
	const LADDER_PLAYERLIST = '/var/www/ligue/cache/playerlist.txt';

	const LADDER_PLAYERLIST_ODD = '/var/www/ligue/cache/ladder_playerlist_odd.txt';
	const LADDER_PLAYERLIST_EVEN = '/var/www/ligue/cache/ladder_playerlist_even.txt';
	
	const LADDER_VIP_PLAYERLIST = '/var/www/ligue/cache/playerlistvip.txt';
	const LADDER_VIP_OBSERVERSLIST = '/var/www/ligue/cache/observers_vip.txt';
	
	//Cache writing
	private static function write_cache($cache, $content) {
		$handle = fopen($cache, 'w');
		fwrite($handle, $content);
		fclose($handle);
	}
	
	//Cache reading
	private static function read_cache($cache) {
		if (file_exists($cache)) {
			$handle = fopen($cache, 'r');
			$mode = fread($handle, filesize($cache));
			return $mode;
		}
	}
	
	public static function get_division_cache() {
		$out = array();
		if (file_exists(self::DIVISION_CACHE)) {
			$content = file(self::DIVISION_CACHE);
			foreach ($content as $val) {
				$out[] = trim($val);
			}
		}
		return $out;
	}
	
	public static function get_ladder_version() {
		return self::read_cache(self::LADDER_VERSION_CACHE);
	}
	
	public static function write_ladder_version($version) {
		self::write_cache(self::LADDER_VERSION_CACHE, $version);
	}
	
	public static function get_ladder_mode_odd() {
		return self::read_cache(self::LADDER_MODE_CACHE_ODD);
	}
	
	public static function write_ladder_mode_odd($mode) {
		self::write_cache(self::LADDER_MODE_CACHE_ODD, $mode);
	}
	
	public static function get_ladder_mode_even() {
		return self::read_cache(self::LADDER_MODE_CACHE_EVEN);
	}
	
	public static function write_ladder_mode_even($mode) {
		self::write_cache(self::LADDER_MODE_CACHE_EVEN, $mode);
	}
	
	public static function get_w3_version() {
		return self::read_cache(self::WARCRAFT_VERSION_CACHE);
	}
	
	public static function write_w3_version($mode) {
		self::write_cache(self::WARCRAFT_VERSION_CACHE, $mode);
	}
	
	public static function get_ladder_mode_modulo($mod) {
		return ($mod % 2) ? self::get_ladder_mode_even() : self::get_ladder_mode_odd();
	}
}
?>