<?php
abstract class ArghSession {
	
	const COOKIE_USERNAME = 'cook_username';
	const COOKIE_PASSWORD = 'cook_password';
	const COOKIE_LANGUAGE = 'cook_language';
	
	public static function begin() {
		if (!headers_sent()) {
			session_start();
		}
	}
	
	public static function end() {
		session_unset();
		session_destroy();
		setcookie(self::COOKIE_USERNAME);
		setcookie(self::COOKIE_PASSWORD);
		setcookie(self::COOKIE_LANGUAGE);
	}
	
	public static function get_lang() {
		/*
		 * Language
		 *
		 * 1. Session
		 * 2. Cookie
		 * 3. Navigateur
		 */
		/*
		if (isset($_COOKIE[self::COOKIE_LANGUAGE])) {
			return $_COOKIE[self::COOKIE_LANGUAGE];
		}
		*/
		
		if (!empty($_SESSION['lang'])) {
			return $_SESSION['lang'];
		}
		
		if (!empty($_COOKIE[self::COOKIE_LANGUAGE])) {
			return self::get_lang_cookie();
		}
		
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		
		if ($lang == 'fr') {
			return 'frFR';
		} else {
			return 'enUS';
		}
		
		//return 'frFR';
	}
	
	public static function get_flag() {
		return '<img src="img/lang/'.self::get_lang().'/flag.gif" />';
	}
	
	public static function set_lang($lang) {
		$_SESSION['lang'] = $lang;
	}
	
	public static function is_gold() {
		//TODO
		return ($_SESSION['goldmember'] == 1) || self::get_rights() != 0;
	}
	
	public static function has_credits() {
		//TODO
		return (self::is_gold() || self::get_daily_credits() > 0);
	}
	
	public static function load_credits() {
		$query = "SELECT daily_games, weekly_games FROM lg_users WHERE username = '".self::get_username()."'";
		$result = mysql_query($query);
		$credits = mysql_fetch_row($result);
		self::set_daily_credits($credits[0]);
		self::set_weekly_credits($credits[1]);
	}
	
	public static function store_session_vars($sql_result) {
		self::set_password($sql_result->password);
		self::set_username($sql_result->username);
		self::set_bnet($sql_result->bnet);
		self::set_ggc($sql_result->ggc);
		self::set_mail($sql_result->mail);
		self::set_clan($sql_result->clan);
		self::set_clan_rank($sql_result->crank);
		self::set_avatar($sql_result->avatar);
		self::set_joined($sql_result->joined);
		self::set_access($sql_result->access);
		self::set_rights($sql_result->rights);
		self::set_rights_base($sql_result->rights_base);
		self::set_theme($sql_result->theme);
		self::set_banner($sql_result->banner);
		self::set_qauth($sql_result->qauth);
		self::set_voucher($sql_result->voucher);
		self::set_goldmember($sql_result->is_gold);
		self::set_city($sql_result->city);
		self::set_country($sql_result->country);
		self::set_birthdate($sql_result->birth);
		self::set_lang($sql_result->lang);
		self::set_vip_ban($sql_result->vip_ban);
		self::set_rgc($sql_result->rgc_account);
	}
	
	public static function set_goldmember($gold) {
		$_SESSION['goldmember'] = $gold;
	}
	
	public static function get_daily_credits() {
		return $_SESSION['daily_credits'];
	}
	
	public static function get_weekly_credits() {
		return $_SESSION['weekly_credits'];
	}
	
	public static function set_daily_credits($daily_credits) {
		$_SESSION['daily_credits'] = $daily_credits;
	}
	
	public static function set_weekly_credits($weekly_credits) {
		$_SESSION['weekly_credits'] = $weekly_credits;
	}
	
	public static function get_username_cookie() {
		return $_COOKIE[self::COOKIE_USERNAME];
	}
	
	public static function get_password_cookie() {
		return $_COOKIE[self::COOKIE_PASSWORD];
	}
	
	public static function get_lang_cookie() {
		return $_COOKIE[self::COOKIE_LANGUAGE];
	}
	
	public static function is_logged() {
		return !empty($_SESSION['username']) ? true : false;
	}
	
