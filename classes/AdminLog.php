<?php
class AdminLog {
	
	public $_who;
	public $_when;
	public $_what;
	public $_type;
	
	const TYPE_MAIN = 0;
	const TYPE_LEAGUE = 1;
	const TYPE_LADDER = 2;
	const TYPE_NEWS = 3;
	const TYPE_ROUTINES = 4;
	const TYPE_PAYMENT = 5;
	const TYPE_ADMIN = 6;
	
	public function AdminLog($what, $type = null, $who = null, $when = null) {
		$this->_what = $what;
		$this->_type = empty($type) ? self::TYPE_MAIN : $type;
		$this->_who = empty($who) ? ArghSession::get_username() : $who;
		$this->_when = empty($when) ? time() : $when;
	}
	
	public function save_log() {
		$query = "
				INSERT INTO
					lg_adminlog (
						qui,
						log_type,
						quand,
						quoi
					)
				VALUES (
					'".mysql_real_escape_string($this->_who)."',
					'".(int)$this->_type."',
					'".(int)$this->_when."',
					'".mysql_real_escape_string($this->_what)."'
				)";
		mysql_query($query);
	}
}
?>