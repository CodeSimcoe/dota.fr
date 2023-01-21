<?php
	require_once('mysql_connect.php');

	require_once('classes/RightsMode.php');

	require_once('classes/ArghSession.php');

	ArghSession::begin();
	
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::NEWS_HEADADMIN,
			RightsMode::NEWS_NEWSER
		)
	);

?>
<html>

<head>
<link rel="stylesheet" href="1.css" type="text/css">
<script language="javascript" type="text/javascript">
function add_icon(text) {
    var oEditor = window.opener.FCKeditorAPI.GetInstance('FCKeditor1');
    oEditor.InsertHtml('<img src="' + text + '" alt="" />');
    oEditor.Focus();
}

</script>
</head>

<body>
<table>
<tr><td><b>Logos</b></td></tr>
<tr><td>
<?php
	$req = "SELECT id, name FROM lg_clans WHERE divi != 0 ORDER BY divi ASC, name ASC";
	$t = mysql_query($req);
	$i = 0;
	while ($l = mysql_fetch_row($t)) {
		$src = 'http://www.dota.fr/ligue/upload/'.$l[0].'.jpg';
		if (list($w, $h) = @getimagesize($src)) {
			$i++;
			$w /= 2;
			$h /= 2;
			echo '<a href="javascript:add_icon(\''.$src.'\')"><img src="'.$src.'" alt="'.$l[1].'" width="'.$w.'" height="'.$h.'" border="0" /></a>';
			if ($i%8 == 0) {
				echo '</td></tr><tr><td>';
			}
		}
	}
?>
</td></tr>
</table>