	public static function is_cookie_set() {
		return !empty($_COOKIE[self::COOKIE_PASSWORD]) ? true : false;
	}
	
	public static function get_username() {
		return $_SESSION['username'];
	}
	
	public static function get_displayed_username() {
		return self::get_username();
	}
	
	public static function set_username($username) {
		$_SESSION['username'] = $username;
	}
	
	public static function get_city() {
		return $_SESSION['city'];
	}
	
	public static function get_password() {
		return $_SESSION['password'];
	}
	
	public static function set_password($password) {
		$_SESSION['password'] = $password;
	}
	
	public static function is_theme_set() {
		return isset($_SESSION['theme']) ? true : false;
	}

	public static function set_country($country) {
		$_SESSION['country'] = $country;
	}
	
	public static function get_country() {
		return $_SESSION['country'];
	}
	
	public static function set_birthdate($birthdate) {
		$_SESSION['birth'] = $birthdate;
	}
	
	public static function get_birthdate() {
		return $_SESSION['birth'];
	}
	
	public static function get_theme() {
		return $_SESSION['theme'];
	}
	
	public static function get_banner() {
		return $_SESSION['banner'];
	}
	
	public static function get_clan() {
		return $_SESSION['clan'];
	}
	
	public static function set_clan($clan) {
		$_SESSION['clan'] = $clan;
	}
	
	public static function get_clan_rank() {
		return $_SESSION['crank'];
	}
	
	public static function get_access() {
		return $_SESSION['access'];
	}
	
	public static function get_rights() {
		return $_SESSION['rights'];
	}

	public static function get_rights_base() {
		return $_SESSION['rights_base'];
	}

	public static function is_vouched() {
		$in_vouchlist = ($_SESSION['vouched'] == 1) ? true : false;
		
		if ($in_vouchlist) {
			return true;
		} else {
			if (self::get_vip_ban() == 1) {
				return false;
			} else {
				return self::is_gold() && self::get_xp() >= 1700;
			}
		}
	}
	
	public static function set_bnet($bnet) {
		$_SESSION['bnet'] = $bnet;
	}
	
	public static function get_bnet() {
		return $_SESSION['bnet'];
	}
	
	public static function set_rgc($bnet) {
		$_SESSION['rgc'] = $bnet;
	}
	
	public static function get_rgc() {
		return $_SESSION['rgc'];
	}
	
	public static function set_ggc($ggc) {
		$_SESSION['ggc'] = $ggc;
	}
	
	public static function get_ggc() {
		return $_SESSION['ggc'];
	}
	
	public static function set_city($city) {
		$_SESSION['city'] = $city;
	}
	
	public static function set_mobile($mobile) {
		$_SESSION['mobile'] = $mobile;
	}
	
	public static function get_mobile() {
		return $_SESSION['mobile'];
	}
	/**
	 * Alias for set_ggc
	 * @return void
	 */
	public static function set_garena($garena) {
		self::set_ggc($garena);
	}
	
	/**
	 * Alias for get_ggc
	 * @return string
	 */
	public static function get_garena() {
		return self::get_ggc();
	}
	
	public static function get_mail() {
		return $_SESSION['mail'];
	}
	
	public static function set_mail($mail) {
		$_SESSION['mail'] = $mail;
	}
	
	public static function set_clan_rank($crank) {
		$_SESSION['crank'] = $crank;
	}
	
	public static function set_avatar($avatar) {
		$_SESSION['avatar'] = $avatar;
	}
	
	public static function set_joined($joined) {
		$_SESSION['joined'] = $joined;
	}
	
	public static function set_access($access) {
		$_SESSION['access'] = $access;
	}
	
	public static function set_rights($rights) {
		$_SESSION['rights'] = $rights;
	}

	public static function set_rights_base($rights) {
		$_SESSION['rights_base'] = $rights;
	}

	public static function set_theme($theme) {
		$_SESSION['theme'] = $theme;
	}
	
	public static function set_banner($banner) {
		$_SESSION['banner'] = $banner;
	}
	
	public static function set_qauth($qauth) {
		$_SESSION['qauth'] = $qauth;
	}
	
