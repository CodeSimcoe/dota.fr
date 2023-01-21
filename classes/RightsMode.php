<?php
abstract class RightsMode {

	const WEBMASTER = 1;

	const LEAGUE_HEADADMIN = 2;
	const LEAGUE_ADMIN = 4;

	const LADDER_HEADADMIN = 8;
	const LADDER_ADMIN = 16;

	const VIP_HEADADMIN = 32;
	const VIP_ADMIN = 64;
	const VIP_VOUCHER = 128;

	const NEWS_HEADADMIN = 256;
	const NEWS_NEWSER = 512;

	const SHOUTCAST_HEADADMIN = 1024;
	const SHOUTCAST_SHOUTCASTER = 2048;
	
	const GUARDIAN_ADMIN = 4096;
	
	const SCREENSHOTS_ADMIN = 8192;
	
	public static function colorize_rights($role) {
		switch ($role) {
			case Lang::RIGHTS_WEBMASTER:
				return '<span class="lose">'.$role.'</span>';
				
			case Lang::RIGHTS_LEAGUE_HEADADMIN:
			case Lang::RIGHTS_LEAGUE_ADMIN:
				return '<span class="vip">'.$role.'</span>';
				
			case Lang::RIGHTS_LADDER_HEADADMIN:
			case Lang::RIGHTS_LADDER_ADMIN:
			case Lang::RIGHTS_VIP_HEADADMIN:
			case Lang::RIGHTS_VIP_ADMIN:
			case Lang::RIGHTS_VIP_VOUCHER:
				return '<span class="win">'.$role.'</span>';
				
			case Lang::RIGHTS_NEWS_HEADADMIN:
			case Lang::RIGHTS_NEWS_NEWSER:
			case Lang::RIGHTS_SHOUTCAST_HEADADMIN:
			case Lang::RIGHTS_SHOUTCAST_SHOUTCASTER:
				return '<span class="newser">'.$role.'</span>';
			
			case Lang::RIGHTS_NONE:
			default:
				return $role;
		}
	}
	
	public static function colorize_rights_mini_ladder($rights_base) {
		$role = self::get_rights_label($rights_base);
		
		switch ($role) {
			case Lang::RIGHTS_VIP_HEADADMIN:
			case Lang::RIGHTS_VIP_ADMIN:
			case Lang::RIGHTS_VIP_VOUCHER:
				return '<span class="draw">V</span>';
		
			case Lang::RIGHTS_WEBMASTER:
				return '<span class="vip">W</span>';
				
			case Lang::RIGHTS_LEAGUE_HEADADMIN:
			case Lang::RIGHTS_LEAGUE_ADMIN:
				return '<span class="lose">A</span>';
				
			case Lang::RIGHTS_LADDER_HEADADMIN:
			case Lang::RIGHTS_LADDER_ADMIN:
				return '<span class="win">L</span>';
				
			case Lang::RIGHTS_NEWS_HEADADMIN:
			case Lang::RIGHTS_NEWS_NEWSER:
			case Lang::RIGHTS_SHOUTCAST_HEADADMIN:
			case Lang::RIGHTS_SHOUTCAST_SHOUTCASTER:
				return '<span class="newser">N</span>';
			
			case Lang::RIGHTS_NONE:
			default:
				return '';
		}
	}

