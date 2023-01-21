<?php

class LadderStatistics
{

	protected $_properties = array();

	protected $_label;
	protected $_played;
	protected $_closed;
	protected $_win;
	protected $_lose;
	protected $_away;
	protected $_left;
	protected $_xp;

	public function __set($key, $value) 
	{
		$key = '_'.$key;
		if (property_exists($this, $key)) $this->$key = $value;
		else $this->_properties[$key] = $value;
    }
	public function __get($key) 
	{
		$key = '_'.$key;
		if (property_exists($this, $key)) return $this->$key;
		else if (isset($this->_properties[$key])) return $this->_properties[$key];
		return '';
	}
	
}

abstract class LadderStatisticsModule
{

	private static function fill_array_from_sql($sql_result, $username, $with = '') {
		$result = array();
		if (mysql_num_rows($sql_result) != 0) {
			while ($sql = mysql_fetch_object($sql_result)) {
				$obj = new LadderStatistics();
				$obj->username = $username;
				$obj->with = ($with == '') ? $sql->with : $with;
				$obj->game_id = $sql->game_id;
				$obj->opened = $sql->opened;
				$obj->year = $sql->year;
				$obj->month = $sql->month;
				$obj->day = $sql->day;
				$obj->side = $sql->side;
				$obj->pick = $sql->pick;
				$obj->played = $sql->played;
				$obj->closed = $sql->closed;
				$obj->win = $sql->win;
				$obj->lose = $sql->lose;
				$obj->away = $sql->away;
				$obj->left = $sql->left;
				$obj->xp = $sql->xp;
				$result[] = $obj;
			}
		}
		return $result;
	}

	public static function render_vip_stats_table($stats, $title, $func, $render_total = true, $caption = '') {
		$out = '';
		$count = 0;
		$total = array( 'P' => 0, 'C' => 0, 'W' => 0, 'Lo' => 0, 'A' => 0, 'Le' => 0 );
		$out .= '<table class="listing">';
		$out .= '<colgroup><col /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="50" /></colgroup>';
		if ($caption != '') $out .= '<caption>'.$caption.'</caption>';
		$out .= '<thead><tr><th>'.$title.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_PLAYED_TITLE.'">'.Lang::LADDER_STATS_PLAYED_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_CLOSED_TITLE.'">'.Lang::LADDER_STATS_CLOSED_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_WIN_TITLE.'">'.Lang::LADDER_STATS_WIN_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_LOSE_TITLE.'">'.Lang::LADDER_STATS_LOSE_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_AWAY_TITLE.'">'.Lang::LADDER_STATS_AWAY_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_LEFT_TITLE.'">'.Lang::LADDER_STATS_LEFT_LETTER.'</th>';
		$out .= '</tr></thead>';
		foreach ($stats as $row) {
			$out .= '<tr'.Alternator::get_alternation($count).'>';
			$out .= '<td>'.$func($row).'</td>';
			$out .= '<td>'.$row->played.'</td>';
			$out .= '<td>'.$row->closed.'</td>';
			$out .= '<td><span class="win">'.$row->win.'</span></td>';
			$out .= '<td><span class="lose">'.$row->lose.'</span></td>';
			$out .= '<td><span class="info">'.$row->away.'</span></td>';
			$out .= '<td><span class="draw">'.$row->left.'</span></td>';
			$out .= '</tr>';
			if ($render_total) {
				$total['P'] += $row->played;
				$total['C'] += $row->closed;
				$total['W'] += $row->win;
				$total['Lo'] += $row->lose;
				$total['A'] += $row->away;
				$total['Le'] += $row->left;
			}
		}
		if ($render_total) {
			$out .= '<tfoot><tr>';
			$out .= '<td>'.Lang::TOTAL.'</td>';
			$out .= '<td>'.$total['P'].'</td>';
			$out .= '<td>'.$total['C'].'</td>';
			$out .= '<td><span class="win">'.$total['W'].'</span></td>';
			$out .= '<td><span class="lose">'.$total['Lo'].'</span></td>';
			$out .= '<td><span class="info">'.$total['A'].'</span></td>';
			$out .= '<td><span class="draw">'.$total['Le'].'</span></td>';
			$out .= '</tr></tfoot>';
		}
		$out .= '</table>';
		return $out;
	}
	public static function render_vip_games_table($stats, $title, $func) {
		$out = '';
		$count = 0;
		$out .= '<table class="listing">';
		$out .= '<colgroup><col /><col width="100" /><col width="50" /></colgroup>';
		$out .= '<thead><tr><th>'.$title.'</th>';
		$out .= '<th>'.Lang::STATUS.'</th>';
		$out .= '<th>&nbsp;</th>';
		$out .= '</tr></thead>';
		foreach ($stats as $row) {
			$out .= '<tr'.Alternator::get_alternation($count).'>';
			$out .= '<td>'.$func($row).'</td>';
			if ($row->win == 1) {
				$out .= '<td><span class="win">'.Lang::WIN.'</span></td>';
				$out .= '<td><span class="win">+</span></td>';
			} else if ($row->lose == 1) {
				$out .= '<td><span class="lose">'.Lang::LOSS.'</span></td>';
				$out .= '<td><span class="lose">-</span></td>';
			} else if ($row->away == 1) {
				$out .= '<td><span class="info">'.Lang::NOT_SHOW_UP.'</span></td>';
				$out .= '<td><span class="info">/</span></td>';
			} else if ($row->left == 1) {
				$out .= '<td><span class="draw">'.Lang::LEFT.'</span></td>';
				$out .= '<td><span class="draw">-</span></td>';
			} else {
				$out .= '<td>'.Lang::GAME_CLOSED.'</td>';
				$out .= '<td>=</td>';
			}
		}
		return $out;
	}

