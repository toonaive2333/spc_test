<?php
$needAuthenticate = true;
require_once('../load.php');
require_once('../includes/chart.class.php');

$action = $_POST['actions'];
$chart_id = $_POST['chart_id'];

$chartName = trim($_POST['name']);
$description = trim($_POST['description']);
$fileName = trim($_POST['fileName']);
$linkName = trim($_POST['linkName']);
$teamId = $_POST['team'];
$chartType = $_POST['chart_selector'];
if( $action=='e' )
	$chartType = $_POST['chart_selector_temp'];
$new_chart_id;
$chart = new Chart(0);

if( empty($teamId) )
	tx_die("请先创建一个Team，然后再创建Chart。<br><span style='font-size:12px'>注：管理员有权限创建Team。</span><br><br><br><br><form action='team.php' method='post'><input type=submit value='创建Team'  class='bt'/></form>");
if( $chartType<TYPE_IMR && (trim($_POST["sampleSize_xr"])>20 || trim($_POST["sampleSize_xs"])>20) ){
	tx_die("子组大小过大，请选择一个小于20的数值。<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
}

global $wpdb;
require_db();
//get team name
$team_name = $wpdb->get_var("SELECT name FROM teams WHERE id=$teamId");

if( $action=='e' ){
//edit chart
	$chart	= new Chart($chart_id,true);
	$prmtrs = $chart->getParameters();
	if($teamId != $prmtrs['team']){
		checkExist();
	}
	
	$wpdb->query("UPDATE teams SET charts=charts-1 WHERE id=".$prmtrs['team']);
	$wpdb->query("UPDATE teams SET charts=charts+1 WHERE id=$teamId");
	$data = array('name'=>$chartName, 'team'=>$teamId);
	$where = array('id'=>$chart_id);
	$wpdb->update( 'charts', $data, $where );
	$new_chart_id = $chart_id;
}else{
//build a new one
	checkExist();		
	$wpdb->query("UPDATE teams SET charts=charts+1 WHERE id=$teamId");			
	$data = array('name'=>$chartName, 'team'=>$teamId, 'chart_type'=>$chartType);
	$wpdb->insert( 'charts', $data );
	$new_chart_id = (int) $wpdb->insert_id;
}

switch($chartType){
	case TYPE_XR:
		$sampleSize = trim($_POST["sampleSize_xr"]);
		$lcl_x = trim($_POST["lcl_xr_x"]); 
		$ucl_x = trim($_POST["ucl_xr_x"]); 
		$ucl_2 = trim($_POST["ucl_xr_r"]);
		$lsl = trim($_POST["lsl_xr"]);
		$usl = trim($_POST["usl_xr"]);
			
		$rules = $_POST['rules_8'];
		$ruleList = '0';
		if(count($rules) > 0){			
			foreach( $rules as $rule ){
				$ruleList .= '|';
				$ruleList .= $rule;
			}
		}		
		$data = array('name'=>$chartName,'sample_size'=>$sampleSize,
					  'lcl_x'=>$lcl_x,'ucl_x'=>$ucl_x,'ucl_2'=>$ucl_2,
					  'lsl'=>$lsl,'usl'=>$usl,
					  'rules'=>$ruleList,
					  'type'=>TYPE_XR,'team'=>$teamId,'team_name'=>$team_name
					 );
		$data = $data+array('description'=>$description);
		$data = $data+array('fileName'=>$fileName,'linkName'=>$linkName);
		
		if( $action=='e' ){
			$chart->updateChart($data);
		}else{
			$xChart	= new Chart($new_chart_id);
			$xChart->createChart($data);
		}
	break;
	case TYPE_XS:
		$sampleSize = trim($_POST["sampleSize_xs"]);
		$lcl_x = trim($_POST["lcl_xs_x"]);
		$ucl_x = trim($_POST["ucl_xs_x"]); 
		$ucl_2 = trim($_POST["ucl_xs_s"]);
		$lsl = trim($_POST["lsl_xs"]);
		$usl = trim($_POST["usl_xs"]);
			
		$rules = $_POST['rules_8'];
		$ruleList = '0';
		if(count($rules) > 0){			
			foreach( $rules as $rule ){
				$ruleList .= '|';
				$ruleList .= $rule;
			}
		}		
		$data = array('name'=>$chartName,'sample_size'=>$sampleSize,
					  'lcl_x'=>$lcl_x,'ucl_x'=>$ucl_x,'ucl_2'=>$ucl_2,
					  'lsl'=>$lsl,'usl'=>$usl,
					  'rules'=>$ruleList,
					  'type'=>TYPE_XS,'team'=>$teamId,'team_name'=>$team_name
					 );
		$data = $data+array('description'=>$description);
		$data = $data+array('fileName'=>$fileName,'linkName'=>$linkName);
		
		if( $action=='e' ){
			$chart->updateChart($data);
		}else{
			$xChart	= new Chart($new_chart_id);
			$xChart->createChart($data);
		}
	break;
	case TYPE_IMR:
		$sampleSize = 1;
		$lcl_x = trim($_POST["lcl_imr_x"]); 
		$ucl_x = trim($_POST["ucl_imr_x"]); 
		$ucl_2 = trim($_POST["ucl_imr_r"]);
		$lsl = trim($_POST["lsl_imr"]);
		$usl = trim($_POST["usl_imr"]);
				
		$rules = $_POST['rules_8'];
		$ruleList = '0';
		if(count($rules) > 0){			
			foreach( $rules as $rule ){
				$ruleList .= '|';
				$ruleList .= $rule;
			}
		}		
		$data = array('name'=>$chartName,'sample_size'=>$sampleSize,
					  'lcl_x'=>$lcl_x,'ucl_x'=>$ucl_x,'ucl_2'=>$ucl_2,
					  'lsl'=>$lsl,'usl'=>$usl,
					  'rules'=>$ruleList,
					  'type'=>TYPE_IMR,'team'=>$teamId,'team_name'=>$team_name
					 );
		$data = $data+array('description'=>$description);
		$data = $data+array('fileName'=>$fileName,'linkName'=>$linkName);
		
		if( $action=='e' ){
			$chart->updateChart($data);
		}else{
			$xChart	= new Chart($new_chart_id);
			$xChart->createChart($data);
		}
	break;
	case TYPE_P:
		$cl = trim($_POST["cl_p"]);				
		$rules = $_POST['rules_4'];
		$ruleList = '0';
		if(count($rules) > 0){			
			foreach( $rules as $rule ){
				$ruleList .= '|';
				$ruleList .= $rule;
			}
		}		
		$data = array('name'=>$chartName,
					  'cl'=>$cl,
					  'rules'=>$ruleList,
					  'type'=>TYPE_P,'team'=>$teamId,'team_name'=>$team_name
					 );
		$data = $data+array('description'=>$description);
		$data = $data+array('fileName'=>$fileName,'linkName'=>$linkName);
		
		if( $action=='e' ){
			$chart->updateChart($data);
		}else{
			$pChart	= new Chart($new_chart_id);
			$pChart->createChart($data);
		}
	break;
	case TYPE_NP:
		$sampleSize = trim($_POST["sampleSize_np"]);
		$cl = trim($_POST["cl_np"]);				
		$rules = $_POST['rules_4'];
		$ruleList = '0';
		if(count($rules) > 0){			
			foreach( $rules as $rule ){
				$ruleList .= '|';
				$ruleList .= $rule;
			}
		}		
		$data = array('name'=>$chartName,'sample_size'=>$sampleSize,
					  'cl'=>$cl,
					  'rules'=>$ruleList,
					  'type'=>TYPE_NP,'team'=>$teamId,'team_name'=>$team_name
					 );
		$data = $data+array('description'=>$description);
		$data = $data+array('fileName'=>$fileName,'linkName'=>$linkName);
		
		if( $action=='e' ){
			$chart->updateChart($data);
		}else{
			$pChart	= new Chart($new_chart_id);
			$pChart->createChart($data);
		}
	break;
	case TYPE_U:
		$cl = trim($_POST["cl_u"]);				
		$rules = $_POST['rules_4'];
		$ruleList = '0';
		if(count($rules) > 0){			
			foreach( $rules as $rule ){
				$ruleList .= '|';
				$ruleList .= $rule;
			}
		}		
		$data = array('name'=>$chartName,
					  'cl'=>$cl,
					  'rules'=>$ruleList,
					  'type'=>TYPE_U,'team'=>$teamId,'team_name'=>$team_name
					 );
		$data = $data+array('description'=>$description);
		$data = $data+array('fileName'=>$fileName,'linkName'=>$linkName);
		
		if( $action=='e' ){
			$chart->updateChart($data);
		}else{
			$pChart	= new Chart($new_chart_id);
			$pChart->createChart($data);
		}
	break;
	case TYPE_C:
		$cl = trim($_POST["cl_c"]);				
		$rules = $_POST['rules_4'];
		$ruleList = '0';
		if(count($rules) > 0){			
			foreach( $rules as $rule ){
				$ruleList .= '|';
				$ruleList .= $rule;
			}
		}		
		$data = array('name'=>$chartName,
					  'cl'=>$cl,
					  'rules'=>$ruleList,
					  'type'=>TYPE_C,'team'=>$teamId,'team_name'=>$team_name
					 );
		$data = $data+array('description'=>$description);
		$data = $data+array('fileName'=>$fileName,'linkName'=>$linkName);
		
		if( $action=='e' ){
			$chart->updateChart($data);
		}else{
			$pChart	= new Chart($new_chart_id);
			$pChart->createChart($data);
		}
	break;
}

function checkExist(){
	global $chartName;
	global $teamId;
	global $wpdb;
	$id = $wpdb->get_var("SELECT id FROM charts WHERE name='$chartName' AND team=$teamId");
	if(!empty($id))
		tx_die("该Team已经有一个同名的Chart，请返回重新输入<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
}

$result = "Chart创建完成";
if($action == 'e')
	$result = "Chart修改完成";
tx_die($result."<br><br><br><br><input type=button value='完成'  class='bt' onclick='history.go(-1)'/>","完成","操作完成");

?>