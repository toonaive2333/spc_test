<?php
$needAuthenticate = true;
require_once('../load.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Chart描述</title>
</head>
<body style="font-size:12px">
<?php
$id = $_GET["id"];
require_once('../includes/chart.class.php');
$chart = new Chart($id,true);	
if( $chart->checkExist() ){
	$parameters = $chart->getParameters();
}
$description = $parameters["description"];
$fileName = $parameters["fileName"];
$linkName = $parameters["linkName"];
echo "<span style='font-size:14px'><strong>".$parameters["name"]."</strong></span><br><br>";
if($description)
	echo $description."<br><br>";
if($fileName && $linkName){
	$ext = substr($fileName,strrpos($fileName,'.')+1);
	$ext = strtolower($ext);
	if(in_array($ext,$FILE_IMG)){
		echo "图纸 ：<a href='../upload/$linkName'>".$fileName."</a>";
		echo "<br><img src='../upload/$linkName'  border='1'/>";
	}else{
		echo "图纸 ：<a href='../upload/$linkName'>".$fileName."</a>";
	}
}
?>
</body>
</html>