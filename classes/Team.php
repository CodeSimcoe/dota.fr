<?php
class Team {
	
	public $_id;
	public $_name;
	public $_tag;
	public $_division;
	public $_players = array();
	public $_date_proposals = array();
	public $_motd;
	public $_logo;
	
	public function Team($id, $name = null, $tag = null) {
		$this->_id = $id;
		$this->_name = $name;
		$this->_tag = $tag;
	}
	
	public function load_infos() {
		$req = "SELECT name, tag, divi, motd, logo FROM lg_clans WHERE id = '".$this->_id."'";
		$t = mysql_query($req);
		if (mysql_num_rows($t)) {
			$l = mysql_fetch_row($t);
			$this->_name = $l[0];
			$this->_tag = $l[1];
			$this->_division = $l[2];
			$this->_motd = stripslashes($l[3]);
			$this->_logo = $l[4];
		}
	}
	
	public function load_players() {
		$this->_players = array();
		$req = "SELECT username FROM lg_users WHERE clan = '".$this->_id."' ORDER BY username ASC";
		$t = mysql_query($req);
		while ($l = mysql_fetch_row($t)) {
			$this->_players[] = $l[0];
		}
	}
	
	public function load_date_proposals() {
		$this->_date_proposals = array();
		$req = "SELECT id, playday, date_proposal FROM lg_clan_dates WHERE clan = '".$this->_id."' ORDER BY playday ASC, date_proposal ASC";
		$t = mysql_query($req);
		while ($l = mysql_fetch_row($t)) {
		
			//Players availabilities
			$availabilities = array();
			$sreq = "SELECT username, response FROM lg_players_availabilities WHERE proposal_id = '".$l[0]."'";
			$st = mysql_query($sreq);
			while ($sl = mysql_fetch_row($st)) {
				$availabilities[$sl[0]] = $sl[1];
			}
		
			$this->_date_proposals[$l[0]] = array($l[1], $l[2], $availabilities);
		}
	}
	
	public static function update_availability($proposal_id, $response, $username, $user_clan) {
		//Let's be safe
		$proposal_id = (int) $proposal_id;
		$username = mysql_real_escape_string($username);
		$response = (int) $response;
		
		//We can only modify our own availabilities, so check that everything is ok
		if ($user_clan != self::get_clan_from_proposal($proposal_id)) return;
		
		$del = "DELETE FROM lg_players_availabilities WHERE username = '".$username."' AND proposal_id = '".$proposal_id."'";
		mysql_query($del);
		
		$ins = "INSERT INTO lg_players_availabilities (username, response, proposal_id) VALUES ('".$username."', '".$response."', '".$proposal_id."')";
		mysql_query($ins);
	}
	
	private static function get_clan_from_proposal($id) {
		$req = "SELECT clan FROM lg_clan_dates WHERE id = '".$id."'";
		$t = mysql_query($req);
		if (mysql_num_rows($t)) {
			$l = mysql_fetch_row($t);
			return $l[0];
		}
	}
	
	public function create_date_proposal($playday, $time) {
		$playday = (int) $playday;
		$time = (int) $time;
		
		$ins = "INSERT INTO lg_clan_dates (clan, playday, date_proposal) VALUES ('".$this->_id."', '".$playday."', '".$time."')";
		mysql_query($ins);
	}
	
	public function delete_date_proposal($proposal_id, $user_clan) {
		$proposal_id = (int) $proposal_id;
		
		//We can only modify our own stuff
		if ($user_clan != self::get_clan_from_proposal($proposal_id)) return;
		
		$del = "DELETE FROM lg_clan_dates WHERE id = '".$proposal_id."'";
		mysql_query($del);
		
		$del = "DELETE FROM lg_players_availabilities WHERE proposal_id = '".$proposal_id."'";
		mysql_query($del);
	}
	
	public function update_motd($new_motd) {
		$query = "UPDATE lg_clans SET motd = '".mysql_real_escape_string($new_motd)."' WHERE id = '".$this->_id."'";
		mysql_query($query);
	}
	
	public static function get_teams_from_division($division) {
		$teams = array();
		
		$req = "SELECT id, name, tag FROM lg_clans WHERE divi = '".$division."' ORDER BY name ASC";
		$t = mysql_query($req);
		while ($l = mysql_fetch_row($t)) {
			$teams[] = new Team($l[0], $l[1], $l[2]);
		}
		
		return $teams;
	}
}
?>