	public static function render_stats_table($stats, $title, $func, $render_total = true, $caption = '') {
		$out = '';
		$count = 0;
		$total = array( 'P' => 0, 'C' => 0, 'W' => 0, 'Lo' => 0, 'A' => 0, 'Le' => 0, 'Xp' => 0 );
		$out .= '<table class="listing">';
		$out .= '<colgroup><col /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="80" /></colgroup>';
		if ($caption != '') $out .= '<caption>'.$caption.'</caption>';
		$out .= '<thead><tr><th>'.$title.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_PLAYED_TITLE.'">'.Lang::LADDER_STATS_PLAYED_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_CLOSED_TITLE.'">'.Lang::LADDER_STATS_CLOSED_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_WIN_TITLE.'">'.Lang::LADDER_STATS_WIN_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_LOSE_TITLE.'">'.Lang::LADDER_STATS_LOSE_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_AWAY_TITLE.'">'.Lang::LADDER_STATS_AWAY_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_LEFT_TITLE.'">'.Lang::LADDER_STATS_LEFT_LETTER.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_XP_TITLE.'">'.Lang::LADDER_STATS_XP_LETTER.'</th>';
		$out .= '</tr></thead>';
		foreach ($stats as $row) {
			$out .= '<tr'.Alternator::get_alternation($count).'>';
			$out .= '<td>'.$func($row).'</td>';
			$out .= '<td>'.$row->played.'</td>';
			$out .= '<td>'.$row->closed.'</td>';
			$out .= '<td><span class="win">'.$row->win.'</span></td>';
			$out .= '<td><span class="lose">'.$row->lose.'</span></td>';
			$out .= '<td><span class="info">'.$row->away.'</span></td>';
			$out .= '<td><span class="draw">'.$row->left.'</span></td>';
			if ($row->xp == 0) {
				$out .= '<td><span class="info">'.$row->xp.'</span></td>';
			} else if ($row->xp > 0) {
				$out .= '<td><span class="win">+'.$row->xp.'</span></td>';
			} else {
				$out .= '<td><span class="lose">'.$row->xp.'</span></td>';		
			}
			$out .= '</tr>';
			if ($render_total) {
				$total['P'] += $row->played;
				$total['C'] += $row->closed;
				$total['W'] += $row->win;
				$total['Lo'] += $row->lose;
				$total['A'] += $row->away;
				$total['Le'] += $row->left;
				$total['Xp'] += $row->xp;
			}
		}
		if ($render_total) {
			$out .= '<tfoot><tr>';
			$out .= '<td>'.Lang::TOTAL.'</td>';
			$out .= '<td>'.$total['P'].'</td>';
			$out .= '<td>'.$total['C'].'</td>';
			$out .= '<td><span class="win">'.$total['W'].'</span></td>';
			$out .= '<td><span class="lose">'.$total['Lo'].'</span></td>';
			$out .= '<td><span class="info">'.$total['A'].'</span></td>';
			$out .= '<td><span class="draw">'.$total['Le'].'</span></td>';
			if ($total['Xp'] == 0) {
				$out .= '<td><span class="info">'.$total['Xp'].'</span></td>';
			} else if ($total['Xp'] > 0) {
				$out .= '<td><span class="win">+'.$total['Xp'].'</span></td>';
			} else {
				$out .= '<td><span class="lose">'.$total['Xp'].'</span></td>';		
			}
			$out .= '</tr></tfoot>';
		}
		$out .= '</table>';
		return $out;
	}
	public static function render_games_table($stats, $title, $func) {
		$out = '';
		$count = 0;
		$out .= '<table class="listing">';
		$out .= '<colgroup><col /><col width="100" /><col width="80" /></colgroup>';
		$out .= '<thead><tr><th>'.$title.'</th>';
		$out .= '<th>'.Lang::STATUS.'</th>';
		$out .= '<th title="'.Lang::LADDER_STATS_XP_TITLE.'">'.Lang::LADDER_STATS_XP_LETTER.'</th>';
		$out .= '</tr></thead>';
		foreach ($stats as $row) {
			$out .= '<tr'.Alternator::get_alternation($count).'>';
			$out .= '<td>'.$func($row).'</td>';
			if ($row->win == 1) {
				$out .= '<td><span class="win">'.Lang::WIN.'</span></td>';
				$out .= '<td><span class="win">+'.$row->xp.'</span></td>';
			} else if ($row->lose == 1) {
				$out .= '<td><span class="lose">'.Lang::LOSS.'</span></td>';
				$out .= '<td><span class="lose">'.$row->xp.'</span></td>';
			} else if ($row->away == 1) {
				$out .= '<td><span class="info">'.Lang::NOT_SHOW_UP.'</span></td>';
				$out .= '<td><span class="info">'.($row->xp > 0 ? '+' : '').$row->xp.'</span></td>';
			} else if ($row->left == 1) {
				$out .= '<td><span class="draw">'.Lang::LEFT.'</span></td>';
				$out .= '<td><span class="draw">'.($row->xp > 0 ? '+' : '').$row->xp.'</span></td>';
			} else {
				$out .= '<td>'.Lang::GAME_CLOSED.'</td>';
				$out .= '<td>'.$row->xp.'</td>';
			}
		}
		return $out;
	}

