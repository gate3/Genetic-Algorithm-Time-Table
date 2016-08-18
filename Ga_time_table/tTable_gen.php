<?php 
define('CLASS_DIR','classes\\');
define('OBJECT_DIR','classes\\Objects\\');
$cls_files = array(CLASS_DIR.'randomGenerator',					
					OBJECT_DIR.'classroom',
					OBJECT_DIR.'lecturer',
					OBJECT_DIR.'Students',
					OBJECT_DIR.'course',
					CLASS_DIR.'objectRespository',
					CLASS_DIR.'fitnessFixedConstraint',
					CLASS_DIR.'hospital',
					CLASS_DIR.'display',
					OBJECT_DIR.'courseName');

 function file_includer($fl){
	for($i =0;$i<count($fl);$i++){
		include_once($fl[$i].'.php');
	}
}
file_includer($cls_files);
?>



<?php  
use	classes\randomGenerator,
	classes\Objects\classroom,
	classes\Objects\lecturer,
	classes\Objects\students,
	classes\Objects\course,
	classes\objectRespository,
	classes\fitnessFixedConstraint,
	classes\hospital,
	classes\display,
	classes\Objects\courseName;
	
class index {	
	
	const DAYS_OF_WEEK = 5;
	const HOURS_PER_DAY = 11;
	
	const ROOM_1 = 0;
	const ROOM_2 = 1;
	const ROOM_3 = 2;

	private $file_location;
	private $ob;
	private $schedule_type;
	private $semester;

	public function __construct($csvFile,$st,$sm){
		$this->file_location = $csvFile;
		$this->schedule_type = $st;
		$this->semester = $sm;
	}

	public function rndGen($min_x,$max_x,$qty){
		$rnds = randomGenerator::generate($min_x,$max_x,$qty);
		return $rnds;
	}	

	private function brkLine(){
		echo '<br/>';
	}
	
	private function echoBrkLine($a){
		echo $a.$this->brkLine();
	}
	
	private function map_class_to_hours($cls_hours){
		$sched = array(	self::ROOM_1 => array(),
						self::ROOM_2 => array(),
						self::ROOM_3 => array());
						
		for($i=0; $i < count($cls_hours); $i++){
			for($j = 0;$j<count($cls_hours[$i]); $j++){
				if(is_object($cls_hours[$i][$j])){
					$sched[$i][$j] = $cls_hours[$i][$j];
					$j+=$sched[$i][$j]->getDuration()-1;
				}
			}
		}
		return $sched;
	}
	
