<?php

define( 'ABSPATH', dirname(__FILE__) . '/' );
$dir = dir(ABSPATH);
$projects = array();

//查找已经安装项目
while (($file = $dir->read()) !== false)
{
	if( strpos($file,'.') !== 0 && strpos($file,'.') == false)
		$projects[] = $file;
}
$dir->close();

if(count($projects) == 1){
	echo "<script>location.href = '$projects[0]'</script>";
}else if(count($projects) > 1){
	show($projects);
}


function show( $message ) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Welcome!</title>
	<style type="text/css">
		.logo{
			margin:auto;
			width:700px;
			padding-top:40px;
			padding-bottom:10px;
			color:#006699
		}
		#container{
			border:#DBDBDB 1px solid;
			background-color:#FFFFFF;
			margin:0px auto;
			padding:20px;
			width:660px;
		}
		#message{
			margin-top:10px;
			line-height:20px;
		}
		.hr{
			background-color:#DEDEDE;
			height:1px;
			overflow:hidden;
			margin-top:5px;
			margin-bottom:5px;
			clear:both
		}
		.t1{
			font-size:16px;
			font-weight:bold;
		}
	</style>
</head>
<body>
	<div class="logo">欢迎使用<a target="_blank" href="http://www.tonxon.com">tonxon.com</a>为您提供的免费软件</div>
	<div id="container">
		<div class="t1">已安装的项目:</div>
		<div class="hr"></div>
		<div id="message"><?php
			foreach($message as $project){
				echo "<a href='$project'>$project</a><br>";
				echo strpos($project,'.');
			}
		?>		
		</div>
	</div>
</body>
</html>
<?php
}
?> 
