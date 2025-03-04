<?php
require_once('../load.php');
$chartTypes = array('Xbar-R','Xbar-S','I-MR','P-Chart','NP-Chart','U-Chart','C-Chart');

$chartId = $_COOKIE['chart_id'];
$sampleSize = $_COOKIE['sample_size'];
$chartType = $_COOKIE['chart_type'];
$pointId = $_POST['pointId'];

global $wpdb;
require_db();
$datas = $wpdb->get_row("SELECT * FROM chart_$chartId WHERE id=$pointId ", ARRAY_A);

echo "<b>Chart type:</b> ".$chartTypes[$chartType-1]."\n";
echo "<b>Time:</b> ".$datas['data_time']."\n";

switch($chartType){
	case TYPE_XR:
	case TYPE_XS:
		$subChart = "Range";
		if($chartType == TYPE_XS)
			$subChart = "StDev";
		echo "<b>Mean:</b> ".$datas["xbar"]."\n";
		echo "<b>$subChart:</b> ".$datas["stat_value"]."\n";
		echo "<b>Samples:</b>\n";
		for($i=1;$i<$sampleSize+1;$i++){
			echo $datas["x_$i"];
			if($datas["product_$i"]!="")
				echo " (".$datas["product_$i"].")";
			echo "\n";
		}
	break;
	case TYPE_IMR:
		echo "<b>Individual:</b> ".$datas["x_1"];
		if($datas["product_1"]!="")
			echo " (".$datas["product_1"].")";
		echo "\n";
		echo "<b>MR:</b> ".$datas["stat_value"]."\n";		
	break;
	case TYPE_P:
		echo "<b>Proportion:</b> ".$datas["rate"]."\n";
		echo "<b>Sample count:</b> ".$datas["ng_count"]."\n";
		echo "<b>Subgroup size:</b> ".$datas["total_count"]."\n";		
	break;
	case TYPE_NP:
		echo "<b>Sample count:</b> ".$datas["ng_count"]."\n";
		echo "<b>Subgroup size:</b> ".$datas["total_count"]."\n";		
	break;
	case TYPE_U:
		echo "<b>Sample count per unit:</b> ".$datas["rate"]."\n";
		echo "<b>Sample count:</b> ".$datas["ng_count"]."\n";
		echo "<b>Unit size:</b> ".$datas["total_count"]."\n";		
	break;
	case TYPE_C:
		echo "<b>Sample count:</b> ".$datas["ng_count"]."\n";
	break;
}
echo "<b>Tests failed:</b> \n";
$against = $datas["against"];
if($against == 9)
	$against = 1;
if($against == 0)
	echo "none";
else
	echo "Rule ".$against.": ".$RULES_ENG[$against-1]."(".$RULES[$against-1].")";
echo "\n";
	
echo "<b>Description:</b> \n";
echo $datas["remark"];
?>