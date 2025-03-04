<?php
$needAuthenticate = true;
require_once('../load.php');
//$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");

$id = $_GET['id'];
if(empty($id))
	tx_die("参数错误<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
$chartTypes = array('Xbar-R','Xbar-S','I-MR','P-Chart','NP-Chart','U-Chart','C-Chart');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置 > 我的Team > Team信息</title>
</head>

<body>
<?php
showMenuBar('setup');
?>

<!--body-->
<div class="tip">
<?php if($_COOKIE['login_isAdmin'] == 1){?>
	<div class="of" onclick="window.location.href='invite.php'"><strong>邀请成员</strong></div>		
	<div class="of" onclick="window.location.href='team.php'"><strong>创建Team</strong></div>
<?php }?>
	<div class="of" onclick="window.location.href='createChart.php'"><strong>创建Chart</strong></div>
	<div class="on" onclick="window.location.href='myTeams.php'"><strong>我的Team</strong></div>	
	<div class="of" onclick="window.location.href='memberInfo.php'"><strong><?php if($_COOKIE['login_isAdmin'] == 2)echo '个人信息';else echo '管理员';?></strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>			
</div>
<div class="container" style="border-top:none;">
<?php if($_GET['a']){?>
<div class="top_tip" style="width:240px"><a href="myTeams.php">我的Team</a> > <a href="javaScript:history.go(-1);">Team信息</a> > Chart属性</div>
<? }else{?>
<div class="top_tip" style="width:80px">Chart属性</div>
<? }?>
<?php
if(!empty($id)){
	require_once('../includes/chart.class.php');
	$chart = new Chart($id,true);	
	if( $chart->checkExist() ){
		$parameters = $chart->getParameters();
		
		$rules_text = array();
		$rules = explode('|',$parameters['rules']);
		foreach($rules as $rule){
			if($rule>0){
				$rules_text = $rules_text+array($rule=>$RULES[$rule-1]);
			}
		}
		
		global $wpdb;
		require_db();
		$team_id = $parameters['team'];
		$members = $wpdb->get_results("SELECT members.* FROM members,members_team WHERE members_team.member_id=members.id AND members_team.team_id=$team_id", ARRAY_A);		
?>
<div class="hr"></div>
<div style="float:left;">
<div style="margin-bottom:10px; font-size:14px;">
<img src="../img/little_chart.gif"/><span class="type"><strong><?php echo ' '.$chartTypes[$parameters['type']-1]?></strong></span>
<strong><?php echo $parameters['name']?></strong>
<div id="chart_description">
<?php
$description = $parameters["description"];
$fileName = $parameters["fileName"];
$linkName = $parameters["linkName"];
echo $description;
?>
</div>
</div>
<div style="margin-left:20px;">
<?php if($fileName && $linkName){
?>
<div style="clear:left"><div class="lable_1">图纸：</div><a class="upfileName" target=_blank href="../upload/<?php echo $linkName?>"><?php echo $fileName?></a></div>
<?php 
}?>
<div style="clear:left"><div class="lable_1">责任Team：</div><?php echo $parameters['team_name']?><!--<span id='show_members' title="显示所有Team成员">(Team成员)</span>--></div>
<div id="members_pan">
<?php
/*if(is_array($members)){
	foreach($members as $member){
		echo $member['nickname'];
		echo " (".$member['email'].")";
		echo " ; ";
	}
}else{
	echo "暂没有成员。";
}*/
?>
</div>
<?php
switch($parameters['type']){	
	case TYPE_XR:
	case TYPE_XS:
	case TYPE_IMR:	
		if($parameters['type'] == TYPE_XR)
			$subChart = 'R';
		elseif($parameters['type'] == TYPE_XS)
			$subChart = 'S';	
		else
			$subChart = 'MR';
			if($parameters['type'] != TYPE_IMR){
?>
<div style="clear:left"><div class="lable_1">子组大小：</div><?php echo $parameters['sample_size']?></div>
<?php       }?>
<div style="clear:left"><div class="lable_1">X Chart控制限：</div><?php echo $parameters['lcl_x']?> ~ <?php echo $parameters['ucl_x']?></div>
<div style="clear:left"><div class="lable_1"><?php echo $subChart?> Chart控制限：</div>0 ~ <?php echo $parameters['ucl_2']?></div>
<div style="clear:left"><div class="lable_1">规格限：</div><?php echo $parameters['lsl']?> ~ <?php echo $parameters['usl']?></div>
<?php
	break;
	case TYPE_P:
	case TYPE_NP:
	case TYPE_U:
	case TYPE_C:
?>
<div style="clear:left"><div class="lable_1">中心线：</div><?php echo $parameters['cl']?></div>
<?php
	if($parameters['type'] == TYPE_NP){
?>
<div style="clear:left"><div class="lable_1">子组大小：</div><?php echo $parameters['sample_size']?></div>	
<?php
	}
	break;
}
?>
<div style="clear:left"><div class="lable_1">检验规则：</div>
<?php
if(empty($rules_text)){
	echo '<div>不检测';
}else{
	echo "<div id='rules_pan'>";
	foreach($rules_text as $rule=>$description){
		echo "<span>".$rule.'</span>'.$description;
		echo "<br>";
	}
}
?>
</div>
<?php
	}
}
?>
</div>
</div>
<div style="clear:both"></div>
<div style="margin-top:20px;margin-left:65px;float:left;width:200px;">
	<div id="edit_delete">
		<a target="_parent" title='修改' href='createChart.php?a=e&chart_id=<?php echo $id?>'><img border=0 src='<?php echo $ABS_PATH?>img/edit.gif'>修改</a>&nbsp;&nbsp;
		<a target="_top" title='依照本参数新建Chart' href='createChart.php?a=c&chart_id=<?php echo $id?>'><img border=0 src='<?php echo $ABS_PATH?>img/copy.gif'>复制</a>&nbsp;&nbsp;
<?php if($_COOKIE['login_isAdmin'] == 1){?>
	<a href='#d' title='删除' id="delete_bt"><img border=0 src='<?php echo $ABS_PATH?>img/delete.gif'>删除</a><a name="d"></a>
<?php }?>
	</div>
	<div id="confirm" style="display:none"><div style="float:left"><img border=0 title='删除' src='<?php echo $ABS_PATH?>img/warn.gif'></div><div>删除Chart将删除属于该Chart的所有数据，<br />您确定要删除吗？</div><br /><form action='chartDelete.php?id=<?php echo $id?>' method='post'><input type=submit value='确定'  class='bt_2'/>&nbsp;&nbsp;<input type=button id='cancel_bt' value='取消'  class='bt_2' /></form></div>
	<?php if($_GET['a']){?>
	<div style="clear:both"><br /><input type=submit value='返回'  class='bt' onclick="history.go(-1)"/></div>
	<?php }?>
</div>
</div>
<div id="help_window" style="width:400px;"><strong>Chart日志</strong><br/>
<div>
<?php
$logs = $chart->getLogs();
foreach(array_reverse($logs) as $log){	
	echo "<span onmouseout='hideLog()' onmouseover=\"showLog('".$log['log']."',event,this)\"><span class='logtime'>".$log['time']." </span>";
	echo $log['operation'];
	echo "<span class='operator'>(";
	echo $log['operator'];
	echo ")</span></span><br>";		
}
?>
</div>
<div id="logDetails"></div>
</div>
<div style="clear:both"></div>
</div>
<?php showBottom();?>
</body>
</html>

<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/setup/chartDetail.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $ABS_PATH?>css/setup.css" />