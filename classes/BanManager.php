<?php
	abstract class BanManager {
	
		const TYPE_BAN = 'ban';
		
		public static function ban($user, $length, $reason, $admin = null) {
		
			if (empty($admin)) {
				$admin = ArghSession::get_username();
			} else {
				$admin = mysql_real_escape_string($admin);
			}
		
			$ins = "INSERT INTO lg_ladderbans (qui, par_qui, quand, duree, raison)
					VALUES ('".mysql_real_escape_string($user)."', '".$admin."', '".time()."', '".(float)$length."', '".mysql_real_escape_string($reason)."')";
			mysql_query($ins);
			
			$ins = "INSERT INTO lg_ladderbans_follow (username, motif, `force`, quand, admin, type)
					VALUES ('".mysql_real_escape_string($user)."', '".mysql_real_escape_string($reason)."', '".(float)$length."', '".time()."', '".$admin."', '".self::TYPE_BAN."')";
			mysql_query($ins);
			
			//Kick de la waiting list
			//Recuperation contenu
			$content = file(CacheManager::LADDER_PLAYERLIST);
			
			//Ouverture fichier
			$handle = fopen(CacheManager::LADDER_PLAYERLIST, 'w+');
			
			//Reecriture
			foreach ($content as $val) {
				$line = explode(';', $val);
				if ($line[0] != $user) {
					fwrite($handle, $val);
				}
			}
		}
		
		public static function unban($ban_id) {
			mysql_query("DELETE FROM lg_ladderbans WHERE id = '".(int)$ban_id."'");
		}
	}
?>