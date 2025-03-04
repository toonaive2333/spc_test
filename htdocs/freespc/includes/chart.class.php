<?php
define('ABSPATH', dirname(dirname(__FILE__)).'/');
require_once(ABSPATH."includes/sendmail.php");

class Chart{
	private $CHART_ID;
	private $configer;
	private $db;
	private $parameters;
	
	public function __construct($id,$exist=false){
		global $wpdb;
		$this->db = $wpdb;
		$this->CHART_ID = $id;
		$this->configer = new Config_xml($id,$exist);
		if($exist)
			$this->parameters = $this->getParameters();
	}
	
	public function createChart($args){
		if($args['type']<TYPE_P){//XR,XS,IMR
			$sampleSize = $args['sample_size'];		
			$sql_create = "CREATE TABLE chart_";
			$sql_create = $sql_create . $this->CHART_ID;
			$sql_create = $sql_create . "(id int(10) UNSIGNED not null AUTO_INCREMENT,primary key(id),";
			for($i = 0;$i<$sampleSize;$i++){
			  $sql_create = $sql_create . "x_" . ($i + 1) . " float not null default 0,";
			  $sql_create = $sql_create . "product_" . ($i + 1) . " varchar(40),"; //40 characters as product id
			}
			if($args['type']!=TYPE_IMR)
				$sql_create = $sql_create . "xbar float,"; //average of datas
			//$sql_create = $sql_create . "cl float,";
			$sql_create = $sql_create . "ucl float,";
			$sql_create = $sql_create . "lcl float,";
			$sql_create = $sql_create . "ucl_2 float,";
			$sql_create = $sql_create . "stat_value float,"; //stat_value in Xbar-R chart means R-value,in Xbar-S means S-value,in I-MR means MR
			$sql_create = $sql_create . "status int(1) not null default 0,"; //0:normal point,1:bad point,2:normal point with comment,3:bad point with comment
			$sql_create = $sql_create . "against int(1) not null default 0,"; //which rule is it against 
			$sql_create = $sql_create . "remark text not null,"; //comment
			$sql_create = $sql_create . "data_time datetime not null,"; //product time
			$sql_create = $sql_create . "INDEX (data_time),"; 
			$sql_create = $sql_create . "record_time datetime not null)"; //record time
		}else{
			$sql_create = "CREATE TABLE chart_";
			$sql_create = $sql_create . $this->CHART_ID;
			$sql_create = $sql_create . "(id int(10) UNSIGNED not null AUTO_INCREMENT,primary key(id),";  
			$sql_create = $sql_create . "ng_count int(10) not null default 0,";//P,NP,U,C
			//more parameters for P and U
			if( $args['type']==TYPE_P || $args['type']==TYPE_NP || $args['type']==TYPE_U ){
				$sql_create = $sql_create . "total_count int(10) not null default 0,";//batch count
				$sql_create = $sql_create . "rate float,";//=ng_count/total_count				
			}
			$sql_create = $sql_create . "cl float,";
			$sql_create = $sql_create . "ucl float,";
			$sql_create = $sql_create . "lcl float,";
			$sql_create = $sql_create . "batch varchar(40),"; //40 characters as batch id
			$sql_create = $sql_create . "status int(1) not null default 0,"; //0:normal point,1:bad point,2:normal point with comment,3:bad point with comment
			$sql_create = $sql_create . "against int(1) not null default 0,"; //which rule is it against
			$sql_create = $sql_create . "remark text not null,"; //comment
			$sql_create = $sql_create . "data_time datetime not null,"; //batch time
			$sql_create = $sql_create . "INDEX (data_time),";
			$sql_create = $sql_create . "record_time datetime not null)"; //record time
		}
		// 添加至 charts.xml
		$this->configer->addChart($args);
		//echo $sql_create;
		$this->db->query($sql_create);
  	}
	
