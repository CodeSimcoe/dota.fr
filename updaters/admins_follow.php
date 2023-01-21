<?php

include('/home/www/ligue/mysql_connect.php');

$res = mysql_query("
	TRUNCATE TABLE lg_ladderfollow_admins
") or die(mysql_error());

$res = mysql_query("
	INSERT INTO lg_ladderfollow_admins (admin, qui, quand)
	SELECT DISTINCT a.username, REPLACE(REPLACE(b.quoi, 'Unban', ''), ' : ', ''), b.quand
	FROM lg_users a
	LEFT JOIN lg_adminlog b ON a.username = b.qui 
	WHERE (a.rights & 1) <> 1
	AND b.quoi LIKE 'unban%'
	AND REPLACE(REPLACE(b.quoi, 'Unban', ''), ' : ', '') <> ''
	AND b.quand > 1251138448
") or die(mysql_error());

$req = "
	SELECT DISTINCT a.qui 
	FROM lg_ladderfollow_admins a
	LEFT JOIN lg_users b ON a.qui = b.username
	WHERE b.username IS NULL
	ORDER BY a.quand DESC";
$res = mysql_query($req) or die(mysql_error());
if (mysql_num_rows($res) > 0) {
	while (true) {
		while ($obj = mysql_fetch_object($res)) {
			$breq = "
				SELECT old_username, new_username 
				FROM lg_pending_nick_changes 
				WHERE old_username = '".$obj->qui."'
				AND validated = 1
				AND changed = 1
				ORDER BY request_time DESC
				LIMIT 1";
			$bres = mysql_query($breq) or die(mysql_error());
			$bobj = mysql_fetch_object($bres);
			$cres = mysql_query("
				UPDATE lg_ladderfollow_admins SET qui = '".$bobj->new_username."' WHERE qui = '".$bobj->old_username."'
			") or die(mysql_error());
		}
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) == 0) break;
	}
}

/*
SELECT FROM_UNIXTIME(b.quand), b.admin, b.qui, a.motif, a.admin, FROM_UNIXTIME(a.quand)
FROM lg_ladderfollow_admins b
LEFT JOIN lg_ladderbans_follow a ON a.username = b.qui
WHERE a.type = 'ban'
AND a.`force` > 0
AND b.quand BETWEEN a.quand 
AND a.quand + (86400 * a.`force`) 
AND a.motif like 'autoban%' 
ORDER BY FROM_UNIXTIME(a.quand) DESC
*/

?>