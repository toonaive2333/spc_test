<?php
$needAuthenticate = true;
require_once('../load.php');
require_once('../includes/chart.class.php');
?>

<?php
$team_id = $_GET['team_id'];
$action = $_GET['a'];
$chart_id = $_GET['chart_id'];
$parameters;
$rules;

$name=''; $teamID=0; $chartType=1;$description="";$fileName="";$linkName="";
$sampleSize_xr=''; $lcl_xr_x=''; $ucl_xr_x=''; $ucl_xr_r=''; $lsl_xr=''; $usl_xr='';
$sampleSize_xs=''; $lcl_xs_x=''; $ucl_xs_x=''; $ucl_xs_s=''; $lsl_xs=''; $usl_xs=''; 
$lcl_imr_x=''; $ucl_imr_x=''; $ucl_imr_r=''; $lsl_imr=''; $usl_imr='';
$cl_p='';
$sampleSize_np=''; $cl_np=''; 
$cl_u='';
$cl_c='';

global $wpdb;
require_db();

if($action == 'e' || $action == 'c'){
	checkChartId();
	$_COOKIE['login_isAdmin'] == 2 && checkAuth();
	$chart = new Chart($chart_id,true);
	$parameters = $chart->getParameters();
	$name=$parameters['name']; 
	$teamID=$parameters['team']; 
	$chartType=$parameters['type'];
	$rules = explode('|',$parameters['rules']);
	$description = $parameters["description"];
	$fileName = $parameters["fileName"];
	$linkName = $parameters["linkName"];

	switch($chartType){
		case TYPE_XR:
			$sampleSize_xr = $parameters['sample_size'];
			$ucl_xr_x = $parameters['ucl_x'];
			$lcl_xr_x = $parameters['lcl_x'];
			$ucl_xr_r = $parameters['ucl_2'];
			$lsl_xr = $parameters['lsl'];
			$usl_xr = $parameters['usl'];
		break;
		case TYPE_XS:
			$sampleSize_xs = $parameters['sample_size'];
			$ucl_xs_x = $parameters['ucl_x'];
			$lcl_xs_x = $parameters['lcl_x'];
			$ucl_xs_s = $parameters['ucl_2'];
			$lsl_xs = $parameters['lsl'];
			$usl_xs = $parameters['usl'];
		break;
		case TYPE_IMR:
			$ucl_imr_x = $parameters['ucl_x'];
			$lcl_imr_x = $parameters['lcl_x'];
			$ucl_imr_r = $parameters['ucl_2'];
			$lsl_imr = $parameters['lsl'];
			$usl_imr = $parameters['usl'];
		break;
		case TYPE_P:
			$cl_p = $parameters['cl'];
		break;
		case TYPE_NP:
			$sampleSize_np = $parameters['sample_size'];
			$cl_np = $parameters['cl'];
		break;
		case TYPE_U:
			$cl_u = $parameters['cl'];
		break;
		case TYPE_C:
			$cl_c = $parameters['cl'];
		break;
	}
}

$teams = "";
if($_COOKIE['login_isAdmin'] == 2)
	$teams = $wpdb->get_results("SELECT * FROM teams WHERE id IN (SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") ORDER BY CONVERT(name USING gbk)", ARRAY_A);
else
	$teams = $wpdb->get_results("SELECT * FROM teams ORDER BY CONVERT(name USING gbk)", ARRAY_A);
if(!is_array($teams))
	tx_die("请先创建一个Team，然后再创建Chart。<br><span style='font-size:12px'>注：管理员有权限创建Team。</span><br><br><br><br><form action='team.php' method='post'><input type=submit value='创建Team'  class='bt'/></form>");

