<?php 
	namespace classes\Objects;
	use classes\Objects\Inheritables\abstractObjectFactory;
	
	include_once('Inheritables\abstractObjectFactory.php');
	
	class classroom extends abstractObjectFactory{		
		private $_lab;
		private $_roomSize;
		
		public function __construct(array $f){
			parent::__construct($f['id'],$f['name']);			
			$this->_lab = $f['lab'];
			$this->_roomSize = (int)$f['size'];
		}
		
		public function getRoomSize(){
			return $this->_roomSize;
		}
		
		public  function getLab(){
			return $this->_lab;
		}
		
		public function toString(){
			printf("Room Id: %s <br/> Room Name: %s <br/> Lab Equipment: %s <br/> Room Size: %s",$this->_Id,$this->_Name,$this->_lab,$this->_roomSize);			
		}
	}
?>
<?php  ?>