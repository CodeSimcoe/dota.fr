<?php
	//Error Reporting
	ini_set('display_errors', 1);
	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	
	require_once '/home/www/ligue/mysql_connect.php';
	
	$query = "SELECT address, website FROM lg_rss_feed";
	$result = mysql_query($query);
	
	while ($row = mysql_fetch_row($result)) {
		$address = $row[0];
		$website = $row[1];
		
		$rss = @simplexml_load_file($address);
		
		if ($rss) {
			$items = $rss->channel->item;
			if (!empty($items)) {
				foreach ($items as $item) {
					$unix_date = strtotime($item->pubDate);
					
					$insert = "INSERT INTO lg_rss_news (title, date_news, link, website)
								VALUES ('".mysql_real_escape_string($item->title)."', '".$unix_date."', '".mysql_real_escape_string($item->link)."', '".$website."')";
					mysql_query($insert);
					
				}
			}
		}
	}
?>