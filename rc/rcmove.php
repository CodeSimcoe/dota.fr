<?php

	if ($_SESSION['access'] < 101) exit();

	function generateKey($length) {
		$pattern = 'abcdefghijklmnopqrstuvwxyz0123456789-_';
		$key = '';
		for ($i = 1; $i <= $length; $i++) {
			$key .= $pattern[rand(0, strlen($pattern)-1)];
		}
		return $key;
	}

	$preplay = "";
	if (isset($_POST["rcreplay"]) AND isset($_POST["move"])) {
		$preplay = $_POST["rcreplay"];
		$path = "rc/rctmp/".$preplay;
		$rdm = 'replay_'.generateKey(16).'.w3g';
		rename($path, "rc/rcdwld/".$rdm);
		rename($path.".txt", "rc/rcdefs/".$rdm.".txt");
		$req = "INSERT INTO rc_replays (replay_title, replay_sentinel, replay_scourge, replay_winner, replay_file, replay_published, posted_by, posted_on)
				VALUES ('".$preplay."', '', '', '', '".$rdm."', 0, '".$_SESSION['username']."', '".time()."')";
		mysql_query($req);
		$rid = mysql_insert_id();
	}

?>