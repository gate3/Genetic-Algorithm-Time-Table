<?php
	namespace classes;
	
	class display {
		
	private $_classes = array();
	private $_sched = array();	
	private $_lecturer = array();
	private $_courseName = array();
	private $_room = array();
	private $_group = array();
	private $_lectures = array();
	
	public function __construct(array $s,array $c,$objectRepo){		
		$this->_classes = $c;		
		$this->_sched = $s;
		
		$ob = $objectRepo;
		
		$this->_lecturer = $ob->getLecturers();
		$this->_courseName = $ob->getCourseName();
		$this->_room = $ob->getRooms();
		$this->_group = $ob->getStudentGroups();
		$this->_lectures = $ob->getCourse_Classes();
		$this->create_css();
	}	
	
	private function create_css(){
		$css = '<head>
				<style type="text/css">				
					body{margin:0px; padding:0px}
					table.Ttable{border:solid thin; width:100%} 
					/*table.small-table{border:solid thin; width:2%; height:2%} */
					td.courses{width:30px; max-width:30px; align:center;text-align:center}
					td.time{width:8px; height:8px; border:none; text-align:center}
					td.day{border:none; text-align:center}
					p.class-details{font-size:8px; width:5px;}
					.Ttable>tbody>tr:nth-child(odd)>td,.timeTable>tbody>tr:nth-child(odd)>th{background-color:#eaeaea}
				</style>
				<title>Kwara State University</title>
				</head>';
		echo $css;
	}
	
	private function startTable(){		
		echo '<table border="1" class="Ttable">';
	}
	
	private function endTable(){
		echo '</table>';
	}
	
	public function inputCourses(){	
		for($u =0;$u<3;$u++){
			$cl_det = '<table align="center" border="1">';
			$cl_det .= '<tr align="center"><td colspan="2">';			 
			$cl_det .= '<h3>Name: '.$this->_room[$u]->getName().'</h3></td></tr>';
			$cl_det .= '<tr align="center"><td>';
			$cl_det .='<h3>Lab: '.$this->_room[$u]->getLab().'</h3></td>';
			$cl_det .= '<td><h3>Seats: '.$this->_room[$u]->getRoomSize().'</h3></td>';
			$cl_det .= '</table>';
			echo $cl_det;
			
			$this->startTable();
			$cls_details = '<tr><td class="time"></td>';
			echo $cls_details;
				for($i=2;$i<=6;$i++){
					echo '<td class="day"><h4>'.date('D',mktime(0,0,0,0,$i)).'</h4></td>';
				}
			echo '</tr>';
			for($i=0;$i<12;$i++){
				$d = $i + 8;
				$b = '';
					if($d<12){
						$b .= '<tr><td class="time"><h4>'. $d .' - '. ($d+1) .'</h4></td>'; 
					}
					elseif($d == 12){
						$b .= '<tr><td class="time"><h4>'. $d .' - '. (($d+1) - 12) .'</h4></td>'; 
					}
					else{
						$b .= '<tr><td class="time"><h4>'. ($d - 12) .' - '. (($d + 1) - 12) .'</h4></td>'; 
					}
				echo $b;
				for($j=0;$j<5;$j++){	
					$a ='<td class="courses">';
					$c = $i + ($j * 12);
					if(is_object($this->_classes[$u][$c])){
						$a .= $this->getCourseComponents($this->_classes[$u][($c)],0);
					}						
					$a.='</td>';
					echo $a;
				}
				echo '</tr>';
			}
			$this->endTable();
			echo $this->brkLine();
		}
	}
	
	private function brkLine(){
		return '<br/>';
	}
	
	private function getStudentGroupName($grps){
		$stdGroup = array();
		$grp_arr = array();
		$result = '';
		
		if(substr_count($grps,'-') != 0){	
			$stdGroup = explode('-',$grps);
		}
		else{
			$stdGroup[] = $grps;
		}
		
		foreach($stdGroup as $g){
			$grp_arr[] = $this->_group[(int)$g-1]->getName();			
		}
		return implode('|',$grp_arr);
	}
	
	private function getCourseComponents($course,$cls){
		$c = '<b>Course Name:  </b>'.$this->_courseName[($course->getCourseName()-1)]->getName() . $this->brkLine();
		$c .= '<b>Lecturer:  </b>'. $this->sortLecturers($course) . $this->brkLine();
		$c .= '<b>Room Name:  </b>'.$this->_room[$cls]->getName() . $this->brkLine();
		$c .= $course->getLab() == 'true' ? '<b>Requires Lab:</b>  Yes'. $this->brkLine() : '<b>Requires Lab:  </b> No' . $this->brkLine();
		$c .= '<b>Student Group:  </b>'.$this->getStudentGroupName($course->getStudentGroup());
		return $c;
	}

	private function sortLecturers($c){
		$bunch = $c->getProf();

		if(substr_count($bunch,'-') != 0){
			$bunch = explode('-',$bunch);
		}
		else{
			$a = $bunch;
			$bunch = array();
			$bunch[] = $a;
		}
		$l = '';

		foreach($bunch as $k){
			$l .= $this->_lecturer[ $k-1 ]->getName() .',';		
			
		}		
		$l = substr_replace($l, '', strlen($l)-1);	
		return $l;
	}
		
}
?>
