<?php
require_once('../load.php');
global $wpdb;
require_db();
?>
<div id="popupBg" style="position:absolute; top:0; left:0;filter:alpha(opacity=95);opacity:0.95; background-color:#FFFFFF; z-index:100;display:none;">
<div id="addWindow"  class="container" style="width:860px; margin-top:40px;">
	<div class="title"><strong>新增事件</strong></div>
	<div class="hr"></div>
	<div style="float:left; margin-top:15px;">
	<div><div class="item" style="width:100px">事件标题：</div><input type="text" name="name" id="name" class="inputText" style="width:604px; float:left"/><div id="title_error_tip" class="error_tip" style="width:100px;"><img src="../img/warning.gif" />请填写标题</div></div>
	<div style="clear:both; height:20px"></div>
	<div><div class="item" style="width:100px">责任Team：</div>
	<select name="team" id="team">	
<?php
$teams = "";
if($_COOKIE['login_isAdmin'] == 2){
	$teams = $wpdb->get_results("SELECT * FROM teams WHERE id IN (SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") ORDER BY CONVERT(name USING gbk)", ARRAY_A);
}else{
	$teams = $wpdb->get_results("SELECT * FROM teams ORDER BY CONVERT(name USING gbk)", ARRAY_A);
	echo "<option value=0>所有Team</option>";
}
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
	<div style="clear:both; height:20px"></div>
	<div><div class="item" style="width:100px"><span style="font-weight:normal;color:#3388DD">(可选)</span>描述：</div><textarea name="description" id="description" style="color:#000000;"></textarea></div>
	<div style="clear:both; height:20px"></div>
	<div id="sourcePan"><div class="item" style="width:100px">引用数据：</div><div id="source"></div></div>
	<div style="clear:both; height:20px"></div>
	<div class="item" style="width:100px">&nbsp;</div><input id="send_bt" type="button" class="bt" value="新建" style="width:100px"/>&nbsp;<img id="loading" src='../img/loading_tiny.gif'/>&nbsp;<input id="cancel_bt" type="button"  class="bt" value="取消"  style="width:100px"/></div>
	<div style="clear:both; height:20px"></div>
</div>
<div id="success" class="container"><img src="<?php echo $ABS_PATH?>img/ok.gif"/>新建事件完成</div>
</div>
<style>
.title{
	color:#006699;
}
.container textarea{
	font-size:12px;
	width:600px;
	height:70px;
	float:left;
	overflow:visible;
	border:#7F9DB9 solid 1px;
	margin-left:3px;
}
#sourcePan{
	display:none;
}
#source{
	width:740px;
	float:left
}
#header{
	margin-top:-16px;
}
#success{
	font-size:16;
	color:#006600;
	font-weight:bold;
	width:860px; 
	margin-top:80px;
	text-align:center;
	display:none;
}
#loading{
	visibility:hidden;
}
</style>
<script language="javascript">
var hasSource = false;
var popUp = function(title,source,teamID){
	popupLayer = document.getElementById("popupBg");
	popupLayer.style.width = document.body.scrollWidth+"px";
	popupLayer.style.height = document.body.scrollHeight+"px";
	popupLayer.style.display = "block";
		
	for(i=0;i<Dom.get('team').options.length;i++){
		if(Dom.get('team').options[i].value == teamID){
			Dom.get('team').options[i].selected = true;
			break;
		}
	}
	if(teamID>0){
		Dom.get('team').disabled = true;
	}
	
	if(title != ""){
		document.getElementById("name").value = title;
		hasSource = true;
	}
	
	if(source != ""){
		document.getElementById("source").innerHTML = source;
		document.getElementById("sourcePan").style.display = "block";
	}
}

var hide = function(){
	document.getElementById("popupBg").style.display = "none";
}

document.getElementById("cancel_bt").onclick = function(){
	hide();
	refreshCaller(0);
}

document.getElementById("name").onkeyup = function(){
	document.getElementById("title_error_tip").style.display = "none";
}

var fadeOut = function(){
	document.getElementById("addWindow").style.display = "block";
	document.getElementById("success").style.display = "none";
	hide();
	refreshCaller(1);
}

document.getElementById("send_bt").onclick = function(){
	var title = YAHOO.lang.trim(document.getElementById("name").value);
	var description = document.getElementById("description").value;
	if(title == ""){
		document.getElementById("title_error_tip").style.display = "block";
		return;
	}
	var sendBt = document.getElementById("send_bt");
	var cancelBt = document.getElementById("cancel_bt");
	sendBt.disabled = true;
	cancelBt.disabled = true;
	Dom.setStyle("loading","visibility","visible");
	
	var datas = "team="+document.getElementById("team").value+"&title="+title+"&description="+description+"&source="+result.replace(/&/g,"@@@");
	var handleSuccess = function(o){
		sendBt.disabled = false;
		cancelBt.disabled = false;	
		Dom.setStyle("loading","visibility","hidden");	
		document.getElementById("addWindow").style.display = "none";
		document.getElementById("success").style.display = "block";
		if(!hasSource){
			setTimeout(fadeOut,1000);			
		}else{
			document.getElementById("success").innerHTML = "<img src='"+ABS_PATH+"img/ok.gif'/>新建事件完成<br><br><br><div style='text-align:center'><form action='"+ABS_PATH+"event/event.php?id="+o.responseText+"' method=post>&nbsp;<input type=submit value='处理本事件'  class='bt' style='width:100px;'/>&nbsp;&nbsp;<input type='button' class='bt' value='返回' style='width:100px;' onclick='fadeOut();'/></form></div>";
		}
	};	
	var callback ={
	  success:handleSuccess
	  //failure: handleFailure,
	  //argument: ['foo','bar']
	};
	var request = YAHOO.util.Connect.asyncRequest('POST', ABS_PATH+'event/addEvent.php', callback, datas);
}
</script>