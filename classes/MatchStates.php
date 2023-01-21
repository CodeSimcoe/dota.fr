<?php
abstract class MatchStates {
	
	const MASKED = -1;
	const NOT_PLAYED_YET = 1;
	const TEAM_ONE_REGULAR_WIN = 4;
	const TEAM_TWO_REGULAR_WIN = 5;
	const DRAW_REGULAR_SENTINEL = 6;
	const DRAW_REGULAR_SCOURGE = 11;
	const TEAM_ONE_DEFAULT_WIN = 2;
	const TEAM_TWO_DEFAULT_WIN = 3;
	const ADMIN_CLOSED = 12;
	const TEAM_ONE_WINS_WITH_SCOURGE_DEFWIN = 7;
	const TEAM_ONE_WINS_WITH_SENTINEL_DEFWIN = 8;
	const TEAM_TWO_WINS_WITH_SCOURGE_DEFWIN = 10;
	const TEAM_TWO_WINS_WITH_SENTINEL_DEFWIN = 9;

/*
	private static function is_match_played($state) {
		return ($state != self::NOT_PLAYED_YET &&
				$state != self::ADMIN_CLOSED &&
				$state != self::TEAM_ONE_DEFAULT_WIN &&
				$state != self::TEAM_TWO_DEFAULT_WIN) ? true : false;
	}
*/
}
?>