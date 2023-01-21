<?php
	class Mail {
	
		public $_expeditor;
		public $_destinators = array();
		public $_subject;
		public $_message;
		
		const DEFAULT_EXPEDITOR = 'arghcontact@gmail.com';
		const MAX_CHARS_PER_ROW = 70;
		
		public function Mail($destinators, $subject, $message, $expeditor = self::DEFAULT_EXPEDITOR) {
			$this->_destinators = $destinators;
			$this->_subject = $subject;
			$this->_message = $message;
			$this->_expeditor = $expeditor;
		}
		
		public function send() {
			@mail(
				implode(', ', $this->_destinators),
				$this->_subject,
				wordwrap($this->_message, self::MAX_CHARS_PER_ROW),
				'From: '.$this->_expeditor."\r\n".'Reply-To: '.$this->_expeditor."\r\n".'X-Mailer: PHP/'.phpversion()
			);
		}
	}
?>