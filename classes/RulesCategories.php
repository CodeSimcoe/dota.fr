<?php
abstract class RulesCategories {
	
	const LEAGUE = 1;
	const LADDER = 2;
	const LADDER_VIP = 3;
	const TOURNAMENT = 4;
	
	function getCategoryByType($type) {
		switch ($type) {
			case self::LEAGUE:
				return Lang::LEAGUE;
			case self::LADDER:
				return Lang::LADDER;
			case self::LADDER_VIP:
				return lang::LADDER_VIP;
		}
	}
}
?>