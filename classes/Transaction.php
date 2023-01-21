<?php
	class Transaction {
		
		public $_id;
		public $_username;
		public $_date_transaction;
		public $_product;
		public $_code;
		
		public function Transaction() {
		}
		
		public function build_from_sql($sql_resource) {
			$this->_id = $sql_resource->id;
			$this->_username = $sql_resource->username;
			$this->_date_transaction = $sql_resource->date_transaction;
			$this->_product = $sql_resource->product;
			$this->_code = $sql_resource->code;
		}
		
		public static function load_user_transactions($user) {
			$query = "SELECT * FROM lg_transactions WHERE username = '".mysql_real_escape_string($user)."' ORDER BY id DESC";
			$result = mysql_query($query);
			
			$transactions = array();
			while ($sql_resource = mysql_fetch_object($result)) {
				$t = new Transaction();
				$t->build_from_sql($sql_resource);
				
				$transactions[] = $t;
			}
			
			return $transactions;
		}
		
		public function save() {
			$query = "INSERT INTO lg_transactions (username, date_transaction, product, code)
					  VALUES ('".mysql_real_escape_string($this->_username)."', '".time()."', '".$this->_product."', '".$this->_code."')";
			mysql_query($query);
		}
		
	}
?>