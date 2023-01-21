<?php

	require_once '/home/www/ligue/classes/ReplayParser.php';

	//$path = '/home/www/ligue/match_files/49735.w3g';
	//$parser = new ReplayParser($path);
	//$parser->txt_serialize();

	$replay = DotaReplay::load_from_txt('/home/www/ligue/match_files/49735.w3g.txt');
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="themes/default/default.css" type="text/css">
	<link rel="stylesheet" href="themes/default/parser.css" type="text/css">
</head>
<body>
<div style="width: 600px;">
	<?php echo ReplayFunctions::html($replay); ?>
</div>
</body>
</html>
