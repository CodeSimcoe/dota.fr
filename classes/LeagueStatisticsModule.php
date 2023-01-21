<?php

class LeagueStatistics
{

	protected $_properties = array();

	protected $_username;
	protected $_team_id;
	protected $_team_tag;
	protected $_divi;
	protected $_games;
	protected $_kills;
	protected $_deaths;
	protected $_assists;
	protected $_creeps;
	protected $_denies;
	protected $_neutrals;
	protected $_towers;
	protected $_towers_denies;

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

abstract class LeagueStatisticsModule
{

	private static function fill_array_from_sql($sql_result) {
		$result = array();
		if (mysql_num_rows($sql_result) != 0) {
			while ($sql = mysql_fetch_object($sql_result)) {
				$obj = new LeagueStatistics();
				$obj->username = $sql->username;
				$obj->team_id = $sql->team_id;
				$obj->team_tag = $sql->team_tag;
				$obj->divi = $sql->divi;
				$obj->games = $sql->games;
				$obj->kills = $sql->kills;
				$obj->deaths = $sql->deaths;
				$obj->assists = $sql->assists;
				$obj->creeps = $sql->creeps;
				$obj->denies = $sql->denies;
				$obj->neutrals = $sql->neutrals;
				$obj->towers = $sql->towers;
				$obj->towers_denies = $sql->towers_denies;
				$result[] = $obj;
			}
		}
		return $result;
	}

	public static function render_replays_stats_table($stats, $divi, $order, $direction, $title, $take) {
		$divisions = CacheManager::get_division_cache();
		$html = '<br />';
		$html .= '<table border="0" cellpadding="2" cellspacing="0" class="listing parser">';
		$html .= '<colgroup><col /><col width="60" /><col width="30" /><col width="40" /><col width="40" /><col width="40" /><col width="50" /><col width="50" /><col width="50" /><col width="30" /><col width="30" /></colgroup>';
		$html .= '<caption><span style="float: right; margin-top: 3px;"><a href="javascript:void(0);" onclick="$(this).parents(\'div.ui-tabs-panel:eq(0)\').load(\'ajax/get_league_statistics.php?mode='.strtolower($order).'_'.(strtoupper($direction) == 'ASC' ? 'desc' : 'asc').'&divi=\' + $(this).parents(\'div.ui-tabs-panel:eq(0)\').find(\'select\').val());">'.(strtoupper($direction) == 'ASC' ? 'Top' : 'Bottom').'&nbsp;'.$take.'</a></span>';
		$html .= '<select style="width: 200px; margin-right: 20px;" onchange="$(this).parents(\'div.ui-tabs-panel:eq(0)\').load(\'ajax/get_league_statistics.php?mode='.strtolower($order).'_'.strtolower($direction).'&divi=\' + $(this).val());"">';
		$html .= '<option'.attr_($divi, 'all').' value="all">Toutes les divisions</option>';
		foreach ($divisions as $div) $html .= '<option'.attr_($divi, $div).' value="'.$div.'">'.Lang::DIVISION.' '.$div.'</option>';
		$html .= '</select>';
		$html .= (strtoupper($direction) == 'ASC' ? 'Bottom' : 'Top').'&nbsp;'.$take.'&nbsp;-&nbsp;'.$title;
		$html .= '</caption>';
		$html .= '<thead><tr style="cursor: default;">';
		$html .= '<th style="padding-left: 2px;">'.Lang::PLAYER.'</th>';
		$html .= '<th style="text-align: right; padding-right: 2px;">'.Lang::TEAM.'</th>';
		$html .= '<th style="text-align: right; padding-right: 2px;">G</th>';
		$html .= '<th style="text-align: right; padding-right: 2px;">K</th>';
		$html .= '<th style="text-align: right; padding-right: 2px;">D</th>';
		$html .= '<th style="text-align: right; padding-right: 2px;">A</th>';
		$html .= '<th style="text-align: right; padding-right: 2px;">CK</th>';
		$html .= '<th style="text-align: right; padding-right: 2px;">CD</th>';
		$html .= '<th style="text-align: right; padding-right: 2px;">N</th>';
		$html .= '<th style="text-align: right; padding-right: 2px;">TK</th>';
		$html .= '<th style="text-align: right; padding-right: 2px;">TD</th>';
		$html .= '</tr></thead>';
		$html .= '<tbody>';
		$count = 0;
		foreach ($stats AS $row) {
			$html .= '<tr'.Alternator::get_alternation($count).'>';
			$html .= '<td><a href="?f=player_profile&player='.$row->username.'">'.$row->username.'</a></td>';
			$html .= '<td class="right"><a href="?f=team_profile&id='.$row->team_id.'">['.$row->team_tag.']</a></td>';
			$html .= '<td class="right">'.$row->games.'</td>';
			$html .= ($order == 'kills') ? '<td class="right" style="color: #F90;"><strong>'.$row->kills.'</strong></td>' : '<td class="right">'.$row->kills.'</td>';
			$html .= ($order == 'deaths') ? '<td class="right" style="color: #F90;"><strong>'.$row->deaths.'</strong></td>' : '<td class="right">'.$row->deaths.'</td>';
			$html .= ($order == 'assists') ? '<td class="right" style="color: #F90;"><strong>'.$row->assists.'</strong></td>' : '<td class="right">'.$row->assists.'</td>';
			$html .= ($order == 'creeps') ? '<td class="right" style="color: #F90;"><strong>'.$row->creeps.'</strong></td>' : '<td class="right">'.$row->creeps.'</td>';
			$html .= ($order == 'denies') ? '<td class="right" style="color: #F90;"><strong>'.$row->denies.'</strong></td>' : '<td class="right">'.$row->denies.'</td>';
			$html .= ($order == 'neutrals') ? '<td class="right" style="color: #F90;"><strong>'.$row->neutrals.'</strong></td>' : '<td class="right">'.$row->neutrals.'</td>';
			$html .= ($order == 'towers') ? '<td class="right" style="color: #F90;"><strong>'.$row->towers.'</strong></td>' : '<td class="right">'.$row->towers.'</td>';
			$html .= ($order == 'towers_denies') ? '<td class="right" style="color: #F90;"><strong>'.$row->towers_denies.'</strong></td>' : '<td class="right">'.$row->towers_denies.'</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

	public static function get_replays_stats($divi, $order, $skip, $take) {
		$req = "
			SELECT
				T1.username,
				T1.team_id,
				T2.tag AS 'team_tag',
				T1.divi,
				COUNT(*) AS 'games',
				SUM(kills) AS 'kills',
				SUM(deaths) AS 'deaths',
				SUM(assists) AS 'assists',
				SUM(creeps) AS 'creeps',
				SUM(denies) AS 'denies',
				SUM(neutrals) AS 'neutrals',
				SUM(towers) AS 'towers',
				SUM(towers_denies) AS 'towers_denies'
			FROM lg_matchs_stats AS T1
			INNER JOIN lg_clans AS T2 ON T1.team_id = T2.id";
		if ($divi != 'all') {
			$req .= " 
				WHERE T1.divi = '".mysql_real_escape_string($divi)."'";
		}
		$req .= "
			GROUP BY T1.username, T1.team_id, T2.tag, T1.divi
			ORDER BY ".$order.", T1.username ASC
			LIMIT ".$skip.", ".$take;
		$res = mysql_query($req) or die(mysql_error());
		return self::fill_array_from_sql($res);
	}

}

?>