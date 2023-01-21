<?php
	function attr_($x, $y) {
		return ($x == $y) ? ' selected="selected"' : '';
	}
	
	//Page debut commentaires
	function getStart($posts) {
		return ($posts == 0) ? 1 : ($posts % 10 == 0) ? $posts - 9 : $posts - $posts%10 + 1;
	}
	
	function alinea() {
		return '&nbsp;&nbsp;';
	}
	
	function shortenTitle($str) {
		return (strlen($str) > 32) ? substr($str, 0, 32).'...' : $str;
	}
	
	//Recocher les checkbox après un POST
	function check_box_array($name, $array_name) {
		return (array_key_exists($array_name, $_POST) && in_array($name, $_POST[$array_name])) ? ' checked="checked"' : '';
	}
	
	function check_box_array2($name, $array) {
		return in_array($name, $array) ? ' checked="checked"' : '';
	}
	
	function check_box($name) {
		return (isset($_POST[$name])) ? ' checked="checked"' : '';
	}
	
	//Trim recursif d'un array
	function trim_array($totrim) {
		if (is_array($totrim)) {
			$totrim = array_map("trim_array", $totrim);
		} else {
			$totrim = trim($totrim);
		}
		return $totrim;
	}
	
	function get_user_lang($user) {
		$query = "SELECT lang FROM lg_users WHERE username = '".mysql_real_escape_string($user)."'";
		$result = mysql_query($query);
		$sql = mysql_fetch_row($result);
		return $sql[0];
	}
	
	function get_user_mail($user) {
		$query = "SELECT mail FROM lg_users WHERE username = '".mysql_real_escape_string($user)."'";
		$result = mysql_query($query);
		$sql = mysql_fetch_row($result);
		return $sql[0];
	}
	
	/*
	//classes/News.php
	function get_news_by_category($categ, $nb_news = 7) {
		$req = "SELECT * FROM lg_newsmod WHERE afficher = 1 AND categorie = '".$categ."' ORDER BY daten DESC LIMIT 0,".$nb_news;
		$t = mysql_query($req);
		while ($l = mysql_fetch_object($t)) {
			$posts = mysql_num_rows(mysql_query("SELECT * FROM lg_comment WHERE news_id = '".$l->id."'"));
			//echo alinea().' '.date("d/m", $l->daten).' <a href="?f=news&amp;id='.$l->id.'">'.shortenTitle(stripslashes($l->titre)).'</a> (<a href="?f=news&amp;id='.$l->id.'&amp;start='.getStart($posts).'#comment">'.$posts.'</a>)<br />';
			echo alinea().' '.date(Lang::DATE_FORMAT_DAY_MONTH_ONLY, $l->daten).' <a href="?f=news&amp;id='.$l->id.'">'.shortenTitle(stripslashes($l->titre)).'</a> (<a href="?f=news&amp;id='.$l->id.'#comment">'.$posts.'</a>)<br />';
		}
	}
	*/
	
	function getLadderRank($user, $vip = false) {
		if ($vip) {
			//$req = "SELECT u.username
			//		FROM lg_users u, lg_laddervip_vouchlist v
			//		WHERE u.username = v.username
			//		ORDER BY u.pts_vip DESC";
			$req = "SELECT username FROM lg_laddervip_players WHERE played > 0 ORDER BY xp DESC, wins / loses DESC";
		} else {
			$req = "SELECT username FROM lg_users u ORDER BY pts DESC";
		}
		
		$t = mysql_query($req);
		$i = 1;
		while ($l = mysql_fetch_row($t)) {
			if ($l[0] == $user) {
				return $i;
			}
			$i++;
		}
	}
	
	/*
	function getGoldXP() {
		$req = "SELECT gold, pts, pts_vip FROM lg_users WHERE username = '".$_SESSION['username']."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return array('gold' => $l[0], 'xp' => $l[1], 'xp_vip' => $l[2]);
	}
	*/
	
	//Ban terminé ?
	function isFinished($start, $length) {
		if ($length == 0) {
			return false;
		} else {
			//Conversion jours en secondes - 1 jour => 86400 secondes
			$length *= 86400;
			
			//Fin du ban
			$end = $start + (int)$length;
			
			//Temps restant en secondes
			$remain = $end - time();
			
			return ($remain <= 0);
		}
	}
	
	function remainingTime($start, $length) {
		if ($length == 0) {
			return Lang::UNLIMITED;
		} else {
			//Conversion jours en secondes - 1 jour => 86400 secondes
			$length *= 86400;
			
			//Fin du ban
			$end = $start + (int)$length;
			
			//Temps restant en secondes
			$remain = $end - time();
			
			//Temps restant formaté
			return transf($remain);
		}
	}
	
    function transf($time) {
	    if ($time >= 86400) {
		    $jour = floor($time / 86400);
		    $reste = $time % 86400;
		    $heure = floor($reste / 3600);
		    $reste = $reste % 3600;
		    $minute = floor($reste / 60);
		    $seconde = $reste % 60;
		    $result = $jour.Lang::DAY_LETTER.' '.$heure.Lang::HOUR_LETTER/*.' '.$minute.Lang::MINUTE_LETTER.' '.$seconde.Lang::SECOND_LETTER*/;
	    } elseif ($time < 86400 && $time >= 3600) {
		    $heure = floor($time / 3600);
		    $reste = $time % 3600;
		    $minute = floor($reste / 60);
		    $seconde = $reste % 60;
		    $result = $heure.Lang::HOUR_LETTER.' '.$minute.Lang::MINUTE_LETTER/*.' '.$seconde.Lang::SECOND_LETTER*/;
	    } elseif ($time < 3600 && $time >= 60) {
		    $minute = floor($time / 60);
		    $seconde = $time % 60;
		    $result = $minute.Lang::MINUTE_LETTER/*.' '.$seconde.Lang::SECOND_LETTER*/;
	    } elseif ($time < 60 && $time > 0) {
			$result = $time.Lang::SECOND_LETTER;
	    } else {
	    	$result = 0;
	    }
	    return $result;
    }
	
	function addGold($player, $amount, $info = null) {
		mysql_query("UPDATE lg_users SET gold = gold + ".$amount." WHERE username = '".mysql_real_escape_string($player)."'");
		//mysql_query("INSERT INTO lg_goldlog (qui, combien, quoi, quand) VALUES ('".$player."', '".$amount."', '".$info."', '".time()."')");
	}
	
	function getRank($player) {
		$req = "SELECT username FROM lg_users ORDER BY pts DESC";
		$t = mysql_query($req);
		$k=1;
		while ($l = mysql_fetch_row($t) && $l[0] != $player) $k++;
		return $k;
	}
	
	function selfURL() {
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
	}
	function strleft($s1, $s2) {
		return substr($s1, 0, strpos($s1, $s2));
	}
	
	function getNewsTitle($newsId) {
		$req = "SELECT titre FROM lg_newsmod WHERE id = '".(int)$newsId."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0];
	}
	
	//Tracking
	function trackUser($vch) {
		$user = ArghSession::is_logged() ? ArghSession::get_username() : $_SERVER['REMOTE_ADDR'];
	
		$time = time();

		$req = "SELECT count(*) FROM lg_usersonline WHERE user = '".$user."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		if ($l[0] > 0) {
			//Déjà listé => màj
			mysql_query("UPDATE lg_usersonline SET last_visit = '".$time."', page = '".mysql_real_escape_string($_GET['f'])."' WHERE user = '".mysql_real_escape_string($user)."'");
		} else {
			//On ajoute l'utilisateur
			$vch = ($vch) ? 1 : 0;
			mysql_query("INSERT INTO lg_usersonline (user, last_visit, vip, page) VALUES ('".mysql_real_escape_string($user)."', '".$time."', '".$vch."', '".mysql_real_escape_string($_GET['f'])."')");
		}
		
		//clean up
		mysql_query("DELETE FROM lg_usersonline WHERE last_visit <= ".($time - 600));
	}
	
	function truncate($str, $len) {
		return ((strlen($str) > $len) ? substr($str, 0, $len-2).'...' : $str);
	}
?>