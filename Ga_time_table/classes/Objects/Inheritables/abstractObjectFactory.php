<?php 
	namespace classes\Objects\Inheritables;
	
	abstract class abstractObjectFactory {
		protected $_Id;
		protected $_Name;
		
		public function __construct($id,$nm){
			$this->_Id = $id;
			$this->_Name = $nm;
		}		
		
		public function getId(){
			return $this->_Id;
		}
		
		public function getName(){
			return $this->_Name;
		}
		
	}
	
?>