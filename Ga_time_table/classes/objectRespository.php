<?php 
	namespace classes;
	use classes\file_parser as fpass,
		classes\Objects\course,
		classes\Objects\classroom,
		classes\Objects\lecturer,
		classes\Objects\students,
		classes\Objects\courseName;
		
	include_once('classes\file_parser.php');
	
	class objectRespository {		
		private $rows;		
		private $user_file;
		private $course_class;
		private $rooms;
		private $groups;
		private $prof;
		private $coursename;
		
		public function __construct($file_location){
			$this->user_file = $file_location;
			$this->course_class = $this->createObjects('#course');
			$this->rooms = $this->createObjects('#room');
			$this->groups = $this->createObjects('#group');
			$this->prof = $this->createObjects('#prof');
			$this->coursename = $this->createObjects('#coursename');
		}
		
		public function getNameById(array $haystack,$id){
			for($i = 0; $i < count($haystack); $i++){
				if($haystack[$i]['id'] == $id){
					return $haystack[$i]['name'];
				}
			}			
		}
		
		//This function get's all items of a particular hashtag
		public function getAll($tag){
			if(file_exists($this->user_file)){
				$this->rows = file($this->user_file);
				$fp = new fpass($this->rows);
				return $fp->parse($tag);
			}	
		}
		
		public function getCourse_Classes(){
			return $this->course_class;
		}
		
		public function getRooms(){
			return $this->rooms;
		}
		
		public function getStudentGroups(){
			return $this->groups;
		}
		
		public function getLecturers(){
			return $this->prof;
		}
		
		public function getCourseName(){
			return $this->coursename;
		}
		
		private function createObjects($tag){			
			$anArr = $this->getAll($tag);
			$aObj = array();
			for($i = 0;$i <count($anArr); $i++){
				switch($tag){
					case '#prof':
						$aObj[] = new lecturer($anArr[$i]);
						break;
					case '#room':
						$aObj[] = new classroom($anArr[$i]);
						break;
					case '#group':
						$aObj[] = new students($anArr[$i]);
						break;
					case '#course':
						$aObj[] = new course($anArr[$i]);
						break;
					case '#coursename':
						$aObj[] = new courseName($anArr[$i]);
						break;
				}
			}	
			return $aObj;		
		}	
		
	}
?>