//check chart id exist
function checkChartId(){
	global $chart_id;
	global $wpdb;
	empty($chart_id) &&	tx_die("参数错误<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
	$sql_check = "SELECT team FROM charts WHERE id=$chart_id";
	$teamId = $wpdb->get_var($sql_check);
	if(empty($teamId))
		tx_die("参数错误<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
}
//check if the login member has the right
function checkAuth(){
	global $chart_id;
	global $wpdb;
	$sql_check = "SELECT charts.team FROM charts,members_team WHERE charts.id=$chart_id AND charts.team=members_team.team_id AND members_team.member_id=".$_COOKIE['login_id'];
	$teamId = $wpdb->get_var($sql_check);
	if(empty($teamId))
		tx_die("参数错误<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置 &rsaquo; 创建Chart</title>
</head>

<body>
<?php showMenuBar('setup');?>
<!--body-->
<div class="tip">
<?php if($_COOKIE['login_isAdmin'] == 1){?>
	<div class="of" onclick="window.location.href='invite.php'"><strong>邀请成员</strong></div>	
	<div class="of" onclick="window.location.href='team.php'"><strong>创建Team</strong></div>
<?php }?>	
	<div class="on" onclick="window.location.href='createChart.php'"><strong>创建Chart</strong></div>
	<div class="of" onclick="window.location.href='myTeams.php'"><strong>我的Team</strong></div>	
	<div class="of" onclick="window.location.href='memberInfo.php'"><strong><?php if($_COOKIE['login_isAdmin'] == 2)echo '个人信息';else echo '管理员';?></strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>			
</div>
<div class="container" style="border-top:none;">
<?php if($action == 'e'){?>
<div class="title"><strong>修改Chart属性</strong></div>
<? }else{?>  
<div class="title"><strong>创建新Chart</strong></div>
<? }?>  
<div class="hr"></div>
<br>	
<div style="float:left; width:660px">
	<form action="createChart_handle.php" enctype="multipart/form-data" id="createForm" name="createForm" method="post">
	<div><div class="item">Chart名称：</div><input type="text" name="name" id="name" class="inputText" style="width:383px; float:left"/></div>	
	<div id="name_error_tip" class="error_tip" style="width:150px;"><img src="../img/warning.gif" />请填写Chart名称</div>
	<div style="clear:both; height:20px;"></div>
	<div><div class="item"><span style="font-weight:normal;color:#3388DD">(可选)</span>描述：</div><textarea name="description" id="description"><?php echo $description?></textarea></div>
	<div style="clear:both; height:20px;"></div>
	<div class="item"><span style="font-weight:normal;color:#3388DD">(可选)</span>图纸：</div><div style="float:left">
	<div><div id="upfileName"></div><div id="deleteBt"></div></div>
	<input type="hidden" name="fileName" id="fileName" />
	<input type="hidden" name="linkName" id="linkName" />
	<span id="uploadRefresh">
	<input name="uploader" id="uploader" type="file" class="inputText" style="width:386px; height:22px;float:left"/>
	</span>	 
	<iframe id="upload_target" name="upload_target" src="#" style="display:none"></iframe>
	 </div>	
	<div style="clear:both; height:20px;"></div>
	<div><div class="item">责任Team：</div>
	<select name="team" id="team">
<?php
if(is_array($teams)){
	foreach($teams as $team){
		echo "<option value=".$team['id']." ";
		if($team_id == $team['id'])
			echo "selected";
		echo ">";
		echo $team['name']."</option>";		
	}
}
?>
	</select>
	</div>
	<p>
	<div><div class="item">Chart类型：</div>
	<select name="chart_selector" id="chart_selector">
	  <option value="1">Xbar-R</option>
	  <option value="2">Xbar-S</option>
	  <option value="3">I-MR</option>
	  <option value="4">P Chart</option>
	  <option value="5">NP Chart</option>
	  <option value="6">U Chart</option>
	  <option value="7">C Chart</option>
	</select><span id="noedit_chart_type">&nbsp;&nbsp;*不可修改</span>
	<input name="chart_selector_temp" id="chart_selector_temp" type="hidden" value="" />
	</div>
	<p>
	<div><div class="item">Chart参数：</div>
<!--Xbar-R参数-->
		<div id="parameters_xr" class="parameters" style="display:block">
		&nbsp;<span>子组大小(推荐2-8)</span>&nbsp;&nbsp;
		<input type="text" id="sampleSize_xr" name="sampleSize_xr" class="input_text" style="width:50px"/><span id="noedit_size_xr">*不可修改</span><p>
		<span><code>X</code> Chart控制限</span>LCL：<input type="text" id="lcl_xr_x" name="lcl_xr_x" class="input_text" style="width:50px"/>&nbsp;&nbsp;UCL：<input type="text" id="ucl_xr_x" name="ucl_xr_x" class="input_text" style="width:50px"/><p>
		<span>R Chart控制限</span>UCL：<input type="text" id="ucl_xr_r" name="ucl_xr_r" class="input_text" style="width:50px"/><p>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>规格限</span>LSL：<input type="text" id="lsl_xr" name="lsl_xr" class="input_text" style="width:50px"/>&nbsp;&nbsp;USL：<input type="text" id="usl_xr" name="usl_xr" class="input_text" style="width:50px"/><p>
		</div>
		
<!--Xbar-S参数-->
		<div id="parameters_xs" class="parameters">
		&nbsp;<span>子组大小(推荐>8)</span>&nbsp;&nbsp;
		<input type="text" id="sampleSize_xs" name="sampleSize_xs" class="input_text"/><span id="noedit_size_xs">*不可修改</span><p>
		<span><code>X</code> Chart控制限</span>LCL：<input type="text" id="lcl_xs_x" name="lcl_xs_x" class="input_text"/>&nbsp;&nbsp;UCL：<input type="text" id="ucl_xs_x" name="ucl_xs_x" class="input_text"/><p>
		<span>S Chart控制限</span>UCL：<input type="text" id="ucl_xs_s" name="ucl_xs_s" class="input_text"/><p>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>规格限</span>LSL：<input type="text" id="lsl_xs" name="lsl_xs" class="input_text"/>&nbsp;&nbsp;USL：<input type="text" id="usl_xs" name="usl_xs" class="input_text"/><p>
		</div>
		
<!--I-MR参数-->
		<div id="parameters_imr" class="parameters">
		&nbsp;&nbsp;&nbsp;<span>X Chart控制限</span>LCL：<input type="text" id="lcl_imr_x" name="lcl_imr_x" class="input_text"/>&nbsp;&nbsp;UCL：<input type="text" id="ucl_imr_x" name="ucl_imr_x" class="input_text"/><p>
		<span>MR Chart控制限</span>UCL：<input type="text" id="ucl_imr_r" name="ucl_imr_r" class="input_text"/><p>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>规格限</span>LSL：<input type="text" id="lsl_imr" name="lsl_imr" class="input_text"/>&nbsp;&nbsp;USL：<input type="text" id="usl_imr" name="usl_imr" class="input_text"/><p>
		</div>	
		
<!--P-Chart参数-->
		<div id="parameters_p" class="parameters">
		<span>指定CL</span><input type="text" id="cl_p" name="cl_p" class="input_text"/>&nbsp;(<code>P</code>)
		<br /><br /><br /><br /><br /><br /><br />
		</div>	
			
<!--NP-Chart参数-->
		<div id="parameters_np" class="parameters">
		<span>子组大小</span><input type="text" id="sampleSize_np" name="sampleSize_np" class="input_text"/>&nbsp;(N)<p>
		&nbsp;&nbsp;&nbsp;<span>指定CL</span><input type="text" id="cl_np" name="cl_np" class="input_text"/>&nbsp;(N<code>P</code>)
		<br /><br /><br /><br /><br /><br /><br />
		</div>
		
<!--U-Chart参数-->
		<div id="parameters_u" class="parameters">
		&nbsp;&nbsp;&nbsp;<span>指定CL</span><input type="text" id="cl_u" name="cl_u" class="input_text"/>&nbsp;(<code>U</code>)
		<br /><br /><br /><br /><br /><br /><br />
		</div>
		
<!--C-Chart参数-->
		<div id="parameters_c" class="parameters">
		&nbsp;&nbsp;&nbsp;<span>指定CL</span><input type="text" id="cl_c" name="cl_c" class="input_text"/>&nbsp;(<code>C</code>)
		<br /><br /><br /><br /><br /><br /><br />
		</div>	
	</div>	
	
		<div id="parameters_error_tip" class="error_tip" style="width:150px;text-align:left"><img src="../img/warning.gif" />Chart参数规则：<br />1.所有参数均为数值型<br />2.子组大小必须为>1的整数<br />3.LCL&lt;UCL<br />4.LSL&lt;USL<br />5.R/S/MR Chart的UCL&gt;0<br />6.P/NP/U/C Chart的CL&gt;0<br />7.P Chart的CL&lt;1</div>
		<div style="clear:both"></div><p>
	<div><div class="item">检验规则：</div>	
	
<!--rules_8-->
		<div id="rules_8" class="parameters" style="display:block">
		<ul>
		<li><input type="checkbox" name="rules_8[]" id="rules_8_1" value="1" checked="checked"/><span>1</span>1个点距离中心线大于3个标准差(即超出控制限)</li>
		<li><input type="checkbox" name="rules_8[]" id="rules_8_2" value="2"/><span>2</span>连续9点在中心线同一侧</li>
		<li><input type="checkbox" name="rules_8[]" id="rules_8_3" value="3"/><span>3</span>连续6个点，全部递增或全部递减</li>
		<li><input type="checkbox" name="rules_8[]" id="rules_8_4" value="4"/><span>4</span>连续 14个点，上下交错</li>
		<li><input type="checkbox" name="rules_8[]" id="rules_8_5" value="5"/><span>5</span>3个点中有2个点，距离中心线（同侧）大于2个标准差</li>
		<li><input type="checkbox" name="rules_8[]" id="rules_8_6" value="6"/><span>6</span>5个点中有4个点，距离中心线（同侧）大于1个标准差</li>
		<li><input type="checkbox" name="rules_8[]" id="rules_8_7" value="7"/><span>7</span>连续15个点，距离中心线（任一侧）1个标准差以内</li>
		<li><input type="checkbox" name="rules_8[]" id="rules_8_8" value="8"/><span>8</span>连续8个点，距离中心线（任一侧）大于1个标准差</li>
		</ul>
		</div>
<!--rules_4-->
		<div id="rules_4" class="parameters">
		<ul>
		<li><input type="checkbox" name="rules_4[]" id="rules_4_1" value="1" checked="checked"/><span>1</span>1个点距离中心线大于3个标(即超出控制限)准差</li>
		<li><input type="checkbox" name="rules_4[]" id="rules_4_2" value="2"/><span>2</span>连续9点在中心线同一侧</li>
		<li><input type="checkbox" name="rules_4[]" id="rules_4_3" value="3"/><span>3</span>连续6个点，全部递增或全部递减</li>
		<li><input type="checkbox" name="rules_4[]" id="rules_4_4" value="4"/><span>4</span>连续 14个点，上下交错</li>
		</ul>
		</div>	
	</div>		
	<div style="clear:both"></div><p>
	<div><div class="item">&nbsp;</div><input name="create_bt" id="create_bt" type="button" class="bt" value="下一步" />
	<input name="actions" type="hidden" value="<?php echo $action?>" />
	<input name="chart_id" type="hidden" value="<?php echo $chart_id?>" /></div>
	</form>
</div>
<div id="help_window"><img src="../img/help.gif"/><strong>提示</strong><br/><div>&nbsp;&nbsp;&nbsp;&nbsp;在此处创建的Chart一般用于长期监控用，如6Sigma项目DMAIC中的Control阶段使用。<br/>&nbsp;&nbsp;&nbsp;&nbsp;所以，您必须指定所有的参数，如计量型Chart的控制限LCL/UCL、计数型Chart的中心线CL等，另外对于计量型Chart您还必须指定规格限LSL/USL，该组数值在计算制程能力Cpk时会用到。<br/>&nbsp;&nbsp;&nbsp;&nbsp;您可能暂时还不能确定LCL/UCL或CL的具体数值，建议您先使用LSL/USL作为LCL/UCL，或者使用历史经验数据，具体运行一段时间后，再依据实际情况适时更新。</div>
</div>
<div style="clear:both"></div>
</div>

<!--end of body-->
<?php showBottom();?>
</body>
</html>

<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/setup/createChart.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $ABS_PATH?>css/setup.css" />
<style>
.input_text{
	width:50px;
}
#description{
	width:380px;
	height:60px;
}
.item{
	width:80px;
}
</style>
<script>

for(i=0;i<Dom.get('team').options.length;i++){
	if(Dom.get('team').options[i].value == <?php echo $teamID?>){
		Dom.get('team').options[i].selected = true;
		break;
	}
}
Dom.get('chart_selector').options[<?php echo $chartType-1?>].selected = true;

var rules = 'rules_4';
if(<?php echo $chartType?> < 4)
	rules = 'rules_8';
<?php 
if( is_array($rules) ){
	foreach($rules as $rule){
		if( $rule>0 ){
			echo "Dom.get(rules+'_'+".$rule.").checked = true;\n";
		}
	}
}
?>
<?php
if($fileName && $linkName){
?>
Dom.get("upfileName").innerHTML = "<a target=_blank href='../upload/<?php echo $linkName?>'><?php echo $fileName?></a>";
Dom.get("fileName").value = "<?php echo $fileName?>";
Dom.get("linkName").value = "<?php echo $linkName?>";
Dom.get("uploader").style.display = "none";
Dom.get("deleteBt").innerHTML = "<img src='../img/delete2.gif'>删除";
Dom.get("deleteBt").style.display = "block";
<?php
}?>
showParameterPan(<?php echo $chartType?>);
Dom.get('sampleSize_xr').value = "<?php echo $sampleSize_xr?>";
Dom.get('ucl_xr_x').value = "<?php echo $ucl_xr_x?>";
Dom.get('lcl_xr_x').value = "<?php echo $lcl_xr_x?>";
Dom.get('ucl_xr_r').value = "<?php echo $ucl_xr_r?>";
Dom.get('lsl_xr').value = "<?php echo $lsl_xr?>";
Dom.get('usl_xr').value = "<?php echo $usl_xr?>";

Dom.get('sampleSize_xs').value = "<?php echo $sampleSize_xs?>";
Dom.get('ucl_xs_x').value = "<?php echo $ucl_xs_x?>";
Dom.get('lcl_xs_x').value = "<?php echo $lcl_xs_x?>";
Dom.get('ucl_xs_s').value = "<?php echo $ucl_xs_s?>";
Dom.get('lsl_xs').value = "<?php echo $lsl_xs?>";
Dom.get('usl_xs').value = "<?php echo $usl_xs?>";

Dom.get('ucl_imr_x').value = "<?php echo $ucl_imr_x?>";
Dom.get('lcl_imr_x').value = "<?php echo $lcl_imr_x?>";
Dom.get('ucl_imr_r').value = "<?php echo $ucl_imr_r?>";
Dom.get('lsl_imr').value = "<?php echo $lsl_imr?>";
Dom.get('usl_imr').value = "<?php echo $usl_imr?>";

Dom.get('cl_p').value = "<?php echo $cl_p?>";

Dom.get('sampleSize_np').value = "<?php echo $sampleSize_np?>";
Dom.get('cl_np').value = "<?php echo $cl_np?>";

Dom.get('cl_u').value = "<?php echo $cl_u?>";

Dom.get('cl_c').value = "<?php echo $cl_c?>";

<?php if($action == 'e'){?>
	var noedit = ['noedit_chart_type','noedit_size_xr','noedit_size_xs'];
	Dom.setStyle(noedit,"visibility","visible");
	Dom.get('chart_selector').disabled = true;
	Dom.get('sampleSize_xr').readOnly = true;
	Dom.get('sampleSize_xs').readOnly = true;
	Dom.get('chart_selector_temp').value = <?php echo $chartType?>;
	
	Dom.get('name').value = "<?php echo $name?>";
<? }elseif($action == 'c'){?>
	Dom.get('name').value = "<?php echo $name.'*'?>";
<? }?>
</script>
