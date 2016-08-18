<?php 
	namespace classes\Objects;
	use classes\Objects\Inheritables\abstractObjectFactory;
	
	include_once('Inheritables\abstractObjectFactory.php');
	
	class course {		
		
		private $_prof;
		private $_duration;
		private $_student_group;		
		private $_course_name;
		private $_lab;
		private $_classRoom;
		
		public function __construct(array $f){
			//parent::__construct($f['id'],$f['name']);			
			$this->_prof = $f['professor'];
			$this->_course_name = $f['coursename'];
			$this->_duration = $f['duration'];
			$this->_student_group = $f['group'];			
			$this->_lab = $f['lab'];			
		}
		
		public function getProf(){
			return $this->_prof;
		}
		
		public function setProf($p){
			$this->_prof = $p;
		}

		public function getCourseName(){
			return $this->_course_name;
		}
		
		public function getDuration(){
			return (int)$this->_duration;
		}
		
		public function getStudentGroup(){
			return $this->_student_group;
		}
		
		public function getLab(){
			return $this->_lab;
		}	
		
		public function setClassRoom($r){
			$this->_classRoom = $r;
		}
		
		public function getClassRoom(){
			return $this->_classRoom;
		}
		
		public function toString(){
			printf("Prof: %s <br/> Course Name: %s <br/> Class Duration: %s Hours <br/>  Student Group: %s",
											$this->_prof,$this->_course_name,$this->_duration,$this->_student_group);			
		}
	}
?>