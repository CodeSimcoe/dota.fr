<?php
	//Encapsulation FCKEditor
	class ArghEditor {
		
		public $_name;
		public $_target;
		private $_editor;
		
		public function ArghEditor($name, $target) {
			$this->_name = $name;
			$this->_target = $target;
			
			//Instanciation FCKeditor avec parametres par defaut
			$this->_editor = new FCKeditor($this->_name);
			$this->_editor->BasePath = '/ligue/FCKeditor/';
			$this->_editor->ToolbarSet = 'Basic';
			$this->_editor->Width = '100%';
			$this->_editor->Height = 200;
		}
		
		public function display() {
			echo '<form method="POST" action="'.$this->_target.'" onSubmit="btn_'.$this->_name.'.disabled=true;"><tr><td>';
			$this->_editor->Create();	
			echo '<input type="submit" value="'.Lang::VALIDATE.'" name="btn_'.$this->_name.'" /></form>';
		}
	}
	
	

?>