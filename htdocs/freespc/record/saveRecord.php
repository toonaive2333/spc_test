<?php
require_once('../load.php');
require_once('../includes/chart.class.php');
global $wpdb;
require_db();

$id = $_POST['id'];
$chart = new Chart($id,true);
if( !$chart->checkExist() ){
	echo "您录入的Chart不存在，可能已经被删除。";
	exit;
}
$parameters = $chart->getParameters();
$result;
switch($parameters['type']){
	case TYPE_XR:
	case TYPE_XS:
	case TYPE_IMR:
		$values = array();
		$ids = array();		
		$sampleSize = $parameters['sample_size'];
		for($i=0;$i<$sampleSize;$i++){
			$values[] = $_POST['v'.$i];
		}
		if( $_POST['p0']!="" ){
			for($i=0;$i<$sampleSize;$i++){
				$ids[] = $_POST['p'.$i];	
			}
		}	
		$result = $chart->record(array('values'=>$values,'ids'=>$ids));	
	break;
	case TYPE_P:
	case TYPE_NP:
	case TYPE_U:
		$result = $chart->record(array('value'=>$_POST['v'],'sampleSize'=>$_POST['s'],'ids'=>$_POST['p']));
	break;
	case TYPE_C:
		$result = $chart->record(array('value'=>$_POST['v'],'ids'=>$_POST['p']));
	break;
}
echo $result;
?>

