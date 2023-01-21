<?php
	ArghPanel::begin_tag();
	/*
	//ESWC Event
	echo '<img src="img/news/enUS/kota.jpg" title="'.Lang::NEWS_CAT_5.'" /><br /><br />';
	News::get_news_by_category(7, 10);
	*/
	//News
	echo '<br /><br /><img src="img/news/enUS/coverage.jpg" title="'.Lang::NEWS_CAT_1.'" /><br /><br />';
	News::get_news_by_category(1, 10);
	echo '<br /><br /><img src="img/news/enUS/community.jpg" title="'.Lang::NEWS_CAT_2.'" /><br /><br />';
	News::get_news_by_category(2, 10);
	echo '<br /><br /><img src="img/news/enUS/inter.jpg" title="'.Lang::NEWS_CAT_3.'" /><br /><br />';
	News::get_news_by_category(3, 10);
	echo '<br /><br /><img src="img/news/enUS/downloads.jpg" title="'.Lang::NEWS_CAT_4.'" /><br /><br />';
	News::get_news_by_category(4, 10);
	
	ArghPanel::end_tag();
?>