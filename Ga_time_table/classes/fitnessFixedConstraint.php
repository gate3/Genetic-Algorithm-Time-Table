<?php
	namespace classes;
	
	class fitnessFixedConstraint {
	
		private $_classes = array();
		private $_ob;
		private $rooms;
		private $_group;
		private $_num_of_classes;
		
		public function __construct(array $c,$cl_count,$objectRepo){		
			$this->_classes = $c;
			$this->_ob = $objectRepo;
			$this->rooms = $this->_ob->getRooms();
			$this->_group = $this->_ob->getStudentGroups();
			$this->_num_of_classes = $cl_count;
		}			
	/*
	|--------------------------------------------------------------------------
	|	Lab Classes
	|--------------------------------------------------------------------------
	|	This function checks lectures that need lab equipment and that have the equipment provided then increases the score.
	|	It also increases the score for those that do not need lab equipment as well.
	*/
		public function classesNeedSpecialRoom(){
			$score_counter = 0;	
			foreach($this->_classes as $k => $v){			
				foreach($v as $p => $c){
					//Check if the current class needs a lab or not
					if(($c->getLab() == 'false')||(($c->getLab() == 'true') && ($this->rooms[$k]->getLab() == 'true'))){	
						$score_counter +=1;
					}
				}				
			}		
			return $score_counter; 
		}	
	/*
	|--------------------------------------------------------------------------
	|	Classroom Size
	|--------------------------------------------------------------------------
	|	This function checks calculates the total number of students for a particular lecture and checks if the class is conducive for them
	|	It increases the score if the class can contain and doesn't if the class cannot.
	*/
		public function classroomSize(){
			$score_counter = 0;			
			$stdGroup = $this->_ob->getStudentGroups();			
			foreach($this->_classes as $k => $v){
				foreach($v as $p => $c){					
					$t_students = 0;	//Count total students
					$studs = $c->getStudentGroup(); //Get the student group by id
					if(substr_count($studs,'-') != 0){
						$studs = explode('-',$c->getStudentGroup()); //Break the student group down					
						for($i=0;$i < count($studs); $i++){ //for each group found
							$t_students += $stdGroup[($studs[$i] - 1)]->getStudentGroupSize();	//for each student group accumulate the size
						}
					}
					else{
							$t_students += $stdGroup[($studs - 1)]->getStudentGroupSize(); 	//for single student group
					}	
					if($this->rooms[$k]->getRoomSize() >= $t_students){		//if the room has enough seats 			
						$score_counter++;
					}					
				}
			}
				return $score_counter;				
		}
	/*
	|--------------------------------------------------------------------------
	|	Consecutive Hours 
	|--------------------------------------------------------------------------
	|	This function checks if the current lecture is being held at thesame venue for the entire duration.
	|	It increases the score if the class is held at thesame venue for the duration and doesn't if the class does not.
	*/
		public function consecutiveHours($s){
			$cnt = 0;	
			$clsDurationCount = 0;
			$score_counter = 0;
			
			foreach($this->_classes as $k => $v){
				foreach($v as $p => $c){				
					$crses = array_slice($s[$k],$p,$c->getDuration());	//Remove the number of elements for the duration in the array
					if(count($crses) == $c->getDuration()){
						$score_counter++;
					}
				}
			}
			return $score_counter;			
		}
	/*
	|--------------------------------------------------------------------------
	|	Clashing Classes 
	|--------------------------------------------------------------------------
	|	This function checks the students and lecturers that have classes that clash with other classes
	|	It increases the score if the class don't clash and doesn't if the class clashes.
	*/
		public function clashingClasses($tag){
			$score_counter = $this->_num_of_classes;	
			$totalClash = 0;
			$stdGroup = '';
			$buff = '';
			foreach($this->_classes as $k => $v){
				$clss = array(); //Declare array for each of the other classes we are comparing 				
				if($this->classToCompare($k) == null){
					break;
				}
				$clss = $this->classToCompare($k);
					for($i=0;$i<count($clss);$i++){	//for each class found for comparison
						$intersect = array_intersect_key($v,$this->_classes[$clss[$i]]); //check if any of the keys match, since key represent time
						
						if($intersect != null){	//if any of the keys match							
							foreach($intersect as $ke => $va){									
								if($tag == '#prof'){ //If lecturers with clashes
									$buff = $this->lecturerToCompare($clss[$i],$k,$ke);
								}					
								elseif($tag == '#group'){	//if students with clashes
									$buff = $this->studentToCompare($clss[$i],$k,$ke);
								}								
								$st = $buff[0];	//The student group or lecturer we are comparing against								
								if(substr_count($buff[1],'-') != 0){	
									$stdGroup = explode('-',$buff[1]);	
								}
								else{
									$stdGroup[] = $buff[1];
								}			
								foreach($stdGroup as $s){
									if(substr_count($st,$s) != 0){	//if the student or lecturer group number exists
										$totalClash++;			//Total number of clashes
										break;
									}
								}
							}
						}						
					}				
			}				
							return $score_counter - $totalClash;
											
		}

		private function classToCompare($cur_class){
			$clss = array();
			if($cur_class == 0){
				$clss[] = 1;	//if first class then we compare to second and third 
				$clss[] = 2;
			}
			elseif($cur_class == 1){
				$clss[] = 2;	//if second class we compare to third only
			}
			else{
				$clss = null;			//if third class we don't need to compare again
			}
			return $clss;
		}
		
		private function studentToCompare($class_to_comp,$cur_class,$time){
			$buff = array();
			$st = $this->_classes[$class_to_comp][$time]->getStudentGroup();	
			$stdGroup = $this->_classes[$cur_class][$time]->getStudentGroup();				
			$buff[] = $st;
			$buff[] = $stdGroup;
			return $buff;
		}
		
		private function lecturerToCompare($class_to_comp,$cur_class,$time){
			$buff = array();
			$lct = $this->_classes[$class_to_comp][$time]->getProf();	//Get the lecturer at the intersection
			$lctGroup = $this->_classes[$cur_class][$time]->getProf();	//Get the lecturer at the current time
			$buff[] = $lct;
			$buff[] = $lctGroup;
			return $buff;
		}		
	}
?>