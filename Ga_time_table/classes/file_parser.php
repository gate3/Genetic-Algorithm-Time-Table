<?php 
	namespace classes;

	class file_parser{
		private $_file_contents = '';
		
		public function __construct(array $f){
			$this->_file_contents = $f;
		}
		
		public function parse($tag){
			$tag_contents = array();
			//Loop through the string seperated as rows
			for($i = 0; $i< count($this->_file_contents);$i++){
				$obj = explode(',',$this->_file_contents[$i]);							
					if($obj[0] == $tag){					
					$tag_contents[] = $this->_split_to_Assoc_Array($obj); 
				}
			}
			return $tag_contents;
		}
		
		private function _split_to_Assoc_Array($reg_arr){
			$res = array();
			for($i = 1;$i < count($reg_arr); $i++){
				$k = trim(substr($reg_arr[$i],0,strpos($reg_arr[$i],'=')));
				$v = trim(substr($reg_arr[$i],strpos($reg_arr[$i],'=') + 1));
				$res[$k] = $v;
 			}
			return $res;
		}		
	}
?>