<?php
/*
TABLE lg_shoutcast (
id MEDIUMINT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
poster VARCHAR( 25 ) NOT NULL ,
team1 INT( 4 ) NOT NULL ,
team2 INT( 4 ) NOT NULL ,
date_shoutcast INT( 10 ) NOT NULL
*/

class Shoutcast {

	private $_id;
	public $_poster;
	public $_team1_id;
	public $_team2_id;
	public $_team1_tag;
	public $_team2_tag;
	public $_date_shoutcast;
	public $_timestamp_shoutcast;
	public $_comment;
	
	public function ShoutCast() {}
	
	public static function get_all() {
		$req = "SELECT c1.tag, c2.tag, s.date_shoutcast, c1.id, c2.id, s.comment, s.id, s.poster
				FROM lg_shoutcast s, lg_clans c1, lg_clans c2
				WHERE s.team1 = c1.id
				AND s.team2 = c2.id
				ORDER BY date_shoutcast DESC";
		$t = mysql_query($req);
		$shouts = array();
		while ($l = mysql_fetch_row($t)) {
			$shout = new Shoutcast();
			$shout->_team1_tag = $l[0];
			$shout->_team2_tag = $l[1];
			$shout->_team1_id = $l[3];
			$shout->_team2_id = $l[4];
			$shout->_timestamp_shoutcast = $l[2];
			$shout->_date_shoutcast = date(Lang::DATE_FORMAT_HOUR, $l[2]);
			$shout->_comment = $l[5];
			$shout->_id = $l[6];
			$shout->_poster = $l[7];
			$shouts[] = $shout;
		}
		return $shouts;
	}
	
	public static function get_next_shoutcasts() {
		$req = "SELECT c1.tag, c2.tag, s.date_shoutcast, c1.id, c2.id, s.comment
				FROM lg_shoutcast s, lg_clans c1, lg_clans c2
				WHERE date_shoutcast > ".(time() - 7200)."
				AND s.team1 = c1.id
				AND s.team2 = c2.id
				ORDER BY date_shoutcast ASC";
		$t = mysql_query($req);
		$shouts = array();
		while ($l = mysql_fetch_row($t)) {
			$shout = new Shoutcast();
			$shout->_team1_tag = $l[0];
			$shout->_team2_tag = $l[1];
			$shout->_team1_id = $l[3];
			$shout->_team2_id = $l[4];
			$shout->_date_shoutcast = date(Lang::DATE_FORMAT_HOUR, $l[2]);
			$shout->_comment = $l[5];
			$shouts[] = $shout;
		}
		return $shouts;
	}
	
	public function get_shoutcast_id() {
		return $this->_id;
	}
}
?>