	public static function get_vip_player_months($username) {
		$req = "
			SELECT 
				`year`, 
				`month`, 
				SUM(played) AS 'played',  
				SUM(closed) AS 'closed', 
				SUM(win) AS 'win', 
				SUM(lose) AS 'lose', 
				SUM(`left`) AS 'left', 
				SUM(away) AS 'away', 
				SUM(xp) AS 'xp'
			FROM lg_laddervip_stats
			WHERE username = '".mysql_real_escape_string($username)."'
			GROUP BY `year`, `month`
			ORDER BY `year` DESC, `month` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_vip_player_days($username, $year, $month) {
		$req = "
			SELECT 
				`year`, 
				`month`,
				`day`,
				SUM(played) AS 'played',  
				SUM(closed) AS 'closed', 
				SUM(win) AS 'win', 
				SUM(lose) AS 'lose', 
				SUM(`left`) AS 'left', 
				SUM(away) AS 'away', 
				SUM(xp) AS 'xp'
			FROM lg_laddervip_stats
			WHERE username = '".mysql_real_escape_string($username)."'
			AND `year` = ".$year." AND `month` = ".$month."
			GROUP BY `year`, `month`, `day`
			ORDER BY `year` DESC, `month` DESC, `day` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_vip_player_games($username, $year, $month, $day) {
		$req = "
			SELECT 
				game_id,
				opened,
				CAST(played AS UNSIGNED) AS 'played',  
				CAST(closed AS UNSIGNED) AS 'closed', 
				CAST(win AS UNSIGNED) AS 'win', 
				CAST(lose AS UNSIGNED) AS 'lose', 
				CAST(`left` AS UNSIGNED) AS 'left', 
				CAST(away AS UNSIGNED) AS 'away', 
				xp
			FROM lg_laddervip_stats
			WHERE username = '".mysql_real_escape_string($username)."'
			AND `year` = ".$year." AND `month` = ".$month." AND `day` = ".$day."
			ORDER BY opened DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}

	public static function get_vip_picks($username) {
		$req = "
			SELECT 
				username, 
				pick, 
				SUM(played) AS 'played', 
				SUM(closed) AS 'closed', 
				SUM(win) AS 'win', 
				SUM(lose) AS 'lose', 
				SUM(`left`) AS 'left', 
				SUM(away) AS 'away', 
				SUM(xp) AS 'xp'
			FROM lg_laddervip_stats
			WHERE username = '".mysql_real_escape_string($username)."'
			GROUP BY username, pick
			ORDER BY pick";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_vip_picks_months($username, $pick) {
		$req = "
			SELECT 
				username, 
				pick, 
				`year`,
				`month`,
				SUM(played) AS 'played', 
				SUM(closed) AS 'closed', 
				SUM(win) AS 'win', 
				SUM(lose) AS 'lose', 
				SUM(`left`) AS 'left', 
				SUM(away) AS 'away', 
				SUM(xp) AS 'xp'
			FROM lg_laddervip_stats
			WHERE username = '".mysql_real_escape_string($username)."'
			AND pick = ".$pick."
			GROUP BY `year`, `month`
			ORDER BY `year` DESC, `month` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_vip_picks_days($username, $pick, $year, $month) {
		$req = "
			SELECT 
				username, 
				pick, 
				`year`,
				`month`,
				`day`,
				SUM(played) AS 'played', 
				SUM(closed) AS 'closed', 
				SUM(win) AS 'win', 
				SUM(lose) AS 'lose', 
				SUM(`left`) AS 'left', 
				SUM(away) AS 'away', 
				SUM(xp) AS 'xp'
			FROM lg_laddervip_stats
			WHERE username = '".mysql_real_escape_string($username)."'
			AND pick = ".$pick." AND `year` = ".$year." AND `month` = ".$month."
			GROUP BY `year`, `month`, `day`
			ORDER BY `year` DESC, `month` DESC, `day` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_vip_picks_games($username, $pick, $year, $month, $day) {
		$req = "
			SELECT 
				game_id, 
				opened,
				CAST(played AS UNSIGNED) AS 'played', 
				CAST(closed AS UNSIGNED) AS 'closed', 
				CAST(win AS UNSIGNED) AS 'win', 
				CAST(lose AS UNSIGNED) AS 'lose', 
				CAST(`left` AS UNSIGNED) AS 'left', 
				CAST(away AS UNSIGNED) AS 'away', 
				xp 
			FROM lg_laddervip_stats
			WHERE username = '".mysql_real_escape_string($username)."'
			AND pick = ".$pick." AND `year` = ".$year." AND `month` = ".$month." AND `day` = ".$day."
			ORDER BY opened DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}

	public static function get_vip_allies($username, $order, $skip, $take) {
		$req = "
			SELECT 
				A.username, 
				B.username AS 'with', 
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_laddervip_stats A
			INNER JOIN lg_laddervip_stats B
			ON (A.game_id = B.game_id AND A.side = B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username <> '".mysql_real_escape_string($username)."'
			GROUP BY A.username, B.username
			HAVING SUM(A.played) > 0
			ORDER BY ".$order.", B.username ASC
			LIMIT ".$skip.", ".$take;
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_vip_allies_months($username, $with) {
		$req = "
			SELECT 
				A.`year`, 
				A.`month`, 
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_laddervip_stats A
			INNER JOIN lg_laddervip_stats B
			ON (A.game_id = B.game_id AND A.side = B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			GROUP BY A.`year`, A.`month`
			ORDER BY A.`year` DESC, A.`month` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}
	public static function get_vip_allies_days($username, $with, $year, $month) {
		$req = "
			SELECT 
				A.`year`, 
				A.`month`, 
				A.`day`, 
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_laddervip_stats A
			INNER JOIN lg_laddervip_stats B
			ON (A.game_id = B.game_id AND A.side = B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			AND A.`year` = ".$year." AND A.`month` = ".$month."
			GROUP BY A.`year`, A.`month`, A.`day`
			ORDER BY A.`year` DESC, A.`month` DESC, A.`day` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}
	public static function get_vip_allies_games($username, $with, $year, $month, $day) {
		$req = "
			SELECT 
				A.game_id, 
				A.opened,
				CAST(A.played AS UNSIGNED) AS 'played', 
				CAST(A.closed AS UNSIGNED) AS 'closed', 
				CAST(A.win AS UNSIGNED) AS 'win', 
				CAST(A.lose AS UNSIGNED) AS 'lose', 
				CAST(A.`left` AS UNSIGNED) AS 'left', 
				CAST(A.away AS UNSIGNED) AS 'away', 
				A.xp 
			FROM lg_laddervip_stats A
			INNER JOIN lg_laddervip_stats B
			ON (A.game_id = B.game_id AND A.side = B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			AND A.`year` = ".$year." AND A.`month` = ".$month." AND A.`day` = ".$day."
			ORDER BY A.opened DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}

	public static function get_vip_againsts($username, $order, $skip, $take) {
		$req = "
			SELECT 
				A.username, 
				B.username AS 'with', 
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_laddervip_stats A
			INNER JOIN lg_laddervip_stats B
			ON (A.game_id = B.game_id AND A.side <> B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			GROUP BY A.username, B.username
			HAVING SUM(A.played) > 0
			ORDER BY ".$order.", B.username ASC
			LIMIT ".$skip.", ".$take;
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_vip_againsts_months($username, $with) {
		$req = "
			SELECT 
				A.`year`, 
				A.`month`, 
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_laddervip_stats A
			INNER JOIN lg_laddervip_stats B
			ON (A.game_id = B.game_id AND A.side <> B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			GROUP BY A.`year`, A.`month`
			ORDER BY A.`year` DESC, A.`month` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}
	public static function get_vip_againsts_days($username, $with, $year, $month) {
		$req = "
			SELECT 
				A.`year`, 
				A.`month`, 
				A.`day`, 
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_laddervip_stats A
			INNER JOIN lg_laddervip_stats B
			ON (A.game_id = B.game_id AND A.side <> B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			AND A.`year` = ".$year." AND A.`month` = ".$month."
			GROUP BY A.`year`, A.`month`, A.`day`
			ORDER BY A.`year` DESC, A.`month` DESC, A.`day` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}
	public static function get_vip_againsts_games($username, $with, $year, $month, $day) {
		$req = "
			SELECT 
				A.game_id, 
				A.opened,
				CAST(A.played AS UNSIGNED) AS 'played', 
				CAST(A.closed AS UNSIGNED) AS 'closed', 
				CAST(A.win AS UNSIGNED) AS 'win', 
				CAST(A.lose AS UNSIGNED) AS 'lose', 
				CAST(A.`left` AS UNSIGNED) AS 'left', 
				CAST(A.away AS UNSIGNED) AS 'away', 
				A.xp 
			FROM lg_laddervip_stats A
			INNER JOIN lg_laddervip_stats B
			ON (A.game_id = B.game_id AND A.side <> B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			AND A.`year` = ".$year." AND A.`month` = ".$month." AND A.`day` = ".$day."
			ORDER BY A.opened DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}

	public static function get_player_months($username)	{
		$req = "
			SELECT 
				`year`, 
				`month`, 
				SUM(played) AS 'played',  
				SUM(closed) AS 'closed', 
				SUM(win) AS 'win', 
				SUM(lose) AS 'lose', 
				SUM(`left`) AS 'left', 
				SUM(away) AS 'away', 
				SUM(xp) AS 'xp'
			FROM lg_ladder_stats
			WHERE username = '".mysql_real_escape_string($username)."'
			GROUP BY `year`, `month`
			ORDER BY `year` DESC, `month` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_player_days($username, $year, $month) {
		$req = "
			SELECT 
				`year`, 
				`month`, 
				`day`, 
				SUM(played) AS 'played',  
				SUM(closed) AS 'closed', 
				SUM(win) AS 'win', 
				SUM(lose) AS 'lose', 
				SUM(`left`) AS 'left', 
				SUM(away) AS 'away', 
				SUM(xp) AS 'xp'
			FROM lg_ladder_stats
			WHERE username = '".mysql_real_escape_string($username)."' AND `year` = ".$year." AND `month` = ".$month."
			GROUP BY `year`, `month`, `day`
			ORDER BY `year` DESC, `month` DESC, `day` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_player_games($username, $year, $month, $day) {
		$req = "
			SELECT 
				game_id,
				opened,
				CAST(played AS UNSIGNED) AS 'played',  
				CAST(closed AS UNSIGNED) AS 'closed', 
				CAST(win AS UNSIGNED) AS 'win', 
				CAST(lose AS UNSIGNED) AS 'lose', 
				CAST(`left` AS UNSIGNED) AS 'left', 
				CAST(away AS UNSIGNED) AS 'away', 
				xp
			FROM lg_ladder_stats
			WHERE username = '".mysql_real_escape_string($username)."' AND `year` = ".$year." AND `month` = ".$month." AND `day` = ".$day."
			ORDER BY opened DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_player_qg($username, $skip, $take) {
		$req = "
			SELECT 
				game_id,
				opened,
				CAST(played AS UNSIGNED) AS 'played',  
				CAST(closed AS UNSIGNED) AS 'closed', 
				CAST(win AS UNSIGNED) AS 'win', 
				CAST(lose AS UNSIGNED) AS 'lose', 
				CAST(`left` AS UNSIGNED) AS 'left', 
				CAST(away AS UNSIGNED) AS 'away', 
				xp
			FROM lg_ladder_stats
			WHERE username = '".mysql_real_escape_string($username)."'
			ORDER BY opened DESC
			LIMIT ".$skip.", ".$take;
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}

	public static function get_allies($username, $order, $skip, $take) {
		$req = "
			SELECT 
				A.username, 
				B.username AS 'with', 
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_ladder_stats A
			INNER JOIN lg_ladder_stats B
			ON (A.game_id = B.game_id AND A.side = B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username <> '".mysql_real_escape_string($username)."'
			GROUP BY A.username, B.username
			ORDER BY ".$order.", B.username ASC
			LIMIT ".$skip.", ".$take;
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_allies_months($username, $with) {
		$req = "
			SELECT 
				A.`year`, 
				A.`month`, 
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_ladder_stats A
			INNER JOIN lg_ladder_stats B
			ON (A.game_id = B.game_id AND A.side = B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			GROUP BY A.`year`, A.`month`
			ORDER BY A.`year` DESC, A.`month` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}
	public static function get_allies_days($username, $with, $year, $month) {
		$req = "
			SELECT 
				A.`year`, 
				A.`month`, 
				A.`day`,
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_ladder_stats A
			INNER JOIN lg_ladder_stats B
			ON (A.game_id = B.game_id AND A.side = B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			AND A.`year` = ".$year." AND A.`month` = ".$month."
			GROUP BY A.`year`, A.`month`, A.`day`
			ORDER BY A.`year` DESC, A.`month` DESC, A.`day` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}
	public static function get_allies_games($username, $with, $year, $month, $day) {
		$req = "
			SELECT 
				A.game_id, 
				A.opened,
				CAST(A.played AS UNSIGNED) AS 'played', 
				CAST(A.closed AS UNSIGNED) AS 'closed', 
				CAST(A.win AS UNSIGNED) AS 'win', 
				CAST(A.lose AS UNSIGNED) AS 'lose', 
				CAST(A.`left` AS UNSIGNED) AS 'left', 
				CAST(A.away AS UNSIGNED) AS 'away', 
				A.xp 
			FROM lg_ladder_stats A
			INNER JOIN lg_ladder_stats B
			ON (A.game_id = B.game_id AND A.side = B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			AND A.`year` = ".$year." AND A.`month` = ".$month." AND A.`day` = ".$day."
			ORDER BY A.opened DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}

	public static function get_againsts($username, $order, $skip, $take)	{
		$req = "
			SELECT 
				A.username, 
				B.username AS 'with', 
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_ladder_stats A
			INNER JOIN lg_ladder_stats B
			ON (A.game_id = B.game_id AND A.side <> B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			GROUP BY A.username, B.username
			ORDER BY ".$order.", B.username ASC
			LIMIT ".$skip.", ".$take;
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username);
	}
	public static function get_againsts_months($username, $with)	{
		$req = "
			SELECT 
				A.`year`, 
				A.`month`, 
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_ladder_stats A
			INNER JOIN lg_ladder_stats B
			ON (A.game_id = B.game_id AND A.side <> B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			GROUP BY A.`year`, A.`month`
			ORDER BY A.`year` DESC, A.`month` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}
	public static function get_againsts_days($username, $with, $year, $month) {
		$req = "
			SELECT 
				A.`year`, 
				A.`month`, 
				A.`day`,
				SUM(A.played) AS 'played', 
				SUM(A.closed) AS 'closed', 
				SUM(A.win) AS 'win', 
				SUM(A.lose) AS 'lose', 
				SUM(A.`left`) AS 'left', 
				SUM(A.away) AS 'away', 
				SUM(A.xp) AS 'xp'
			FROM lg_ladder_stats A
			INNER JOIN lg_ladder_stats B
			ON (A.game_id = B.game_id AND A.side <> B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			AND A.`year` = ".$year." AND A.`month` = ".$month."
			GROUP BY A.`year`, A.`month`, A.`day`
			ORDER BY A.`year` DESC, A.`month` DESC, A.`day` DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}
	public static function get_againsts_games($username, $with, $year, $month, $day)	{
		$req = "
			SELECT 
				A.game_id, 
				A.opened,
				CAST(A.played AS UNSIGNED) AS 'played', 
				CAST(A.closed AS UNSIGNED) AS 'closed', 
				CAST(A.win AS UNSIGNED) AS 'win', 
				CAST(A.lose AS UNSIGNED) AS 'lose', 
				CAST(A.`left` AS UNSIGNED) AS 'left', 
				CAST(A.away AS UNSIGNED) AS 'away', 
				A.xp 
			FROM lg_ladder_stats A
			INNER JOIN lg_ladder_stats B
			ON (A.game_id = B.game_id AND A.side <> B.side)
			WHERE A.username = '".mysql_real_escape_string($username)."'
			AND B.username = '".mysql_real_escape_string($with)."'
			AND A.`year` = ".$year." AND A.`month` = ".$month." AND A.`day` = ".$day."
			ORDER BY A.opened DESC";
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res, $username, $with);
	}

}

?>