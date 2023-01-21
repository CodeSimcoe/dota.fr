<?php
	ArghSession::exit_if_not_logged();
	
	//Fonctions
	require_once 'laddervip_functions.php';
?>

<script language="javascript">

	//Ajax
	var http;
	
	function Refresh(doLock) {
		if (doLock == 1) lockIcons();
		http = createRequestObject();
		http.open('get', 'ajax/laddervip_10.php', true);
		http.onreadystatechange = handleAJAXRefresh;
		http.send(null);
	}
	
	function Leave() {
		lockIcons();
	    http = createRequestObject();
	    http.open('get', 'ajax/laddervip_left.php', true);
	    http.onreadystatechange = handleAJAXLeave;
	    http.send(null);
	}
	
	function Join() {
		lockIcons();
	    http = createRequestObject();
	    http.open('get', 'ajax/laddervip_joined.php', true);
	    http.onreadystatechange = handleAJAXJoin;
	    http.send(null);
	}
	
	function lockIcons() {
		document.getElementById('btn_refresh').innerHTML = '<center><img src="ladder/btn_norefresh.jpg" alt="" /></center>';
		
		switch (document.getElementById('icon').innerHTML) {
			case '0':
				//Rien
				break;
			case '1':
				document.getElementById('btn_2').innerHTML = '<img src="ladder/btn_noleave.jpg" alt="" />';
				break;
			case '2':
				document.getElementById('btn_2').innerHTML = '<img src="ladder/btn_nojoin.jpg" alt="" />';
				break;
			default:
				break;
		}
		
		document.getElementById('ajax_loading').innerHTML = '<img src="img/ajax-loader.gif" alt="" />';
	}

	function createRequestObject() {
	    var http;
	    if (window.XMLHttpRequest) {
	        http = new XMLHttpRequest();
	    } else if (window.ActiveXObject) {
	        http = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    return http;
	}
	
	function handleAJAXRefresh() {
		if (http.readyState == 4) {
			if (http.status == 200) {
				document.getElementById('ladder10').innerHTML = http.responseText;
			} else {
				document.getElementById('ladder10').innerHTML = '<strong>Erreur.</strong>';
			}
		}
	}
	
	function handleAJAXLeave() {
		if (http.readyState == 4) {
			if (http.status == 200) {
				Refresh(0);
			}
		}
	}
	
	function handleAJAXJoin() {
		if(http.readyState == 4) {
			if(http.status == 200) {
				Refresh(0);
			}
		}
	}

	<?php
	if (ArghSession::is_gold()) {
		echo 'setInterval("Refresh(1)", 8000);';
	} else {
		echo 'setInterval("Refresh(1)", 16000);';
	}
	?>

	
</script>
<?php
	//Vouched ?
	if (!ArghSession::is_vouched()) {

		exit(Lang::LADDERVIP_CANT_PARTICIPATE);
	}
		
	ArghPanel::begin_tag(Lang::LADDER_VIP);
?>
<table class="simple">
	<tr><td colspan="2"><div id="ladder10">
	<?php
		//Banned
		if (isBanned(ArghSession::get_username())) {
			$req = "SELECT * FROM lg_ladderbans WHERE qui = '".ArghSession::get_username()."'";
			$t = mysql_query($req);
			$l = mysql_fetch_object($t);
			
			//Unban si ban termine
			if (isFinished($l->quand, $l->duree)) {
				BanManager::unban($l->id);
				
				//mysql_query("DELETE FROM lg_ladderbans WHERE id='".$l->id."'");
				//Admin Log
				$al = new AdminLog(sprintf(Lang::ADMIN_LOG_UNBAN_USER, $l->qui), AdminLog::TYPE_LADDER, 'LadderGuardian');
				$al->save_log();
				/*
				$admin_req="INSERT INTO lg_adminlog (qui, quand, quoi) VALUES ('LadderGuardian', '".time()."', 'Unban ".$l->qui."')";
				mysql_query($admin_req);
				*/
			} else {
				$remain = remainingTime($l->quand, $l->duree);
				
				echo '<center>'.sprintf(Lang::LADDER_BANNED_ACCOUNT, $l->par_qui, $l->raison).'<br />';
				if ($remain != '-') {
					if ($remain == 0) {
						echo Lang::LADDER_UNBAN_LESS_1_HOUR;
					} else {
						echo sprintf(Lang::LADDER_DELAY_UNTIL_UNBAN, $remain);
					}
				}
				echo '</center>';
				
				exit;
			}
		}

		include 'ajax/laddervip_10.php';
?>
	</div></td></tr>
</table>
<?php

	ArghPanel::end_tag();

	ArghPanel::begin_tag(Lang::ONLINE_VIP_PLAYERS);
	$req = "
		SELECT o.user, u.ladder_status
		FROM lg_usersonline o, lg_users u
		WHERE o.vip = '1'
		AND u.username = o.user
		ORDER BY o.user ASC";
	$res = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($res) > 0) {
		$players = array();
		while ($obj = mysql_fetch_object($res)) {
			$status = "win";
			if ($obj->ladder_status == 'busy_norm') $status = "lose";
			else if ($obj->ladder_status == 'busy_vip') $status = "vip";
			$players[$obj->user] = array( "user" => $obj->user, "status" => $status );
		}
		$content = file(CacheManager::LADDER_VIP_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (isset($players[$line[0]])) $players[$line[0]]["status"] = "vip";
		}
		$content = file(CacheManager::LADDER_PLAYERLIST);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (isset($players[$line[0]])) $players[$line[0]]["status"] = "lose";
		}
	}
	echo '<table class="simple">';
	echo '<tr><td>'.Lang::LEGEND.'</td></tr>';
	echo '<tr><td class="line">&nbsp;</td></tr>';
	echo '<tr><td><span class="win">'.Lang::AVAILABLE.'</span></td></tr>';
	echo '<tr><td><span class="vip">'.Lang::IN_A_VIP_GAME.'</span></td></tr>';
	echo '<tr><td><span class="lose">'.Lang::IN_A_LADDER_GAME.'</span></td></tr>';
	echo '<tr><td>&nbsp;</td></tr>';
	echo '<tr><td>'.Lang::USERNAME.'</td></tr>';
	echo '<tr><td class="line">&nbsp;</td></tr>';
	foreach ($players as $player) {
		echo '<tr><td><span class="'.$player["status"].'">'.$player["user"].'</span></td></tr>';
	}
	echo '</table>';
	ArghPanel::end_tag();
?>

<!-- Preload Icones -->
<img src="ladder/btn_norefresh.jpg" class="hid" alt="" />
<img src="ladder/btn_nojoin.jpg" class="hid" alt="" />
<img src="ladder/btn_leave.jpg" class="hid" alt="" />
<img src="ladder/btn_noleave.jpg" class="hid" alt="" />
<img src="ladder/btn_join.jpg" class="hid" alt="" />