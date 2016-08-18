<?php 
	namespace classes\Objects;
	use classes\Objects\Inheritables\abstractObjectFactory;
	
	include_once('Inheritables\abstractObjectFactory.php');
	
	class courseName extends abstractObjectFactory{
	
		public function __construct(array $f){
			parent::__construct($f['id'],$f['name']);
		}
		
		public function toString(){
			printf("Course Id: %s <br/> Course Name: %s <br/>",$this->_Id,$this->_Name);			
		}
	}
?>