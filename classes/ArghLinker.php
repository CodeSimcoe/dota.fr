<?php
abstract class ArghLinker {
	
	public static function get_profile_link($username) {
		return '<a href="?f=player_profile&player='.$username.'">'.$username.'</a>';
	}
	
	public static function get_team_link($team, $team_id) {
		return '<a href="?f=team_profile&id="'.$team_id.'>'.$team.'</a>';
	}
}
?>