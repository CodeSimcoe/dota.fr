<?php
	class Notification {
	
		public $_id;
		public $_destinator;
		public $_message;
		public $_link;
		public $_notif_time;
		
		public function Notification() {}
		
		public function save() {
			$query = "INSERT INTO lg_notifications (destinator, message, link, notif_time) VALUES ('".$this->_destinator."', '".mysql_real_escape_string($this->_message)."', '".$this->_link."', '".(empty($this->_notif_time) ? time() : $this->_notif_time)."')";
			mysql_query($query) or die(mysql_error());
		}
	}
	
	class NotificationManager {
		
		public static function get_user_notifications($user, $only_new = false) {
			$query = "SELECT * FROM lg_notifications WHERE destinator = '".mysql_real_escape_string($user)."'";
			if ($only_new) {
				$query .= " AND new_notif = 1";
			}
			$query .= " ORDER BY id DESC";
			return mysql_query($query);
		}
		
		public static function update_user_new_status($user, $status) {
			$query = "UPDATE lg_notifications SET new_notif = '".(int)$status."' WHERE destinator = '".mysql_real_escape_string($user)."'";
			mysql_query($query);
		}
		
		public static function remove_notifications($notifications, $user) {
			foreach ($notifications as $notif) {
				$query = "DELETE FROM lg_notifications WHERE id = '".(int)$notif."' AND destinator = '".mysql_real_escape_string($user)."'";
				mysql_query($query);
			}
		}
	}
?>