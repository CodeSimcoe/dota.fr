<?php

require_once '/home/www/ligue/classes/CacheManager.php';

abstract class VipManager {

	const NO_RANK = 230;

	const STEP_ORDER_PICK = 1;

	const STEP_SIDE_PICK = 2;

	const STEP_PLAYER_PICK_1 = 3;
	const STEP_PLAYER_PICK_2 = 4;
	const STEP_PLAYER_PICK_3 = 5;
	const STEP_PLAYER_PICK_4 = 6;
	const STEP_PLAYER_PICK_5 = 7;

	const STEP_BAN_1 = 8;
	const STEP_BAN_2 = 9;
	const STEP_BAN_3 = 10;
	const STEP_BAN_4 = 11;
	const STEP_BAN_5 = 12;
	const STEP_BAN_6 = 13;
	const STEP_BAN_7 = 14;
	const STEP_BAN_8 = 15;

	const STEP_HERO_1 = 16;
	const STEP_HERO_2 = 17;
	const STEP_HERO_3 = 18;
	const STEP_HERO_4 = 19;
	const STEP_HERO_5 = 20;
	const STEP_HERO_6 = 21;

	public static function AddPlayerToCache($username, $rank, $ggc) {
		$handle = fopen(CacheManager::LADDER_VIP_PLAYERLIST, 'a');
		$content = $username.';'.$rank.';'.$ggc.';'.time()."\n";
		fwrite($handle, $content);
		fclose($handle);
	}
	
	public static function RemovePlayerFromCache($username) {
		$content = file(CacheManager::LADDER_VIP_PLAYERLIST);
		$handle = fopen(CacheManager::LADDER_VIP_PLAYERLIST, 'w+');
		foreach ($content as $val) {
			$line = explode(';', $val);
			if ($line[0] != $username) fwrite($handle, $val);
		}
		fclose($handle);
	}

	public static function GetPlayersFromCache() {
		$players = array();
		$content = file(CacheManager::LADDER_VIP_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (count($line) == 4) $players = array_merge($players, array($line));
		}
		return $players;
	}

	public static function ClearCache() {
		$handle = fopen(CacheManager::LADDER_VIP_PLAYERLIST, 'w');
		fwrite($handle, '');
		fclose($handle);
	}

	public static function UpdatePlayersStatus($players) {
		$req = mysql_query("UPDATE lg_users SET  ladder_status = 'busy_vip' WHERE username IN ('".implode("', '", $players)."')") or die(mysql_error());
	}

	public static function OpenNewGame() {
		$req = mysql_query("INSERT INTO lg_laddervip_games (status) VALUES ('opened')") or die(mysql_error());
	}
	
	public static function GetPlayerRank($username) {
		$req = "
			SELECT * FROM (
				SELECT @num := @num + 1 AS 'rank', username, xp
				FROM lg_laddervip_players, (SELECT @num := 0) `DerivedTable`
				WHERE played > 0
				ORDER BY xp DESC, wins / loses DESC
			) A
			WHERE username = '".username."'";
		$res = mysql_query($req) or die(mysql_error());
		$obj = mysql_fetch_object($res);
		return VipManager::XPRank(empty($obj->rank) ? VipManager::NO_RANK : $obj->rank);
	}
	
	public static function XPRank($rank) {
		$values = array(1, 3, 6, 11, 19, 32, 53, 87, 142, 231, 375, 608, 985, 1595, 2582, 4179, 6763, 10944, 17709, 28655);
		foreach ($values as $key => $val) {
			if ($rank <= $val) return $key + 1;
		}
		return 0;
	}

}

?>