	public static function get_qauth() {
		return $_SESSION['qauth'];
	}
	
	public static function set_voucher($voucher) {
		$_SESSION['voucher'] = $voucher;
	}
	
	public static function set_ladder_admin($ladder_admin) {
		$_SESSION['ladder_admin'] = $ladder_admin;
	}
	
	public static function set_vouched($vouched) {
		$_SESSION['vouched'] = $vouched;
	}
	
	public static function get_vouched() {
		return $_SESSION['vouched'];
	}
	
	public static function set_laddervip_admin($laddervip_admin) {
		$_SESSION['laddervip_admin'] = $laddervip_admin;
	}
	
	public static function set_league_admin($league_admin) {
		$_SESSION['league_admin'] = $league_admin;
	}
	
	public static function get_league_admin() {
		return $_SESSION['league_admin'];
	}
	
	public static function is_ladder_admin() {
		return ($_SESSION['ladder_admin'] != 0) ? true : false;
	}
	
	public static function is_voucher() {
		return ($_SESSION['voucher'] > 0) ? true : false;
	}
	
	/**
	 * Display ADS ?
	 * @return boolean
	*/
	public static function display_ad() {
		//return (self::get_username() == 'ThunderBolt_') ? true : false;
		return (!self::is_gold() || self::get_username() == 'ThunderBolt_') ? true : false;
	}
	
	public static function display_google_ad() {
		return self::display_ad();
	}
	
	public static function set_xp($xp) {
		$_SESSION['xp'] = $xp;
	}
	
	public static function set_xp_vip($xp_vip) {
		$_SESSION['xp_vip'] = $xp_vip;
	}
	
	public static function set_gold($gold) {
		$_SESSION['gold'] = $gold;
	}
	
	public static function get_xp() {
		return $_SESSION['xp'];
	}
	
	public static function get_xp_vip() {
		return $_SESSION['xp_vip'];
	}
	
	public static function set_vip_ban($vip_ban) {
		$_SESSION['vip_ban'] = $vip_ban;
	}
	
	public static function get_vip_ban() {
		return $_SESSION['vip_ban'];
	}
	
	public static function get_gold() {
		return $_SESSION['gold'];
	}
	
	public static function get_avatar() {
		return $_SESSION['avatar'];
	}
	
	/**
	 * Recupere gold / xp / xp_vip depuis la bdd et stocke en session
	 * @return void
	 */
	public static function set_gold_and_xp() {
		$req = "SELECT gold, pts, pts_vip, vip_ban FROM lg_users WHERE username = '".self::get_username()."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		self::set_gold($l[0]);
		self::set_xp($l[1]);
		self::set_xp_vip($l[2]);
		self::set_vip_ban($l[3]);
	}
	
	//@deprecated, see "exit_if_not_rights()"
	public static function exit_if_not($minimum_access_required) {
		if (!self::is_logged() || self::get_access() < $minimum_access_required) {
			exit(Lang::AUTHORIZATION_REQUIRED);
		}
	}
	
	public static function exit_if_not_logged() {
		if (!self::is_logged()) {
			exit(Lang::LOGGING_REQUIRED);
		}
	}
	
	public static function is_garena_account_set() {
		return (strlen(trim(self::get_garena())) >= 2) ? true : false;
	}
	
	/**
	 * Check Rights
	 * @return boolean
	 */
	public static function is_rights($rights_mode) {
		if (!self::is_logged()) return false;
		if (self::get_rights() == 0) return false;
		if (is_array($rights_mode)) {
			$out = false;
			foreach ($rights_mode as $rights) {
				$out = $out | ((self::get_rights() & $rights) == $rights);
			}
			return $out;
		} else {
			return ((self::get_rights() & $rights_mode) == $rights_mode);
		}
	}
	
	/**
	 * Exit if not rights
	 */
	public static function exit_if_not_rights($rights_mode) {
		if (!self::is_rights($rights_mode)) exit(ArghPanel::str_begin_tag(Lang::ERROR).Lang::AUTHORIZATION_REQUIRED.ArghPanel::str_end_tag());
	}

}
?>