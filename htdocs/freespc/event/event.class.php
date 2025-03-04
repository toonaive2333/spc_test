<?php
require_once('../load.php');

function cmp($x, $y){  
 	return strcmp($y["time"], $x["time"]);  
}

class Event{
	private $EVENT_ID;
	private $db;
	private $tasks;
	private $expiredTasks;
	private $notExpiredTasks;
	private $reports;
	private $meetings;
	private $files;
	
	public function __construct($id){
		global $wpdb;
		$this->db = $wpdb;
		$this->EVENT_ID = $id;
		$this->tasks = array();
		$this->expiredTasks = array();
		$this->notExpiredTasks = array();
	}
//-----------tasks----------------------------
	public function setTasks(){
		$r = $this->db->get_results("SELECT * FROM tasks WHERE event=".$this->EVENT_ID." ORDER BY lastTime ASC", ARRAY_A);
		$today = strtotime(date("Y-m-d H:i:s"));
		if(is_array($r)){
			foreach($r as $task){
				$expired = 0;
				if($task['expiration']){
					$expiration = strtotime($task['expiration'])+24*3600;
					if($today > $expiration && $task['complete'] == 0){
						$expired = 1;
						$expiration = ceil(($today-$expiration)/24/3600);
					}
				}
				$this->tasks[] = array(	
										'time'=>strtotime($task['lastTime']),
										'type'=>'task',
										'title'=>$task['title'],
										'id'=>$task['id'],
										'updater'=>$task['updater'],
										'lastTime'=>$task['lastTime'],
										'detail'=>array(
														 'createTime'=>$task['createTime'],
														 'complete'=>$task['complete'],
														 'expiration'=>$expiration."天",
														 'expireTime'=>date("Y/m/d",strtotime($task['expiration'])),
														 'expired'=>$expired,
														 'responser'=>$task['responser']
														)
									  );
			}
		}
		if(is_array($this->tasks ))
			$this->tasks = $this->sortByTime($this->tasks);
	}	
	public function getTasks(){
		return $this->tasks; 
	}
	
	public function setExpiredTasks(){
		$temp = $this->tasks;
		foreach($temp as $key=>$task){
			if($task['detail']['expired'] > 0 ){
				$this->expiredTasks[] = $task;
			}else{
				$this->notExpiredTasks[] = $task;
			}
		}
		if(is_array($this->expiredTasks ))
			$this->expiredTasks = $this->sortByExpiration($this->expiredTasks);
	}
	public function getExpiredTasks(){
		return $this->expiredTasks; 
	}
	public function getNotExpiredTasks(){
		return $this->notExpiredTasks; 
	}
	
	private function sortByTime($a){		
		usort($a,"cmp");
		return $a;
	}
	private function sortByExpiration($a){
		function cmp2($x, $y){  
    		return strcmp($y["detail"]['expiration'], $x["detail"]['expiration']); 
 		}
		usort($a,"cmp2");
		return $a;
	}
//-----------reports----------------------------
	public function setReports(){
		$r = $this->db->get_results("SELECT * FROM reports WHERE event=".$this->EVENT_ID." ORDER BY lastTime ASC", ARRAY_A);
		if(is_array($r)){
			foreach($r as $report){				
				$this->reports[] = array(	
										'time'=>strtotime($report['lastTime']),
										'type'=>'report',
										'title'=>$report['title'],
										'id'=>$report['id'],
										'updater'=>$report['updater'],
										'lastTime'=>$report['lastTime'],
										'detail'=>array()
									  );
			}
		}
		
		if(is_array($this->reports ))
			$this->reports = $this->sortByTime($this->reports);
	}	
	public function getReports(){
		return $this->reports; 
	}
//-----------meetings----------------------------
	public function setMeetings(){
		$r = $this->db->get_results("SELECT * FROM meetings WHERE event=".$this->EVENT_ID." ORDER BY lastTime ASC", ARRAY_A);
		if(is_array($r)){
			foreach($r as $meeting){				
				$this->meetings[] = array(	
										'time'=>strtotime($meeting['lastTime']),
										'type'=>'meeting',
										'title'=>$meeting['title'],
										'id'=>$meeting['id'],
										'updater'=>$meeting['updater'],
										'lastTime'=>$meeting['lastTime'],
										'detail'=>array()
									  );
			}
		}
		if(is_array($this->meetings ))
			$this->meetings = $this->sortByTime($this->meetings);
	}	
	public function getMeetins(){
		return $this->meetings; 
	}
//-----------files----------------------------
	public function setFiles(){
		$r = $this->db->get_results("SELECT * FROM files WHERE event=".$this->EVENT_ID." ORDER BY lastTime ASC", ARRAY_A);
		if(is_array($r)){
			foreach($r as $file){				
				$this->files[] = array(	
										'time'=>strtotime($file['lastTime']),
										'type'=>'file',
										'title'=>$file['fileName'],
										'id'=>$file['id'],
										'updater'=>$file['updater'],
										'lastTime'=>$file['lastTime'],
										'detail'=>array()
									  );
			}
		}
		if(is_array($this->files ))
			$this->files = $this->sortByTime($this->files);
	}	
	public function getFiles(){
		return $this->files; 
	}
//-----------mix----------------------------
	public function getMix(){
		if(is_array($this->notExpiredTasks)){
			$mix = $this->notExpiredTasks;
			if(is_array($this->reports))
				$mix = array_merge($mix,$this->reports);
			if(is_array($this->meetings))
				$mix = array_merge($mix,$this->meetings);
			if(is_array($this->files))
				$mix = array_merge($mix,$this->files);
		}elseif(is_array($this->reports)){
			$mix = $this->reports;
			if(is_array($this->meetings))
				$mix = array_merge($mix,$this->meetings);
			if(is_array($this->files))
				$mix = array_merge($mix,$this->files);
		}elseif(is_array($this->meetings)){
			$mix = $this->meetings;
			if(is_array($this->files))
				$mix = array_merge($mix,$this->files);
		}elseif(is_array($this->files)){
			$mix = $this->files;
		}
		if(is_array($mix))
			$mix = $this->sortByTime($mix);
		return $mix; 
	}
}
?>