	public static function get_rights($rights_base) {
		$out = 0;
		switch ($rights_base) {
			case self::WEBMASTER:
				$out = self::WEBMASTER;
				$out |= self::LEAGUE_HEADADMIN;
				$out |= self::LEAGUE_ADMIN;
				$out |= self::LADDER_HEADADMIN;
				$out |= self::LADDER_ADMIN;
				$out |= self::VIP_HEADADMIN;
				$out |= self::VIP_ADMIN;
				$out |= self::VIP_VOUCHER;
				$out |= self::NEWS_HEADADMIN;
				$out |= self::NEWS_NEWSER;
				$out |= self::SHOUTCAST_HEADADMIN;
				$out |= self::SHOUTCAST_SHOUTCASTER;
				$out |= self::SCREENSHOTS_ADMIN;
				break;
			case self::LEAGUE_HEADADMIN:
				$out = self::LEAGUE_HEADADMIN;
				$out |= self::LEAGUE_ADMIN;
				$out |= self::NEWS_NEWSER;
				break;
			case self::LEAGUE_ADMIN:
				$out = self::LEAGUE_ADMIN;
				$out |= self::NEWS_NEWSER;
				break;
			case self::LADDER_HEADADMIN:
				$out = self::LADDER_HEADADMIN;
				$out |= self::LADDER_ADMIN;
				$out |= self::NEWS_NEWSER;
				break;
			case self::LADDER_ADMIN:
				$out = self::LADDER_ADMIN;
				break;
			case self::VIP_HEADADMIN:
				$out = self::VIP_HEADADMIN;
				$out |= self::VIP_ADMIN;
				$out |= self::VIP_VOUCHER;
				$out |= self::NEWS_NEWSER;
				break;
			case self::VIP_ADMIN:
				$out = self::VIP_ADMIN;
				$out |= self::VIP_VOUCHER;
				break;
			case self::VIP_VOUCHER:
				$out = self::VIP_VOUCHER;
				break;
			case self::NEWS_HEADADMIN:
				$out = self::NEWS_HEADADMIN;
				$out |= self::NEWS_NEWSER;
				break;
			case self::NEWS_NEWSER:
				$out = self::NEWS_NEWSER;
				break;
			case self::SHOUTCAST_HEADADMIN:
				$out = self::SHOUTCAST_HEADADMIN;
				$out |= self::SHOUTCAST_SHOUTCASTER;
				$out |= self::NEWS_NEWSER;
				break;
			case self::SHOUTCAST_SHOUTCASTER:
				$out = self::SHOUTCAST_SHOUTCASTER;
				$out |= self::NEWS_NEWSER;
				break;
			case self::SCREENSHOTS_ADMIN:
				$out = self::SCREENSHOTS_ADMIN;
		}
		return $out;
	}

	public static function get_rights_label($rights_base) {
		$rank = '';
		switch ($rights_base) {
			case self::WEBMASTER:
				$rank = Lang::RIGHTS_WEBMASTER;
				break;
			case self::LEAGUE_HEADADMIN:
				$rank = Lang::RIGHTS_LEAGUE_HEADADMIN;
				break;
			case self::LEAGUE_ADMIN:
				$rank = Lang::RIGHTS_LEAGUE_ADMIN;
				break;
			case self::LADDER_HEADADMIN:
				$rank = Lang::RIGHTS_LADDER_HEADADMIN;
				break;
			case self::LADDER_ADMIN:
				$rank = Lang::RIGHTS_LADDER_ADMIN;
				break;
			case self::VIP_HEADADMIN:
				$rank = Lang::RIGHTS_VIP_HEADADMIN;
				break;
			case self::VIP_ADMIN:
				$rank = Lang::RIGHTS_VIP_ADMIN;
				break;
			case self::VIP_VOUCHER:
				$rank = Lang::RIGHTS_VIP_VOUCHER;
				break;
			case self::NEWS_HEADADMIN:
				$rank = Lang::RIGHTS_NEWS_HEADADMIN;
				break;
			case self::NEWS_NEWSER:
				$rank = Lang::RIGHTS_NEWS_NEWSER;
				break;
			case self::SHOUTCAST_HEADADMIN:
				$rank = Lang::RIGHTS_SHOUTCAST_HEADADMIN;
				break;
			case self::SHOUTCAST_SHOUTCASTER:
				$rank = Lang::RIGHTS_SHOUTCAST_SHOUTCASTER;
				break;
			case self::GUARDIAN_ADMIN:
				$rank = Lang::RIGHTS_LADDERGUARDIAN_ADMIN;
				break;
			case self::SCREENSHOTS_ADMIN:
				$rank = Lang::RIGHTS_SCREENSHOTS_ADMIN;
				break;
		}
		return $rank;
	}

}

?>