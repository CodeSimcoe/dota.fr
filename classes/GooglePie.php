<?php
	class GooglePie {

		public $_PieSlices;
		public $_width;
		public $_height;
		public $_background_color;
		public $_title;
		public $_main_color;
		
		public function GooglePie() {
			$this->_background_color = '000000';
		}
		
		public function set_size($width, $height) {
			$this->_width = $width;
			$this->_height = $height;
		}
		
		public function add_slice(PieSlice $slice) {
			$this->_PieSlices[] = $slice;
		}
		
		public function render() {
			echo $this->render_str();
		}
		
		public function render_str() {
			$out = '<img src="http://chart.apis.google.com/chart?cht=p3&chd=t:';
			foreach ($this->_PieSlices as $slice) {
				$out .= $slice->_value.',';
			}
			self::remove_last_char($out);
			$out .= '&chs='.$this->_width.'x'.$this->_height.'&chl=';
			foreach ($this->_PieSlices as $slice) {
				$out .= urlencode($slice->_text).'|';
			}
			self::remove_last_char($out);
			$out .= '&chco=';
			foreach ($this->_PieSlices as $slice) {
				$out .= urlencode($slice->_color).',';
				if ($slice->_color == null) {
					$out .= $this->_main_color;
				}
			}
			self::remove_last_char($out);
			$out .= '&chf=bg,s,'.$this->_background_color.'" alt="'.$this->_title.'" />';
			
			return $out;
		}
		
		private static function remove_last_char(&$array) {
			$array = substr($array, 0, strlen($array) - 1);
		}
	}

	class PieSlice {

		public $_value;	//valeur (en %)
		public $_color;	//couleur (code hexa sans #)
		public $_text;	//legende
		
		public function PieSlice($text, $value, $color = null) {
			$this->_text = $text;
			$this->_value = $value;
			$this->_color = $color;
		}
	}
?>