<?php
	class Screenshot {
	
		const SCREENSHOT_FOLDER = '/home/www/ligue/screenshots/';
		const SCREENSHOT_URL_FOLDER = '/ligue/screenshots/';
		const SCREENSHOT_EXTENSION = '.jpg';
		const THUMBNAIL_SUFFIX = '_small';
		const THUMBNAIL_WIDTH = 170;
		const MEDIUM_THUMBNAIL_WIDTH = 590;
		
		public $_id;
		public $_name;
		public $_uploader;
		public $_date_upload;
		public $_keywords;
		public $_validated;
		public $_heroes;
		public $_ratings;
		public $_rating;
		public $_messages;
		
		public function Screenshot() {}
		
		public function build_from_sql_resource($sql_resource) {
			$this->_id = $sql_resource->id;
			$this->_name = $sql_resource->name;
			$this->_uploader = $sql_resource->uploader;
			$this->_date_upload = $sql_resource->date_upload;
			$this->_rating = $sql_resource->rating;
		}
		
		public function save_screenshot() {
			$query = "INSERT INTO lg_screenshots (name, uploader, date_upload)
						VALUES ('".mysql_real_escape_string($this->_name)."', '".mysql_real_escape_string($this->_uploader)."', '".time()."')";
			mysql_query($query);
			
			$id = mysql_insert_id();
			
			if (is_array($this->_heroes)) {
				foreach ($this->_heroes as $hero) {
					$query = "INSERT INTO lg_screenshots_heroes (screenshot_id, hero) VALUES ('".$id."', '".mysql_real_escape_string($hero)."')";
					mysql_query($query);
				}
			}
			
			if (is_array($this->_keywords)) {
				foreach ($this->_keywords as $keyword) {
					$query = "INSERT INTO lg_screenshots_keywords (screenshot_id, keyword) VALUES ('".$id."', '".mysql_real_escape_string($keyword)."')";
					mysql_query($query);
				}
			}
			
			return $id;
		}
		
		public function validate_screenshot() {
			$query = "UPDATE lg_screenshots SET name = '".$this->_name."', validated = 1 WHERE id = '".(int)$this->_id."'";
			mysql_query($query);
			
			$this->delete_references();
			
			if (is_array($this->_heroes)) {
				foreach ($this->_heroes as $hero) {
					$query = "INSERT INTO lg_screenshots_heroes (screenshot_id, hero) VALUES ('".(int)$this->_id."', '".mysql_real_escape_string($hero)."')";
					mysql_query($query);
				}
			}
			
			if (is_array($this->_keywords)) {
				foreach ($this->_keywords as $keyword) {
					$query = "INSERT INTO lg_screenshots_keywords (screenshot_id, keyword) VALUES ('".(int)$this->_id."', '".mysql_real_escape_string($keyword)."')";
					mysql_query($query);
				}
			}
			
			return $id;
		}
		
		public function load_screenshot($id, $filter_validated = false) {
			$query = "SELECT * FROM lg_screenshots WHERE id = '".(int)$id."'";
			if ($filter_validated) {
				$query .= " AND validated = 1";
			}
			$result = mysql_query($query);
			if (mysql_num_rows($result) == 1) {
				$sql = mysql_fetch_object($result);
				$this->_id = $sql->id;
				$this->_name = $sql->name;
				$this->_uploader = $sql->uploader;
				$this->_date_upload = $sql->date_upload;
				$this->_validated = $sql->validated;
				
				$this->get_screenshot_heroes();
				$this->get_screenshot_keywords();
				$this->get_ratings();
				//$this->get_messages();@deprecated
				
				return true;
			} else {
				return false;
			}
		}
		
		public function delete_screenshot($physical = true) {
			$query = "DELETE FROM lg_screenshots WHERE id = '".(int)$this->_id."'";
			mysql_query($query);
			
			$this->delete_references();
			
			if ($physical) {
				unlink($this->get_screenshot_filepath());
				unlink($this->get_thumbnail_filepath());
			}
		}
		
		private function delete_references() {
			$query = "DELETE FROM lg_screenshots_heroes WHERE screenshot_id = '".(int)$this->_id."'";
			mysql_query($query);
			
			$query = "DELETE FROM lg_screenshots_keywords WHERE screenshot_id = '".(int)$this->_id."'";
			mysql_query($query);
			
			$query = "DELETE FROM lg_screenshots_messages WHERE screenshot_id = '".(int)$this->_id."'";
			mysql_query($query);
			
			$query = "DELETE FROM lg_screenshots_ratings WHERE screenshot_id = '".(int)$this->_id."'";
			mysql_query($query);
		}
		
		public function get_screenshot_filepath() {
			return self::SCREENSHOT_FOLDER.$this->_id.self::SCREENSHOT_EXTENSION;
		}
		
		public function get_screenshot_url() {
			return self::SCREENSHOT_URL_FOLDER.$this->_id.self::SCREENSHOT_EXTENSION;
		}

		public function get_thumbnail_filepath() {
			return self::SCREENSHOT_FOLDER.$this->_id.self::THUMBNAIL_SUFFIX.self::SCREENSHOT_EXTENSION;
		}
		
		public function get_thumbnail_url() {
			return self::SCREENSHOT_URL_FOLDER.$this->_id.self::THUMBNAIL_SUFFIX.self::SCREENSHOT_EXTENSION;
		}
		
		public function rating_to_stars() {
			$rating = $this->_rating;
			
			if ($rating <= 0) {
				$stars = array(0, 0, 0, 0, 0);
			} elseif ($rating > 0.25 && $rating <= 0.75) {
				$stars = array(-1, 0, 0, 0, 0);
			} elseif ($rating > 0.75 && $rating <= 1.25) {
				$stars = array(1, 0, 0, 0, 0);
			} elseif ($rating > 1.25 && $rating <= 1.75) {
				$stars = array(1, -1, 0, 0, 0);
			} elseif ($rating > 1.75 && $rating <= 2.25) {
				$stars = array(1, 1, 0, 0, 0);
			} elseif ($rating > 2.25 && $rating <= 2.75) {
				$stars = array(1, 1, -1, 0, 0);
			} elseif ($rating > 2.75 && $rating <= 3.25) {
				$stars = array(1, 1, 1, 0, 0);
			} elseif ($rating > 3.25 && $rating <= 3.75) {
				$stars = array(1, 1, 1, -1, 0);
			} elseif ($rating > 3.75 && $rating <= 4.25) {
				$stars = array(1, 1, 1, 1, 0);
			} elseif ($rating > 4.25 && $rating <= 4.75) {
				$stars = array(1, 1, 1, 1, -1);
			} else {
				$stars = array(1, 1, 1, 1, 1);
			}
			
			$return = '';
			
			foreach ($stars as $star) {
				switch ($star) {
					case 1:
						$return .= '<img src="img/star_on.png" alt="" />';
						break;
					case 0:
						$return .= '<img src="img/star_off.png" alt="" />';
						break;
					case -1:
						$return .= '<img src="img/star_half.png" alt="" />';
						break;
				}
			}
			
			return $return;
		}
		
		public function display_thumbnail() {
			$file = $this->get_screenshot_filepath();
			list($width, $height, $type, $attr) = getimagesize($file);
			
			echo '<a href="?f=screenshot&id='.$this->_id.'"><img src="'.$this->get_thumbnail_url().'" alt="'.$this->get_screenshot_url().'" width="'.self::THUMBNAIL_WIDTH.'" height="'.((int)(self::THUMBNAIL_WIDTH * $height / $width)).'" /></a>';
		}
		
		public function display_thumbnail_pending() {
			$file = $this->get_screenshot_filepath();
			list($width, $height, $type, $attr) = getimagesize($file);
			
			echo '<a href="'.$this->get_screenshot_url().'"><img src="'.$this->get_thumbnail_url().'" alt="'.$this->get_screenshot_url().'" width="'.self::THUMBNAIL_WIDTH.'" height="'.((int)(self::THUMBNAIL_WIDTH * $height / $width)).'" /></a>';
		}
		
		public function display_medium_thumbnail() {
			$file = $this->get_screenshot_filepath();
			list($width, $height, $type, $attr) = getimagesize($file);
			
			echo '<a href="'.$this->get_screenshot_url().'"><img src="'.$this->get_screenshot_url().'" alt="'.$this->get_screenshot_url().'" width="'.self::MEDIUM_THUMBNAIL_WIDTH.'" height="'.((int)(self::MEDIUM_THUMBNAIL_WIDTH * $height / $width)).'" /></a>';
		}
		
		public function get_screenshot_heroes() {
			$query = "SELECT hero FROM lg_screenshots_heroes WHERE screenshot_id = '".(int)$this->_id."'";
			$result = mysql_query($query);
			
			$heroes = array();
			
			while ($sql_hero = mysql_fetch_row($result)) {
				$heroes[] = $sql_hero[0];
			}
			
			$this->_heroes = $heroes;
		}
		
		public function get_screenshot_keywords() {
			$query = "SELECT keyword FROM lg_screenshots_keywords WHERE screenshot_id = '".(int)$this->_id."'";
			$result = mysql_query($query);
			
			$keywords = array();
			
			while ($sql_keyword = mysql_fetch_row($result)) {
				$keywords[] = $sql_keyword[0];
			}
			
			$this->_keywords = $keywords;
		}
		
		public function get_ratings() {
			$query = "SELECT AVG(rating), COUNT(rating) FROM lg_screenshots_ratings WHERE screenshot_id = '".(int)$this->_id."' GROUP BY screenshot_id";
			$result = mysql_query($query);
			
			if (mysql_num_rows($result)) {
				$sql = mysql_fetch_row($result);
				$this->_rating = $sql[0];
				$this->_ratings = $sql[1];
			} else {
				$this->_rating = 0;
				$this->_ratings = 0;
			}
		}
		
		public function user_has_voted($user) {
			$query = "SELECT COUNT(*) FROM lg_screenshots_ratings WHERE screenshot_id = '".(int)$this->_id."' AND username = '".mysql_real_escape_string($user)."'";
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
			
			return ($row[0] == 1) ? true : false;
		}
		
		/*@deprecated
		public function get_messages() {
			$this->_messages = GenericMessage::load_referenced(Tables::SCREENSHOT_MESSAGES, $this->_id);
		}
		*/
	}

	abstract class ScreenshotModule {
		
		public static function get_last_screenshots($limit = 3) {
			$query = "SELECT s.*, AVG(r.rating) as rating FROM lg_screenshots s LEFT JOIN lg_screenshots_ratings r ON s.id = r.screenshot_id WHERE s.validated = 1 GROUP BY s.id ORDER BY s.id DESC LIMIT ".(int)$limit;
			return mysql_query($query);
		}
		
		public static function get_best_screenshots($limit = 3) {
			$query = "SELECT s.*, AVG(r.rating) as rating FROM lg_screenshots s LEFT JOIN lg_screenshots_ratings r ON s.id = r.screenshot_id WHERE s.validated = 1 GROUP BY s.id ORDER BY rating DESC LIMIT ".(int)$limit;
			return mysql_query($query);
		}
		
		public static function get_pending_screenshots() {
			$query = "SELECT * FROM lg_screenshots WHERE validated = 0 ORDER BY id DESC";
			return mysql_query($query);
		}
		
		public static function get_random_screenshots($limit = 3) {
			$query = "SELECT s.*, AVG(r.rating) as rating FROM lg_screenshots s LEFT JOIN lg_screenshots_ratings r ON s.id = r.screenshot_id WHERE s.validated = 1 GROUP BY s.id ORDER BY rand() LIMIT ".(int)$limit;
			return mysql_query($query);
		}
		
		public static function get_screenshot($id) {
			$query = "SELECT * FROM lg_screenshots WHERE id = '".(int)$id."'";
			$result = mysql_query($query);
			if (mysql_num_rows($result)) {
				$sql_screenshot = mysql_fetch_object($result);
				$ss = new Screenshot();
				$ss->_id = $sql_screenshot->id;
				$ss->_name = $sql_screenshot->name;
				$ss->_uploader = $sql_screenshot->uploader;
				$ss->_date_upload = $sql_screenshot->date_upload;
				
				return $ss;
			}
		}
		
		public static function validate_screenshot($id) {
			$query = "UPDATE lg_screenshots SET validated = 1 WHERE id = '".(int)$id."'";
			mysql_query($query);
		}
	}
?>