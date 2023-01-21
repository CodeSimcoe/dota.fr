<?php
	abstract class Heroes {
	
		public static function get_heroes() {
			$heroes = array();
			
			$result = mysql_query('SELECT hero FROM lg_heroes ORDER BY hero ASC');
			while ($row = mysql_fetch_row($result)) {
				$heroes[] = $row[0];
			}
			
			return $heroes;
		}
		
		public static function get_sorted_heroes() {
			$heroes = array();
			
			$result = mysql_query('SELECT hero, main_attribute, affiliation FROM lg_heroes ORDER BY main_attribute ASC, affiliation ASC, hero ASC');
			while ($row = mysql_fetch_row($result)) {
				$heroes[$row[1]][$row[2]][] = $row[0];
			}
			
			return $heroes;
		}
	}
?>