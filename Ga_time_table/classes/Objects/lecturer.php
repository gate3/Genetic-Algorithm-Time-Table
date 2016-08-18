<?php 
	namespace classes\Objects;
	use classes\Objects\Inheritables\abstractObjectFactory;
	
	include_once('Inheritables\abstractObjectFactory.php');
	
	class lecturer extends abstractObjectFactory{
	
		public function __construct(array $f){
			parent::__construct($f['id'],$f['name']);
		}
		
		public function toString(){
			printf("Lecturer Id: %s <br/> Lecturer Name: %s <br/>",$this->_Id,$this->_Name);			
		}
	}
	
?>