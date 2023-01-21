<?php
require_once '/home/www/ligue/classes/ReplayParser.php';

class Report {
	
	const REPLAY_PATH = '/home/www/ligue/reports/replays/';
	const REPLAY_LINK = '/ligue/reports/replays/';
	
	const STATUS_NO_REPORT = 0;
	const STATUS_OPENED = 1;
	const STATUS_BEING_HANDLED = 2;
	const STATUS_REPORT_CLOSED = 3;
	
	const BANTYPE_OTHER = -1;
	const BANTYPE_NO_STATEMENT = 0;
	const BANTYPE_FLAME = 1;
	const BANTYPE_RUINING = 2;
	const BANTYPE_RULES_ABUSE = 3;
	const BANTYPE_RAGE_LEAVE = 4;
	const BANTYPE_BAD_RESULT = 5;
	const BANTYPE_USELESS_REPORT = 6;
	const BANTYPE_BUG_ABUSE = 7;
	const BANTYPE_CHEATING = 8;
	const HOST_LEAVER = 9;
	const BANTYPE_CAP_DISOBEY = 10;
	const BANTYPE_GGC_ACCOUNT = 11;
	const BANTYPE_FF_BEFORE_10_MINS = 12;
	
	public $_is_vip;
	public $_table;
	public $_games_table;
	
	public $_game_id;
	public $_opening_date;
	public $_initiator;
	public $_opening_reasons;
	public $_concerned_players;
	public $_replay;
	public $_screen1;
	public $_screen2;
	public $_screen3;
	public $_comment;
	public $_status;
	public $_admin;
	public $_close_time;
	public $_admin_comment;
	public $_messages;
	
	public function Report($is_vip = false) {
		$this->_is_vip = $is_vip;
		
		if ($is_vip) {
			$this->_table = 'lg_reports_vip';
			$this->_games_table = 'lg_laddervip_games';
		} else {
			$this->_table = 'lg_reports';
			$this->_games_table = 'lg_laddergames';
		}
	}
	
	public function save() {
		$reasons_str = (count($this->_opening_reasons) > 0) ? implode(';', $this->_opening_reasons) : '';
		$players_str = (count($this->_concerned_players) > 0) ? implode(';', $this->_concerned_players) : '';
		
		$this->_comment = str_replace('&nbsp;', ' ', $this->_comment);
	
		$query = "INSERT INTO ".$this->_table." (
					game_id,
					opening_time,
					initiator,
					reasons,
					concerned_players,
					replay,
					screen1,
					screen2,
					screen3,
					comment,
					status,
					admin
				) VALUES (
					'".$this->_game_id."',
					'".$this->_opening_date."',
					'".$this->_initiator."',
					'".$reasons_str."',
					'".$players_str."',
					'".$this->_replay."',
					'".$this->_screen1."',
					'".$this->_screen2."',
					'".$this->_screen3."',
					'".mysql_real_escape_string($this->_comment)."',
					'".$this->_status."',
					''
				)";
		mysql_query($query);
	}
	
	public function load() {
		
		$query = "SELECT * FROM ".$this->_table." WHERE game_id = '".$this->_game_id."'";
		$result = mysql_query($query) or die(mysql_error());
		$obj = mysql_fetch_object($result);
		
		$this->_opening_date = $obj->opening_time;
		$this->_initiator = $obj->initiator;
		$this->_opening_reasons = explode(';', $obj->reasons);
		$this->_concerned_players = explode(';', $obj->concerned_players);
		$this->_replay = $obj->replay;
		$this->_screen1 = $obj->screen1;
		$this->_screen2 = $obj->screen2;
		$this->_screen3 = $obj->screen3;
		$this->_comment = stripslashes($obj->comment);
		$this->_status = $obj->status;
		$this->_admin = $obj->admin;
		$this->_close_time = $obj->close_time;
		$this->_admin_comment = stripslashes($obj->admin_comment);
	}
	
	private function get_file_name() {
		if ($this->_is_vip) {
			return 'vip_'.$this->_game_id;
		} else {
			return $this->_game_id;
		}
	}
	
	public function get_replay_filepath() {
		return self::REPLAY_PATH.$this->get_file_name().'.w3g';
	}
	
	public function get_replay_link() {
		return self::REPLAY_LINK.$this->get_file_name().'.w3g';
	}
	
	public function get_replay_log_path() {
		return self::REPLAY_PATH.$this->get_file_name().'.w3g.txt';
	}
	
	public function parse_replay() {
		$parser = new ReplayParser($this->get_replay_filepath());
		$parser->txt_serialize();
	}
	
	public function show_chatlog() {
		
		$log = $this->get_replay_log_path();
		if (file_exists($log)) {
			$replay = DotaReplay::load_from_txt($log);
			echo '<hr />';
			echo ReplayFunctions::html_header($replay).'<hr />';
			echo ReplayFunctions::html_teams($replay, true).'<hr />';
			echo ReplayFunctions::html_chat($replay, true).'<hr />';
		}
	}
	
	private static function convert_time($value) {
		$output = sprintf('%02d', intval($value / 60000)).':';
		$value = $value % 60000;
		$output .= sprintf('%02d', intval($value / 1000));
		
		return $output;
	}
	
	public function delete_replay() {
		if (file_exists($this->get_replay_filepath())) {
			unlink($this->get_replay_filepath());
		}
		if (file_exists($this->get_replay_log_path())) {
			unlink($this->get_replay_log_path());
		}
		$query = "UPDATE ".$this->_table." SET replay = 0 WHERE game_id = '".$this->_game_id."'";
		mysql_query($query);
	}
	
	public function add_replay() {
		$query = "UPDATE ".$this->_table." SET replay = 1 WHERE game_id = '".$this->_game_id."'";
		mysql_query($query);
	}
}

class ReportModule {

	public function ReportModule($is_vip = false) {
		//$this->_is_vip = $is_vip;
		
		if ($is_vip) {
			$this->_table = 'lg_reports_vip';
			$this->_games_table = 'lg_laddervip_games';
		} else {
			$this->_table = 'lg_reports';
			$this->_games_table = 'lg_laddergames';
		}
	}

	public function report_exists($game_id) {
		$query = "SELECT game_id FROM ".$this->_table." WHERE game_id = '".$game_id."'";
		$result = mysql_query($query);
		return (mysql_num_rows($result) > 0);
	}
	
	public function get_opened_reports() {
		$query = "SELECT * FROM ".$this->_table." WHERE status IN ('".Report::STATUS_OPENED."', '".Report::STATUS_BEING_HANDLED."') ORDER BY game_id ASC";
		return mysql_query($query);
	}
	
	public function get_last_reports() {
		$query = "SELECT * FROM ".$this->_table." WHERE status = '".Report::STATUS_REPORT_CLOSED."' ORDER BY close_time DESC LIMIT 25";
		return mysql_query($query);
	}
}
?>