	public function record($args){
		try{
			$against = 0;
			$data = array();
			if($this->parameters['type']<TYPE_P){//XR,XS,IMR
				$values = $args['values'];
				$ids = $args['ids'];
				$i=1;
				for(;$i<=$this->parameters['sample_size'];){
					$data = $data+array("x_$i"=>$values[$i-1]);
					$i++;
				}
				
				if(!empty($ids)){
					$i=1;
					for(;$i<=$this->parameters['sample_size'];){
						$data = $data+array("product_$i"=>$ids[$i-1]);
						$i++;
					}
				}
				$data = $data+array("ucl"=>$this->parameters['ucl_x'],
									"lcl"=>$this->parameters['lcl_x'],
									//"cl"=>(($this->parameters['ucl_x']+$this->parameters['lcl_x'])/2),
									"ucl_2"=>$this->parameters['ucl_2']
									);
				
				switch($this->parameters['type']){
					case TYPE_XR:
						$xbar = $this->getXbar($values);
						$r = max($values)-min($values);					
						$data = $data+array("xbar"=>$xbar,"stat_value"=>$r);					
						$against = $this->getAgainst(array("xbar"=>$xbar,"stat_value"=>$r));
					break;
					case TYPE_XS:
						$xbar = $this->getXbar($values);
						$s = $this->getStd($values);
						$data = $data+array("xbar"=>$xbar,"stat_value"=>$s);					
						$against = $this->getAgainst(array("xbar"=>$xbar,"stat_value"=>$s));
					break;
					case TYPE_IMR:
						$mr = $this->getMR($values[0]);
						if($mr != -1){
							$data = $data+array("stat_value"=>$mr);						
						}else{
							$mr = 0;//for getAgainst() function
						}
						$against = $this->getAgainst(array("xbar"=>$values[0],"stat_value"=>$mr));
					break;				
				}			
			}else{//P,NP,U,C
				switch($this->parameters['type']){
					case TYPE_P:
					case TYPE_NP:
					case TYPE_U:
						$ng_count = $args['value'];
						$total_count = $args['sampleSize'];						
						$data = $data+array("ng_count"=>$args['value'],"total_count"=>$args['sampleSize'],"batch"=>$args['ids']);
						
						$rate = $ng_count/$total_count;	
						$cl = $this->parameters['cl'];
						if($this->parameters['type'] == TYPE_U)
							$temp = sqrt($cl/$total_count);
						elseif($this->parameters['type'] == TYPE_NP)
							$temp = sqrt($cl*(1-$cl/$total_count));
						else
							$temp = sqrt($cl*(1-$cl)/$total_count);
						$ucl = $cl+3*$temp;
						$lcl = $cl-3*$temp;
						if($lcl<0)
							$lcl = 0;
						$data = $data+array("rate"=>$rate,"ucl"=>$ucl,"lcl"=>$lcl,"cl"=>$cl);
						
						if($this->parameters['type'] == TYPE_NP)//for $this->getAgainst function 
							$rate = $ng_count;
						
						$against = $this->getAgainst(array("rate"=>$rate,"ucl"=>$ucl,"lcl"=>$lcl));
					break;	
					case TYPE_C:				
						$data = $data+array("ng_count"=>$args['value'],"batch"=>$args['ids']);
							
						$cl = $this->parameters['cl'];
						$temp = sqrt($cl);
						$ucl = $cl+3*$temp;
						$lcl = $cl-3*$temp;
						if($lcl<0)
							$lcl = 0;
						$data = $data+array("ucl"=>$ucl,"lcl"=>$lcl,"cl"=>$cl);
						
						$against = $this->getAgainst(array("rate"=>$args['value'],"ucl"=>$ucl,"lcl"=>$lcl));					
					break;
				}			
			}
			if($against != 0){
				$data = $data+array("against"=>$against,"status"=>1);
				$this->failedMail($data);
			}
			$data = $data+array('data_time'=>date('y-m-d H:i:s'),'record_time'=>date('y-m-d H:i:s'));
			$this->db->insert("chart_".$this->CHART_ID,$data);
			return 1;
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	//send a mail when tast failed
	public function failedMail($data){
		$mailList = "";
		$teamId = $this->db->get_var("SELECT team FROM charts WHERE id=".$this->CHART_ID);
		$members = $this->db->get_col("SELECT members.email FROM members,members_team WHERE members_team.team_id=$teamId AND members.id=members_team.member_id");
		if(is_array($members)){
			foreach($members as $member)
				$mailList = $mailList.$member.";";
		}else return;
		
		$subject = "(Chart)";
		$subject .= $this->parameters['name'];
		$subject .= $this->encode("有OOC报警，请关注。");
		
		$body = $this->encode("Chart：");
		$body .= $this->parameters['name']."\n";
		$body .= $this->encode("数据时间：".date('Y/m/d H:i:s')."\n");
		$against = $data['against'];
		if($against == 9) $against = 1;
		$body .= $this->encode("违反规则：Rule".$against."\n");
		$body .= $this->encode("原始数据：\n");		
		
		for($i=1;$i<=$this->parameters['sample_size'];){
			$body .= "Data".$i.":".$data["x_$i"]."\n";
			$i++;
		}				
		sendmail($mailList, $subject, $body, '');
	}
	
	//for X chart
	private function getXbar($values){
		$sum=0;
		for($i=0;$i<$this->parameters['sample_size'];$i++){
			$sum += $values[$i];
		}
		return($sum/$this->parameters['sample_size']);		
	}
	
	//for XS chart
	private function getStd($values){
		$avg = $this->getXbar($values);		
		for($i=0;$i<$this->parameters['sample_size'];$i++){
			$S += pow(($values[$i]-$avg),2);
		}
		$s = sqrt($S/($this->parameters['sample_size']-1));
		return $s;
	}
	
	//for IMR Chart
	private function getMR($value){
		//previous data value
		$prev = $this->db->get_var("SELECT x_1 FROM chart_".$this->CHART_ID." ORDER BY id DESC LIMIT 1");
		if( is_numeric($prev) )
			return(abs($prev-$value));
		else
			return -1;
	}
	
	//if against certain rules
	private function getAgainst($args){
		$against = 0;
		$rules = explode('|',$this->parameters['rules']);
	
		//check rule 1
		if(in_array(1,$rules)){			
			$against = $this->rule1($args);
		}
		//check rule 2
		if($against != 0)
			return $against;
		if(in_array(2,$rules)){			
			$against = $this->rule2($args);
		}
		//check rule 3
		if($against != 0)
			return $against;
		if(in_array(3,$rules)){			
			$against = $this->rule3($args);
		}
		//check rule 4
		if($against != 0)
			return $against;
		if(in_array(4,$rules)){			
			$against = $this->rule4($args);
		}
		//check rule 5
		if($against != 0)
			return $against;
		if(in_array(5,$rules)){			
			$against = $this->rule5($args);
		}
		//check rule 6
		if($against != 0)
			return $against;
		if(in_array(6,$rules)){			
			$against = $this->rule6($args);
		}
		//check rule 7
		if($against != 0)
			return $against;
		if(in_array(7,$rules)){			
			$against = $this->rule7($args);
		}
		//check rule 8
		if($against != 0)
			return $against;
		if(in_array(8,$rules)){			
			$against = $this->rule8($args);
		}
		
		return $against;
	}
	
	//rule1:1个点距离中心线大于3个标准差(即超出控制限) 
	private function rule1($args){
		if($this->parameters['type']<TYPE_P){//args包含了xbar和sub chart的值
			$ucl = $this->parameters['ucl_x'];
			$lcl = $this->parameters['lcl_x'];
			$ucl_2 = $this->parameters['ucl_2'];
			
			if($args['xbar']>$ucl || $args['xbar']<$lcl){
				return 1;
			}
			if($args['stat_value']>$ucl_2){
				return 9;//sub chart against
			}
		}else{			
			if($args["rate"]<$args["lcl"] || $args["rate"]>$args["ucl"])
				return 1;			
		}
		return 0 ;
	}
	//rule 2:连续9点在中心线同一侧
	private function rule2($args){
		$cl = $this->parameters['cl'];
		$v = $args['rate'];//point value
		$fieldInDB = "";
		if($this->parameters['type']<TYPE_P){//args包含了xbar和sub chart的值
			$ucl = $this->parameters['ucl_x'];
			$lcl = $this->parameters['lcl_x'];
			$cl = ($ucl+$lcl)/2;
			$v = $args['xbar'];
			$fieldInDB = 'xbar';
			if($this->parameters['type']==TYPE_IMR)
				$fieldInDB = 'x_1';
		}elseif($this->parameters['type']==TYPE_P || $this->parameters['type']==TYPE_U){
			$fieldInDB = 'rate';
		}elseif($this->parameters['type']==TYPE_NP || $this->parameters['type']==TYPE_C){
			$fieldInDB = 'ng_count';
		}
		//not against
		if($v==$cl)
			return 0;		
		//前8个点
		$prev8 = $this->db->get_col("SELECT $fieldInDB FROM chart_".$this->CHART_ID." ORDER BY id DESC LIMIT 8");
		//do not check
		if(count($prev8)<8)
			return 0;
		//连续9点< CL
				
		if($v<$cl){
			foreach($prev8 as $value){
				if($value>=$cl)
					return 0;
			}
		}		
		//连续9点> CL	
		if($v>$cl){
			foreach($prev8 as $value){
				if($value<=$cl)
					return 0;
			}
		}
				
		return 2;
	}
	//rule 3:连续6个点，全部递增或全部递减
	private function rule3($args){
		$v = $args['rate'];//point value
		if($this->parameters['type']<TYPE_P){//args包含了xbar和sub chart的值
			$v = $args['xbar'];
			$fieldInDB = 'xbar';
			if($this->parameters['type']==TYPE_IMR)
				$fieldInDB = 'x_1';
		}elseif($this->parameters['type']==TYPE_P || $this->parameters['type']==TYPE_U){
			$fieldInDB = 'rate';
		}elseif($this->parameters['type']==TYPE_NP || $this->parameters['type']==TYPE_C){
			$fieldInDB = 'ng_count';
		}
		$prev5 = $this->db->get_col("SELECT $fieldInDB FROM chart_".$this->CHART_ID." ORDER BY id DESC LIMIT 5");
		//do not check
		if(count($prev5)<5)
			return 0;
		//not against
		if($v==$prev5[0])
			return 0;
		
			
		//连续下降	
		if($v<$prev5[0]){
			for($i=0;$i<4;$i++){
				if($prev5[$i]>=$prev5[$i+1])
					return 0;
			}
		}		
		//连续上升
		if($v>$prev5[0]){
			for($i=0;$i<4;$i++){
				if($prev5[$i]<=$prev5[$i+1]){					
					return 0;
				}
			}
		}
		
		return 3;
	}
	//rule 4:连续 14个点，上下交错
	private function rule4($args){
		$v = $args['rate'];//point value
		if($this->parameters['type']<TYPE_P){//args包含了xbar和sub chart的值
			$v = $args['xbar'];
			$fieldInDB = 'xbar';
			if($this->parameters['type']==TYPE_IMR)
				$fieldInDB = 'x_1';
		}elseif($this->parameters['type']==TYPE_P || $this->parameters['type']==TYPE_U){
			$fieldInDB = 'rate';
		}elseif($this->parameters['type']==TYPE_NP || $this->parameters['type']==TYPE_C){
			$fieldInDB = 'ng_count';
		}
		$prev13 = $this->db->get_col("SELECT $fieldInDB FROM chart_".$this->CHART_ID." ORDER BY id DESC LIMIT 13");
		//do not check
		if(count($prev13)<13)
			return 0;
		//not against
		if($v==$prev13[0])
			return 0;		
		
		$flag = -1;	
		if($v>$prev13[0])
			$flag = 1;		
			
		for($i=0;$i<12;$i++){
			if($flag == -1 && $prev13[$i]<=$prev13[$i+1]){
				return 0;
			}elseif($flag == 1 && $prev13[$i]>=$prev13[$i+1]){
				return 0;
			}
			$flag = -1*$flag;
		}
				
		return 4;
	}
	//rule 5:3个点中有2个点，距离中心线（同侧）大于2个标准差
	private function rule5($args){		
		$v = $args['xbar'];	
		$ucl = $this->parameters['ucl_x'];
		$lcl = $this->parameters['lcl_x'];
		$cl = ($ucl+$lcl)/2;	
		$sigma2Up = ($cl+$ucl*2)/3;//+2个标准差
		$sigma2Low = ($cl+$lcl*2)/3;//-2个标准差
		$fieldInDB = 'xbar';
		if($this->parameters['type']==TYPE_IMR)
				$fieldInDB = 'x_1';
		$prev2 = $this->db->get_col("SELECT $fieldInDB FROM chart_".$this->CHART_ID." ORDER BY id DESC LIMIT 2");
		//do not check	
		if(count($prev2)<2)
			return 0;
		if($v<=$sigma2Up && $v>=$sigma2Low)	
			return 0;
		
		if($v>$sigma2Up){
			if($prev2[0]>$sigma2Up || $prev2[1]>$sigma2Up)
				return 5;
		}
		if($v<$sigma2Low){
			if($prev2[0]<$sigma2Low || $prev2[1]<$sigma2Low)
				return 5;
		}
				
		return 0;
	}
	//rule 6:5个点中有4个点，距离中心线（同侧）大于1个标准差
	private function rule6($args){		
		$v = $args['xbar'];	
		$ucl = $this->parameters['ucl_x'];
		$lcl = $this->parameters['lcl_x'];
		$cl = ($ucl+$lcl)/2;	
		$sigma1Up = ($cl*2+$ucl)/3;//+1个标准差
		$sigma1Low = ($cl*2+$lcl)/3;//-1个标准差
		$fieldInDB = 'xbar';
		if($this->parameters['type']==TYPE_IMR)
				$fieldInDB = 'x_1';
		$prev4 = $this->db->get_col("SELECT $fieldInDB FROM chart_".$this->CHART_ID." ORDER BY id DESC LIMIT 4");
		//do not check	
		if(count($prev4)<4)
			return 0;
		if($v<=$sigma1Up && $v>=$sigma1Low)	
			return 0;
					
		$j = 0;
		if($v>$sigma1Up){			
			for($i=0;$i<4;$i++){
				if($prev4[$i]<=$sigma1Up)
					$j++;
			}
		}
		if($v<$sigma1Low){
			for($i=0;$i<4;$i++){
				if($prev4[$i]>=$sigma1Low)
					$j++;
			}
		}
		if($j > 1){			
			return 0;
		}			
		return 6;
	}
	//rule 7:连续15个点，距离中心线（任一侧）1个标准差以内
	private function rule7($args){		
		$v = $args['xbar'];	
		$ucl = $this->parameters['ucl_x'];
		$lcl = $this->parameters['lcl_x'];
		$cl = ($ucl+$lcl)/2;	
		$sigma1 = ($cl*2+$ucl)/3;//+1个标准差
		$sigma2 = ($cl*2+$lcl)/3;//-1个标准差
		
		$fieldInDB = 'xbar';
		if($this->parameters['type']==TYPE_IMR)
				$fieldInDB = 'x_1';
		$prev14 = $this->db->get_col("SELECT $fieldInDB FROM chart_".$this->CHART_ID." ORDER BY id DESC LIMIT 14");
		//do not check	
		if(count($prev14)<14)
			return 0;
		if($v>=$sigma1 || $v<=$sigma2)	
			return 0;
					
		foreach($prev14 as $value){
			if($value>=$sigma1 || $value<=$sigma2)
				return 0;
		}
					
		return 7;
	}
	//rule 8:8连续8个点，距离中心线（任一侧）大于1个标准差
	private function rule8($args){		
		$v = $args['xbar'];	
		$ucl = $this->parameters['ucl_x'];
		$lcl = $this->parameters['lcl_x'];
		$cl = ($ucl+$lcl)/2;	
		$sigma1 = ($cl*2+$ucl)/3;//1个标准差
		$sigma2 = ($cl*2+$lcl)/3;//-1个标准差
		
		$fieldInDB = 'xbar';
		if($this->parameters['type']==TYPE_IMR)
				$fieldInDB = 'x_1';
		$prev7 = $this->db->get_col("SELECT $fieldInDB FROM chart_".$this->CHART_ID." ORDER BY id DESC LIMIT 7");
		//do not check	
		if(count($prev7)<7)
			return 0;
		if($v<=$sigma1 && $v>=$sigma2)	
			return 0;
					
		foreach($prev7 as $value){
			if($value<=$sigma1 && $value>=$sigma2)
				return 0;
		}
					
		return 8;
	}
	
	//delete chart
	public function deleteSelf(){
		$sql_drop = "DROP TABLE chart_".$this->CHART_ID;
		$this->db->query($sql_drop);
		$sql_delete = "DELETE FROM charts WHERE id=".$this->CHART_ID;
		$this->db->query($sql_delete);
		$this->parameters = $this->configer->getParameters();
		$sql_delete = "UPDATE teams SET charts=charts-1 WHERE id=".$this->parameters['team'];
		$this->db->query($sql_delete);
		$this->configer->deleteSelf();
	}
	
	public function getParameters(){
		return $this->configer->getParameters();
	}
	
	public function getLogs(){
		return $this->configer->getLogs();
	}
	
	public function updateChart($ags){
		return $this->configer->updateChart($ags);
	}
	
	public function checkExist(){
		return $this->configer->exist();
	}
	
	public function addRemark($dataID, $remark){
		$sql_update = "update " . $this->TABLE_NAME;
		$sql_update = $sql_update . " set remark=concat(remark,";
		$sql_update = $sql_update . "'" . $remark . "'),status=status+2 where id=$dataID";
	
		$this->db->query($sql_update);
 	}
  // public abstract function getData();
  // public abstract function record();
    private function encode($source){
   		return mb_convert_encoding($source, 'utf-8', 'gb2312');
    }
}


class Config_xml{
  private $dom;
  private $fileName;
  private $chartId;
  private $parameters;
  private $exist=false;  

  public function __construct($id,$exist=false){
  	if ( file_exists(ABSPATH.'charts.xml') ) 
		$this->fileName = ABSPATH.'charts.xml';
	elseif ( file_exists(dirname(ABSPATH) . '/charts.xml') )
		$this->fileName = dirname(ABSPATH) . '/charts.xml';
	else
		tx_die($this->encode("找不到<code>charts.xml</code>文件，若您误删除该文件，您可以从<code>charts-sample.xml</code>复制。"));
		
 	$this->chartId = $id;
    $this->dom = new DOMDocument();
    $this->dom->load($this->fileName);
	
	if($exist){
		//its not the creating case
		$charts = $this->dom->getElementsByTagName("chart");
		$this->parameters = new DOMElement('parameters');
		foreach($charts as $chart){
			if($chart->getAttribute('id') == $this->chartId){
				$this->parameters = $chart;
				$this->exist = true;
				break;
			}
		}
	}
  }
  
  public function exist(){
  	return $this->exist;
  }

  public function addChart($args){
    $root = $this->dom->getElementsByTagName('freespc')->item(0);

    $chart = $this->dom->createElement('chart');	
    $chart->setAttribute('id', $this->chartId);
    $chart->setAttribute('name', $args['name']);
	$chart->setAttribute('team', $args['team']);
	$chart->setAttribute('team_name', $args['team_name']);
    $chart->setAttribute('type', $args['type']); //XR,XS,IMR,P,UP,U,C	
	
	$description = $this->dom->createElement('description', $args['description']); //description
	$fileName = $this->dom->createElement('fileName', $args['fileName']);
	$linkName = $this->dom->createElement('linkName', $args['linkName']);
    $chart->appendChild($description);
	$chart->appendChild($fileName);
	$chart->appendChild($linkName);

    switch ($args['type']) { //special parameters for different type
      case TYPE_XR:
      case TYPE_XS:
	  case TYPE_IMR:
        $sample_size = $this->dom->createElement('sample_size', $args['sample_size']); //sample size
        $chart->appendChild($sample_size);
        $ucl_x = $this->dom->createElement('ucl_x', $args['ucl_x']); //UCL
        $chart->appendChild($ucl_x);
        $lcl_x = $this->dom->createElement('lcl_x', $args['lcl_x']); //LCL
        $chart->appendChild($lcl_x);
        $ucl_2 = $this->dom->createElement('ucl_2', $args['ucl_2']); //UCL of R chart or S chart or MR chart
        $chart->appendChild($ucl_2);
		$usl = $this->dom->createElement('usl', $args['usl']); //USL of X chart
        $chart->appendChild($usl);
		$lsl = $this->dom->createElement('lsl', $args['lsl']); //LSL of X chart
        $chart->appendChild($lsl);
      break;
	  case TYPE_NP:
	  	$sample_size = $this->dom->createElement('sample_size', $args['sample_size']); //sample size just for NP chart
        $chart->appendChild($sample_size);
	  case TYPE_P:
      case TYPE_U:	  
	  case TYPE_C:
	  	$cl = $this->dom->createElement('cl', $args['cl']); //Center Line
        $chart->appendChild($cl);
	  break;
    }

    $rules = $this->dom->createElement('rules', $args['rules']); //rules,e.g.:1,3,8
    $chart->appendChild($rules);
    $logs = $this->dom->createElement('logs'); //logs of chart:create,modify
	$log = $this->dom->createElement('log');
	$log->setAttribute('time',date('y-m-d H:i:s'));
	$log->setAttribute('operator',$_COOKIE['login_name']);
	$log->setAttribute('operation',$this->encode("创建Chart"));
    //add a create log when creating
    $logs->appendChild($log);
    $chart->appendChild($logs);

    $root->appendChild($chart);
    $this->dom->save($this->fileName);
    return true;
  }
  
  public function updateChart($args){
  	global $wpdb;
	$operation = "更新Chart";
  	$log = "";	
    $root = $this->parameters;
	if($root->getAttribute('name') != $args['name']){
		$log .= "Chart名称由[".$this->decode($root->getAttribute('name'))."]改为[".$this->decode($args['name'])."];<br>";
		$root->setAttribute('name',$args['name']);		
	}
	if($root->getAttribute('team') != $args['team']){
		$log .= "责任Team由[".$this->decode($root->getAttribute('team_name'))."]改为[".$this->decode($args['team_name'])."];<br>";
		$root->setAttribute('team',$args['team']);
		$root->setAttribute('team_name',$args['team_name']);
	}
	
	$childNodes = $root->childNodes;
	foreach($childNodes as $childNode){
		$nodeName = $childNode->nodeName;
		if($nodeName == 'description' || $nodeName == 'fileName' || $nodeName == 'linkName'){			
			$childNode->nodeValue = $args[$nodeName];
		}else{
			if($nodeName != 'logs'){
				if($childNode->nodeValue != $args[$nodeName]){
					$temp = $childNode->nodeValue;
					$childNode->nodeValue = $args[$nodeName];
					if($nodeName == 'rules')
						$log .= $nodeName."由[".substr($temp,2)."]改为[".substr($args[$nodeName],2)."];<br>";
					else
						$log .= $nodeName."由[$temp]改为[".$args[$nodeName]."];<br>";
				}
			}
		}
	}
    
	if($log)
		$this->logging($this->encode($log),$this->encode($operation));
	else
		$this->dom->save($this->fileName);
  }
  
  public function getParameters(){
  	$root = $this->parameters;
	$parameter = array('id'=>$root->getAttribute('id'));
  	$parameter = $parameter + array('name'=>$root->getAttribute('name'));
	$parameter = $parameter + array('team'=>$root->getAttribute('team'));
	$parameter = $parameter + array('team_name'=>$root->getAttribute('team_name'));
	$parameter = $parameter + array('type'=>$root->getAttribute('type'));
	
	$childNodes = $root->childNodes;
	foreach($childNodes as $childNode){
		if(	$childNode->nodeName != 'logs' ){
			$nodeName = $childNode->nodeName;
			$parameter = $parameter + array($nodeName=>$childNode->nodeValue);
		}
	}
	
	return $parameter;
  }
  
  private function logging($log,$operation){
  	$root = $this->parameters->getElementsByTagName("logs")->item(0);
	$log = $this->dom->createElement('log',$log);
	$log->setAttribute('time',date('y-m-d H:i:s'));
	$log->setAttribute('operator',$_COOKIE['login_name']);
	$log->setAttribute('operation',$operation);
	$root->appendChild($log);
	$this->dom->save($this->fileName);
  }
  
  public function getLogs(){
  	$logs = array();
  	$root = $this->parameters->getElementsByTagName("logs")->item(0);
	$childNodes = $root->childNodes;	
	foreach($childNodes as $childNode){	
		$logs = array_merge($logs,array(array('time'=>$childNode->getAttribute('time'),'operator'=>$childNode->getAttribute('operator'),'operation'=>$childNode->getAttribute('operation'),'log'=>$childNode->nodeValue)));
	}
	
	return $logs;
  }  
  
  public function deleteSelf(){
  	$root = $this->dom->getElementsByTagName("freespc")->item(0);
	$root->removeChild($this->parameters);
	$this->dom->save($this->fileName);
  }

  private function encode($source){
    return mb_convert_encoding($source, 'utf-8', 'gb2312');
  }
  
  private function decode($source){
    return mb_convert_encoding($source, 'gb2312', 'utf-8');
  }
}
?>