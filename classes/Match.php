<?php
class Match {

	public $_id;
	public $_messages = array();
	
	public function Match() {}
	
	private function build_from_sql_resource($sql_resource) {
		$this->_id = $sql_resource->id;
	}
	
	/*@deprecated
	public function get_messages() {
		//Loads messages from the database
		$this->_messages = GenericMessage::load_referenced(Tables::LEAGUE_MESSAGES, $this->_id, true);
	}
	*/
}
?>