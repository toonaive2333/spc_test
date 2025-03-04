<?php
$needAuthenticate = true;
require_once('../load.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置 &rsaquo; 录入数据</title>
</head>

<body>
<?php
showMenuBar('record');
?>

<!--body-->
<input type="text" id="tab_temp" style="width:0px;height:0px;border:0; position:absolute"/>
<div class="container">	
	<div id="current_chart_title"></div>&nbsp;
	<span id="chart_description"></span>
	<div class="hr"></div>	
<!--input panel-->	
	<div style="width:420px; float:left">
		<div id="chart_parameters"></div>
 <!--input area for xr,xs chart-->
		<div id="input_area_xchart" style="display:none">
			<div style="float:left"><div class="label_2">样本值<span id="sample_size_x"></span></div>
				<textarea id="sampleValue_x" rows="16"  wrap="off"></textarea></div>
			<div class="spacer_1"><br /><br />用换行或空格来区隔两个数据</div>
			<div style="float:left">
			  <div class="label_3">样本标识(&lt;20位,可选)</div>
			  <textarea id="sampleID_x" rows="16" wrap="off"></textarea>
			</div>				
		</div>
 <!--input area for imr chart-->
		<div id="input_area_imrchart" style="display:none;margin-top:15px;">
			<div style="float:left"><div class="label_2">样本值</div>
				<input type="text" id="sampleValue_imr" /></div>
			<div class="spacer_1"></div>
			<div style="float:left">
			  <div class="label_3">样本标识(&lt;20位,可选)</div>
			  <textarea id="sampleID_imr" rows="1" wrap="off"></textarea>
			</div>			
		</div>
 <!--input area for p chart-->
		<div id="input_area_pchart" style="display:none;margin-top:15px;">
			<div style="float:left"><div class="label_2">不良数</div><input type="text" id="sampleValue_p"/><br/><br/>
				<div class="label_2">子组大小</div><input type="text" id="sampleSize_p"/></div>
			<div class="spacer_1"></div>
			<div style="float:left">
			  <div class="label_3">样本标识(&lt;20位,可选)</div>
			  <textarea id="sampleID_p" rows="5" wrap="off"></textarea>
			</div>
		</div>
 <!--input area for np chart-->
		<div id="input_area_npchart" style="display:none;margin-top:15px;">
			<div style="float:left"><div class="label_2">不良数</div><input type="text" id="sampleValue_np"/><br/><br/>
				<div class="label_2">子组大小</div><input type="text" id="sampleSize_np" readonly="true"/></div>
			<div class="spacer_1"></div>
			<div style="float:left">
			  <div class="label_3">样本标识(&lt;20位,可选)</div>
			  <textarea id="sampleID_np" rows="5" wrap="off"></textarea>
			</div>
		</div>
 <!--input area for u chart-->
		<div id="input_area_uchart" style="display:none;margin-top:15px;">
			<div style="float:left"><div class="label_2">缺陷数</div><input type="text" id="sampleValue_u"/><br/><br/>
				<div class="label_2">子组大小</div><input type="text" id="sampleSize_u"/></div>
			<div class="spacer_1"></div>
			<div style="float:left">
			  <div class="label_3">样本标识(&lt;20位,可选)</div>
			  <textarea id="sampleID_u" rows="5" wrap="off"></textarea>
			</div>
		</div>
 <!--input area for c chart-->
		<div id="input_area_cchart" style="display:none;margin-top:15px;">
			<div style="float:left"><div class="label_2">缺陷数</div><input type="text" id="sampleValue_c"/><br/></div>
			<div class="spacer_1"></div>
			<div style="float:left">
			  <div class="label_3">样本标识(&lt;20位,可选)</div>
			  <textarea id="sampleID_c" rows="1" wrap="off"></textarea>
			</div>
		</div>
 <!--end pans-->
 		<div id="errorPan" class="error_tip"><img src="../img/warning.gif" /><strong>出现错误：</strong>
			<div id="error_content"></div>
		</div>
		<div id="completePan"><img src="../img/ok.gif" /><strong>所有Chart均录入完成。</strong></div>
		<div id="completePan2"><img src="../img/ok.gif" /><strong>当前Chart录入完成。</strong></div>
		<div id="help_tip" style="width:355px;">Tab键切换，Ctrl+Enter键提交</div>	
		<div><input type="button" id="recordBt" title="可以使用Shift+Enter快捷键" value="提交并录入下一Chart"/></div>	
	</div>			
	

<!--chart list-->
	<div id="list_container">
		<div id="label_4" style="float:left"><table><tr><td>录入顺序</td><td><img id="loading" src="../img/loading_tiny.gif" /></td><td id="save_bt">保存顺序</td><td id='shifter'></td></tr></table></div>
		<div class="hr"></div>
		<ul id="List">
<?php
global $wpdb;
require_db();
$teams = "";
$chartsList = array();
if($_COOKIE['login_isAdmin'] == 2)
	$teams = $wpdb->get_results("SELECT * FROM teams WHERE id IN (SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") ORDER BY CONVERT(name USING gbk)", ARRAY_A);
else
	$teams = $wpdb->get_results("SELECT * FROM teams ORDER BY CONVERT(name USING gbk)", ARRAY_A);
if(is_array($teams)){
	foreach($teams as $team){
		$charts = $wpdb->get_results("SELECT * FROM charts WHERE team=".$team['id']." ORDER BY CONVERT(name USING gbk)", ARRAY_A);
		if(is_array($charts)){
			foreach($charts as $chart){
				$chartsList = $chartsList+array($chart['id']=>$chart['name']);
			}
		}
	}
}

$i = 1;
$cookieName = '';
if($_COOKIE['login_isAdmin'] == 2){
	$cookieName = 'recordOrder_'.$_COOKIE['login_id'];
}else{
	$cookieName = 'recordOrder_admin';
}
if(empty($_COOKIE[$cookieName])){
	showNotInCookie();
}else{	
	$order = explode('|',substr($_COOKIE[$cookieName],0,-2));//1|3|8|9|2|0|	
	foreach($order as $a){
		if( !empty($chartsList[$a]) ){			
			echo "<li id='item$i'>";
			echo "<img src='../img/rec.gif' title='录入数据' onclick='setCurrent($i)'/><b>$a</b>".$chartsList[$a];
			echo "</li>";
			$i++;
			$chartsList = array_diff($chartsList,array($a=>$chartsList[$a]));
		}
	}
	showNotInCookie();
}

$manual = $_COOKIE['manual'];
if($manual == 1){
	echo "<script>document.getElementById('shifter').innerHTML = '手动';";
	echo "var manual = 1;</script>";
}else{
	echo "<script>document.getElementById('shifter').innerHTML = '自动';";
	echo "var manual = 2;</script>";//自动：manual = 2
}


function showNotInCookie(){
	global $i;
	global $chartsList;
	foreach($chartsList as $chartId => $chartName){
		echo "<li id='item$i'>";
		echo "<img src='../img/rec.gif' title='录入数据' onclick='setCurrent($i)'/><b>$chartId</b>".$chartName;
		echo "</li>";
		$i++;
	}
}
echo "<script>var total = $i;</script>";
?>  
		<li id='item0' style="display:none"><b>0</b></li>
		</ul>
	</div>	
<div style="clear:both"></div>
</div>
<!--end of body-->
<?php showBottom();?>
</body>
</html>
<script type="text/javascript" src="../jui/util/dragdrop-min.js"></script>
<script type="text/javascript" src="../jui/util/connection-min.js"></script>
<script type="text/javascript" src="../jui/record/record.js"></script>
<link rel="stylesheet" type="text/css" href="../css/record.css" />