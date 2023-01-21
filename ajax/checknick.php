<?
	include'../mysql_connect.php';
	$username = mysql_real_escape_string(substr($_GET["pseudo"], 0, 25));
	$result = mysql_query("SELECT username FROM lg_users WHERE username LIKE '".$username."'");
	echo (mysql_num_rows($result) >= 1 || !preg_match('`^[a-zA-Z0-9_\[\]\-\.]+$`', $username)) ? '1' : '2';
?>