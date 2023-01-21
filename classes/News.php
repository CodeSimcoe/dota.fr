<?php
class News {

	public $_id;
	public $_author;
	public $_title;
	public $_publication_date;
	public $_content;
	public $_is_deleted;
	public $_is_shown;
	public $_views;
	public $_comments_locked;
	public $_category;
	public $_author_lock;
	
	public $_edit_historical = array();
	public $_messages = array();
	
	public function News() {}
	
	private function build_from_sql_resource($sql_resource) {
		$this->_id = $sql_resource->id;
		$this->_author = $sql_resource->poster;
		$this->_title = stripslashes($sql_resource->titre);
		$this->_publication_date = $sql_resource->daten;
		$this->_content = stripslashes($sql_resource->texte);
		$this->_is_deleted = ($sql_resource->deleted == 0) ? false : true;
		$this->_is_shown = ($sql_resource->afficher == 0) ? false : true;
		$this->_views = $sql_resource->views;
		$this->_comments_locked = ($sql_resource->comments_locked == 0) ? false : true;
		$this->_category = $sql_resource->categorie;
		$this->_author_lock = $sql_resource->author_lock;
	}
	
	public function load($news_id, $fetch_deleted = 0) {
		$req = "SELECT * FROM lg_newsmod WHERE id = '".(int)$news_id."' AND deleted = '".$fetch_deleted."'";
		$t = mysql_query($req);
		
		if (mysql_num_rows($t) == 0) return false;
		
		$l = mysql_fetch_object($t);
		
		//Loading news information without messages
		$this->build_from_sql_resource($l);
		
		return true;
	}
	
	/*@deprecated
	public function get_messages() {
		//Loads messages from the database
		$this->_messages = GenericMessage::load_referenced(Tables::NEWS_MESSAGES, $this->_id);
	}
	*/
	
	public function increment_views() {
		mysql_query("UPDATE lg_newsmod SET views = views + 1 WHERE id = '".$this->_id."'");
	}
	
	public function is_shown() {
		return $this->_is_shown;
	}
	
	public function show() {
	
		$locked = '';
		if ($this->_comments_locked) {
			$locked = '<img src="/img/icons/lock.png" alt="" />&nbsp;';
		}
	
		ArghPanel::begin_tag($locked.$this->_title);
		
		echo Lang::BY.' <strong>'.$this->_author.'</strong> '.Lang::POSTED_ON.' '.date(Lang::DATE_FORMAT_HOUR, $this->_publication_date).' - <em>'.$this->_views.' '.Lang::VIEWS.'</em><br /><hr />'.$this->_content;
		
		ArghPanel::end_tag();
	}
	
	public static function get_news_by_category($categ, $nb_news = 7) {
		$time = time();
		$req = "SELECT * FROM lg_newsmod WHERE afficher = 1 AND categorie = '".$categ."' ORDER BY daten DESC LIMIT 0,".(int)$nb_news;
		$t = mysql_query($req);
		while ($l = mysql_fetch_object($t)) {
			$is_new = ($time - $l->daten) < 84600;
			$posts = mysql_num_rows(mysql_query("SELECT * FROM lg_comment WHERE news_id = '".$l->id."'"));
			echo alinea().date(Lang::DATE_FORMAT_DAY_MONTH_ONLY, $l->daten).' <a href="?f=news&amp;id='.$l->id.'">'.shortenTitle(stripslashes($l->titre)).'</a> (<a href="?f=news&amp;id='.$l->id.'#comment">'.$posts.'</a>)'.($is_new ? '<img src="img/warn.png" width="16" height="16" alt="" />' : '').'<br />';
		}
	}
	
	public static function get_rss_news($nb_news = 7) {
		$time = time();
		$query = "SELECT title, date_news, link, website FROM lg_rss_news ORDER BY date_news DESC LIMIT 0,".(int)$nb_news;
		$result =  mysql_query($query);
		while ($l = mysql_fetch_object($result)) {
			$is_new = ($time - $l->date_news) < 84600;
			echo '<img src="rss/'.$l->website.'.png" alt="" />&nbsp;'.date(Lang::DATE_FORMAT_DAY_MONTH_ONLY, $l->date_news).' <a href="'.stripslashes($l->link).'" target="_blank">'.utf8_decode(stripslashes($l->title)).'</a> '.($is_new ? '<img src="img/warn.png" width="16" height="16" alt="" />' : '').' <span class="info" style="font-size: 10px;">'.$l->website.'</span><br />';
		}
	}
	
}
?>
