<?php
namespace classes;

class hospital {
	private $_classes = array();
	private $_sched = array();
	
	public function __construct(){		
		
	}
		
	public function set_class_hours($cls_hours){
		$this->_classes = $cls_hours;
	}
	
	public function set_schedule($sch){
		$this->_sched = $sch;
	}
	
	public function get_class_hours(){
		return $this->_classes;
	}
	
	public function get_schedule(){
		return $this->_sched;
	}
	
	private function randClass(){
		$rand = array();
		$rand[] = $this->genRnd();
		$rand[] = $this->genRnd();
		while($rand[0]==$rand[1]){
			$rand[0] = $this->genRnd();
			$rand[1] = $this->genRnd();
			if($rand[0] != $rand[1]){
				break;
			}
		}
		return $rand;
	}
	
	private function genRnd(){
		return rand()%3;
	}
	
	private function map_class_to_hours($cls_hours){
		$sched = array();
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
	
	private function swapClasses($c1,$c2){
		$cls = array();
		$proxy_classes = $this->_classes;
		$cls[] = $proxy_classes[$c1];
		$cls[] = $proxy_classes[$c2];
		$mediator = $cls[1];		
		$cls[1] = $cls[0];		
		$cls[0] = $mediator;		
		return $cls;
	}
	
	/*
	|--------------------------------------------------------------------------
	| Double Classroom Reselect
	|--------------------------------------------------------------------------
	|	This function picks two classes randomly and swaps all the courses within both classes
	|	Both classes have to be of same duration
	*/
	public function class_reselect(){
		$rnds = $this->randClass();
		$new_clss_hrs = $this->swapClasses($rnds[0],$rnds[1]);	
		
		$this->_classes[$rnds[0]] = $new_clss_hrs[0];
		$this->_classes[$rnds[1]] = $new_clss_hrs[1];
		
		$this->_sched = $this->map_class_to_hours($this->_classes);
		
	}
	
	private function fill_arr($time,$duration,$content){
		$keys = array();
		$t = $time;
		for($i=0;$i<$duration;$i++){
			$keys[] = $t++;
		}
		return array_fill_keys($keys,$content);
	}
	
	/*
	|--------------------------------------------------------------------------
	| Hour Reselect
	|--------------------------------------------------------------------------
	|	This function takes two scheduled hours in two different classrooms and swaps the courses within these hours. 
	|	Both classes have to be of same duration
	*/
	public function hour_reselect(){			
		$rnds = $this->randClass();				
		$cls = array(0,0);
		
		do{			//while the duration of both classes are not equal			
			$cls[0] = array_rand($this->_sched[$rnds[0]],1);
			$cls[1] = array_rand($this->_sched[$rnds[1]],1);	
			if($this->_sched[$rnds[0]][$cls[0]]->getDuration() == $this->_sched[$rnds[1]][$cls[1]]->getDuration()){					
				break;
			}
		}while($this->_sched[$rnds[0]][$cls[0]]->getDuration() != $this->_sched[$rnds[1]][$cls[1]]->getDuration());
		
		for($i=0; $i<2; $i++){
			$sel_cls_time[] = $this->_classes[$rnds[$i]][$cls[$i]];	//Select the lectures within the two selected hours
		}
		$cls_duration = $this->_classes[$rnds[0]][$cls[0]]->getDuration();
		$clss_swapperA = array();
		$clss_swapperB = array();				//We are getting duration,since same duration any lecture can be used
		$clss_swapperA = $this->fill_arr($cls[0],$cls_duration,$sel_cls_time[1]);	//Swap the classes between the two selected times
		$clss_swapperB = $this->fill_arr($cls[1],$cls_duration,$sel_cls_time[0]);	//Swap the classes between the two selected times
	
		$proxy_cls = array_replace($this->_classes[$rnds[0]],$clss_swapperA);//use array replace fxn to swap lectures at times in array
		$this->_classes[$rnds[0]] = $proxy_cls;		
		$proxy_cls = array_replace($this->_classes[$rnds[1]],$clss_swapperB);
		$this->_classes[$rnds[1]] = $proxy_cls;			
		
		$this->_sched = $this->map_class_to_hours($this->_classes);	
	}
	
	/*
	|--------------------------------------------------------------------------
	| Single Classroom reselect
	|--------------------------------------------------------------------------
	|	This function takes two classes within same classroom and swaps them
	|	Both classes have to be of same duration
	*/
	public function single_classroom_reselect($sch=''){		
		$rnds = $this->randClass();		//Generate two random classrooms
		$cls = array(0,0);
		
		do{	//while the duration of both classes are not equal			
				$cls[0] = array_rand($this->_sched[$rnds[0]],1);		//Generate a random hour within classroom
				$cls[1] = array_rand($this->_sched[$rnds[0]],1);		//Generate a random hour within classroom
				if($cls[0]!=$cls[1]){		//if both hours are not thesame
					if($this->_sched[$rnds[0]][$cls[0]]->getDuration() == $this->_sched[$rnds[0]][$cls[1]]->getDuration()){	//and the duration are thesame					
						break;
					}
				}
		}while(1); //create an infinite loop and only break when conditions are met
		
		for($i=0; $i<2; $i++){	//Select the lectures within the two selected hours
			$sel_cls_time[] = $this->_classes[$rnds[0]][$cls[$i]];
		}
		//$this->_classes[$rnds[0]][$cls[1]]->getDuration()
		$cls_duration = $this->_classes[$rnds[0]][$cls[0]]->getDuration();
		$clss_swapperA = array();
		$clss_swapperB = array();
									//First hour  //get the duration of this class			//Get the course itself
		$clss_swapperA = $this->fill_arr($cls[0],$cls_duration,$sel_cls_time[1]);	//Swap the classes between the two selected times
		$clss_swapperB = $this->fill_arr($cls[1],$cls_duration,$sel_cls_time[0]);
		
		$proxy_cls = array_replace($this->_classes[$rnds[0]],$clss_swapperA);
		$this->_classes[$rnds[0]] = $proxy_cls;
		$proxy_cls = array_replace($this->_classes[$rnds[0]],$clss_swapperB);
		$this->_classes[$rnds[0]] = $proxy_cls;
		
		$this->_sched = $this->map_class_to_hours($this->_classes);			
	}
	
	/*
	|--------------------------------------------------------------------------
	| Mutation function
	|--------------------------------------------------------------------------
	|	The mutation function takes two random classes and swaps them
	*/
	public function mutation(){
		$rnds = $this->randClass();		//Generate two random classrooms
		$cls = array(0,0);
		
		do{	//while the duration of both classes are not equal			
				$cls[0] = array_rand($this->_sched[$rnds[0]],1);		//Generate a random hour within classroom
				$cls[1] = array_rand($this->_sched[$rnds[1]],1);		//Generate a random hour within classroom
				if($cls[0]!=$cls[1]){		//if both hours are not thesame
					if($this->_sched[$rnds[0]][$cls[0]]->getDuration() == $this->_sched[$rnds[1]][$cls[1]]->getDuration()){	//and the duration are thesame					
						break;
					}
				}
		}while(1); //create an infinite loop and only break when conditions are met
		
		$dur = $this->_sched[$rnds[0]][$cls[0]]->getDuration();
		$new_time = array(0,0);
		$sel_cls_time = array();
		for($i=0;$i<2;$i++){
			$time = 0;
			do{
				$time = rand()%60;
				if(!is_object($this->_classes[$rnds[$i]][$time])){
					break;
				}
			}while(is_object($this->_classes[$rnds[$i]][$time]));
			$new_time[$i] = $time;
		}
		
		for($i=0; $i<2; $i++){
			$sel_cls_time[] = $this->_classes[$rnds[$i]][$cls[$i]];
		}
		
		for($i=0;$i<2;$i++){
			$buff = $this->_classes[$rnds[$i]];
			$test = array_fill(0,$dur,$sel_cls_time[abs($i-1)]);//This little hack inverts 1 to 0 and 0 to 1		
			array_splice($buff,$new_time[$i],0,$test);
			$this->_classes[$rnds[$i]] = $buff;	
		}
		for($i=0;$i<2;$i++){	//Remove the content of moved class
			$sel_cls_time[$i] = $this->fill_arr($cls[$i],$dur,0);
			array_replace($this->_classes[$rnds[$i]],$sel_cls_time[$i]);
		}
		$this->_sched = $this->map_class_to_hours($this->_classes);	
	}

	public function do_crossover(){
		$this->class_reselect();
		$this->hour_reselect();		
		$this->single_classroom_reselect();		
	}
}
?>