	public function initialise_schedule(){//'C:\wamp\www\Ga_time_table\config.csv'
		$t_space = self::DAYS_OF_WEEK * self::HOURS_PER_DAY; 
		$this->ob = new objectRespository($this->file_location);				
		
		$clsRoom = array(self::ROOM_1 => array(),
						 self::ROOM_2 => array(),
						 self::ROOM_3 => array());
						 
		$schedule = $clsRoom; //DRY concept (don't repeat yourself)
		$randNums = $clsRoom;
		
		$randNums[self::ROOM_1] = $this->rndGen(0,$t_space,21); //Generate 21 random numbers
		$randNums[self::ROOM_2] = $this->rndGen(0,$t_space,21);
		$randNums[self::ROOM_3] = $this->rndGen(0,$t_space,21);
		
		$clsRoom[self::ROOM_1] = array_fill(0,$t_space,0);	//Fill each room with 0's 
		$clsRoom[self::ROOM_2] = array_fill(0,$t_space,0);
		$clsRoom[self::ROOM_3] = array_fill(0,$t_space,0);		
		
		$course_class = $this->ob->getCourse_Classes();	//Get the lectures
		
		for($i = 0; $i < count($course_class); $i++){
			$buff = array();
			$test = array();
					
			$dur = $course_class[$i]->getDuration();				
			$cls = rand()%3;	
			$time = abs($randNums[$cls][$i]);					
			
			while(is_object($clsRoom[$cls][$time])){
				$time = rand()%60;
				if(!is_object($clsRoom[$cls][$time])){
					break;
				}
			}
			
			if( $this->schedule_type == 'Examination Schedule' ){
				$rand_lect = $this->rndGen(1,10,2);
				$lects = '';
				foreach($rand_lect as $k => $p){
					$lects.=$p .'-';
				}			
				$lects = substr_replace($lects, '', strlen($lects)-1);			
				$course_class[$i]->setProf($lects);				
			}

			$buff = $clsRoom[$cls];
			$test = array_fill(0,$dur,$course_class[$i]);			
			array_splice($buff,$time,0,$test);
			$clsRoom[$cls] = $buff;						
		}		
		$schedule = $this->map_class_to_hours($clsRoom);
		
		$fit = $this->calculateFitness($schedule,$clsRoom,count($course_class));		
		$dr = new hospital();	
		$generations = 1;	//Initial generation is the first generation
		$crossovers = 0;
		$elite = array();	//Store the elite, any chromosome above 0.95
		$mutations = 0;
		$new_fit = 0;
		
		while(($fit >= 1) || ($generations < 1000)){
			$dr->set_class_hours($clsRoom);
			$dr->set_schedule($schedule);
			
			if(rand()%3 == 2){				
				$dr->do_crossover();
				$copy_cls_hrs = $dr->get_class_hours();
				$copy_cls_sch = $dr->get_schedule();
				$new_fit = $this->calculateFitness($copy_cls_sch,$copy_cls_hrs,count($course_class));
				$crossovers++;	
			}
			
			if(rand()%3 == 2){	//Perform mutation
				$dr->mutation();
				$mutations++;
			}
			
			if($new_fit > $fit){
				if($fit >= 0.95){
					$elite[] = $clsRoom;
				}
				$schedule = $copy_cls_sch;
				$clsRoom = $copy_cls_hrs;
				$fit = $new_fit;	
			}	
			$generations++;			
		}
		echo '<h1 style="font-align:center">'. $this->semester. '</h1>';
		echo '<h2 style="font-align:center"><u> Fitness Statistics </u></h2>';
		$fit = $this->calculateFitness($schedule,$clsRoom,count($course_class),true);
		//$this->brkLine();
		$this->echoBrkLine('Elite Chromosomes: '.count($elite));
		$this->brkLine();
		$this->echoBrkLine('Number of Generations: '. $generations);
		$this->brkLine();
		$this->echoBrkLine('Number of Crossovers: '. $crossovers);
		$this->brkLine();
		$this->echoBrkLine('Number of Mutations: '. $mutations);		
		
		$ds = new display($schedule,$clsRoom,$this->ob);		
		$ds->inputCourses();	
	}
	
	private function calculateFitness($schedule_array,$class_hours_array,$number_of_courses,$rpt=false){
		$schScore =0;
		$fit = new fitnessFixedConstraint($schedule_array,$number_of_courses,$this->ob);
		$schScore += $fit->classesNeedSpecialRoom(); 		
		$schScore += $fit->classroomSize();			
		$schScore += $fit->consecutiveHours($class_hours_array); 
		$schScore += $fit->clashingClasses('#group'); 		
		$schScore += $fit->clashingClasses('#prof'); 		
		if($rpt == true){
			$this->echoBrkLine('Need Special Room: '.$fit->classesNeedSpecialRoom());
			$this->echoBrkLine('Classroom size: '.$fit->classroomSize());
			$this->echoBrkLine('Consecutive Hours: '. $fit->consecutiveHours($class_hours_array));
			$this->echoBrkLine('Clashing course students: '.$fit->clashingClasses('#group'));
			$this->echoBrkLine('Clashing course lecturers: '.$fit->clashingClasses('#prof'));
			$this->echoBrkLine('Total Courses = '.$number_of_courses);
			$this->echoBrkLine('Fitness Score = ' .$schScore/($number_of_courses * self::DAYS_OF_WEEK));
		}		
		return ($schScore/($number_of_courses * self::DAYS_OF_WEEK));
	}
	
	private function get_lecturer_count(){		
		$lecturers = $this->ob->getLecturers();
		$lect_count = 0;
		foreach($lecturers as $k => $p){
			$lect_count++;
		}
		return $lect_count;
	}
	
}


	$uploaddir = 'csv/';
	$uploadfile = $uploaddir . $_FILES['userfile']['name'];
	$schedule_type = $_POST['s_type'];
	$sem = $_POST['semester'];

	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    	
    	$start_time = microtime(true);
		$start_memory = memory_get_peak_usage(false);

		$id = new index($uploadfile,$schedule_type,$sem);
		$id->initialise_schedule();
		
		$end_memory = memory_get_peak_usage(false);
		$end_time = microtime(true);

		echo '<h2>Speed Evaluation = '. number_format(($end_time - $start_time),4). ' Seconds </h2>';
		echo '<h2>Memory Utilization = '. ($end_memory - $start_memory). ' Bytes</h2>';

		    	
	} else {
	    echo "File Upload Error!\n";
	}
?>