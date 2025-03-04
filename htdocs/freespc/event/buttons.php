<div id="buttons"><div id="add_task">新建任务</div><div id="add_report">发表评论</div><div id="add_meeting">会议记录</div><div id="add_file">上传文件</div></div>
<div id="popupBg" style="position:absolute; top:0; left:0;opacity:0.95; background-color:#FFFFFF; border:1 #FF0000 solid; z-index:100;display:none">
<div class="container2" id="window">
<!--add task-->
	<div id="task_pan">
		<div class="title"><strong>新建任务</strong></div>
		<div class="hr"></div>
		<div style="float:left; margin-top:15px;">
			<div><div class="item" style="width:100px">任务：</div><input type="text" name="task" id="task" class="inputText" style="width:604px; float:left"/><div id="task_title_error_tip" class="error_tip" style="width:100px;"><img src="../img/warning.gif" />请填写任务</div></div>
			<div style="clear:both; height:20px"></div>
			<div><div class="item" style="width:100px"><span style="font-weight:normal;color:#3388DD">(可选)</span>描述：</div><textarea id="task_description"></textarea></div>	
			<div style="clear:both; height:20px"></div>
			<div><div class="item" style="width:100px">负责人：</div>
			<select id="responser">
			
<?php
$team = $wpdb->get_var("SELECT team FROM events WHERE id=$id");
if($team == 0){
	$members = $wpdb->get_results("SELECT email,nickname FROM members",ARRAY_A);
}else{
	$members = $wpdb->get_results("SELECT email,nickname FROM members WHERE id IN (SELECT member_id FROM members_team WHERE team_id=$team)",ARRAY_A);
}
if($_COOKIE['login_isAdmin'] == 2 && $team > 0){
	echo "<option value='all'>任何Team成员</option>";
}else{
	echo "<option value='all'>任何人</option>";
}
if(is_array($members)){
	foreach($members as $member){
		echo "<option value='".$member['email']."'>".$member['nickname']."</option>";
	}
}
?>			</select>
			</div>
			<div style="clear:both; height:20px"></div>
			<div><div class="item" style="width:100px">截止日期：</div>       
			<select id="endDate" class="endDate">
			  <option value="later">不定</option>
			  <option value="today">今天</option>
			  <option value="tomorrow">明天</option>
			  <option value="thisweek">本周</option>
			  <option value="nextweek">下周</option>
			  <option value="exact">指定一个确切日期...</option>
			</select>
			</div>
			<br />
			<div style="position:absolute"><div class="item" style="width:100px;"></div> 
				<div id="exact_pan"><div id="date" title="截止时间"></div><div id="calendar_container"></div></div>
				<div id="closer"><img src="../jui/calendarBar/close.gif" width="11" height="11"/></div>
			</div>
			<br />
		</div>
	</div>
	
<!--add report-->
	<div id="report_pan">
		<div class="title"><strong>发表评论</strong></div>
		<div class="hr"></div>
		<div style="float:left; margin-top:15px;">
			<div><div class="item" style="width:100px">标题：</div><input type="text" name="report_title" id="report_title" class="inputText" style="width:604px; float:left"/><div id="report_title_error_tip" class="error_tip" style="width:100px;"><img src="../img/warning.gif" />请填写标题</div></div>
			<div style="clear:both; height:20px"></div>
			<div><div class="item" style="width:100px"><span style="font-weight:normal;color:#3388DD">(可选)</span>内容：</div><textarea name="report" id="report"></textarea></div>			
		</div>
	</div>
	
<!--add meeting-->
	<link rel="stylesheet" type="text/css" href="../jui/rte/simpleeditor.css" />
	<link rel="stylesheet" type="text/css" href="../jui/rte/editor.css" />
	<script type="text/javascript" src="../jui/rte/element-beta-min.js"></script>
	<script type="text/javascript" src="../jui/rte/container_core-min.js"></script>
	<script type="text/javascript" src="../jui/rte/editor-min.js"></script>
	<div id="meeting_pan">
		<div class="title"><strong>会议记录存档</strong></div>
		<div class="hr"></div>
		<div style="float:left; margin-top:15px;">
			<div><div class="item" style="width:100px">会议主题：</div><input type="text" name="meeting_title" id="meeting_title" class="inputText" style="width:604px; float:left"/><div id="meeting_title_error_tip" class="error_tip" style="width:100px;"><img src="../img/warning.gif" />请填写主题</div></div>
			<div style="clear:both; height:20px"></div>			
			<div><div class="item" style="width:100px">会议内容：</div><div style="float:left"><img src="../img/loading_tiny.gif" id="load_rte"/><textarea name="meeting_content" id="meeting_content"><strong>时间</strong>：<br><strong>地点</strong>：<br><strong>与会人员</strong>：<br><strong>内容</strong>：<br></textarea></div></div>			
		</div>
	</div>
	
<!--add file-->
	<div id="file_pan">
		<div class="title"><strong>上传文件</strong></div>
		<div class="hr"></div>
		<div style="float:left; margin-top:15px;">
			<div><div class="item" style="width:100px">文件：</div>			
				<form action="upload.php" enctype="multipart/form-data" id="createForm" target="upload_target" method="post">
				<div id="upfileName"></div><div id="deleteBt"></div>
				<input type="hidden" name="fileName" id="fileName" />
				<input type="hidden" name="linkName" id="linkName" />
				<span id="uploadRefresh">
				<input name="uploader" id="uploader" type="file" class="inputText" style="width:386px; height:22px;float:left"/>
				</span><div id="file_title_error_tip" class="error_tip" style="width:100px;"><img src="../img/warning.gif" />请上传文件</div>
				<iframe id="upload_target" name="upload_target" src="#" style="display:none"></iframe>		
				</form>		
			
			</div>
			<div style="clear:both; height:20px"></div>
			<div><div class="item" style="width:100px"><span style="font-weight:normal;color:#3388DD">(可选)</span>描述：</div><textarea name="file_description" id="file_description"></textarea></div>		
		</div>
	</div>

<!--submit-->
<div style="clear:both; height:20px"></div>
<div id="submit_pan"><div class="item" style="width:100px">&nbsp;</div><input id="send_bt" type="button" class="bt" value="提交" style="width:100px"/>&nbsp;<img id="loading" src='../img/loading_tiny.gif'/>&nbsp;<input id="cancel_bt" type="button"  class="bt" value="取消"  style="width:100px"/></div>
<div style="clear:both; height:20px"></div>
</div>
<!--cant add-->
<div id="cantAdd_pan" class="container"><img src="../img/warn.gif"/>&nbsp;&nbsp;事件已经结案，不能再添加项目。<br /><br /><input type="button" class="bt" value="返回" style="width:100px" onclick="fadeOut();"/></div>
<!--complete-->
<div id="success" class="container"><img src="../img/ok.gif"/>新建完成</div>
</div>
<style>
#buttons{
	margin-top:-10px;
	float:right;	
}
#buttons div{
	background-color:#DDEEFF;
	text-align:center;
	width:70px;
	font-size:14px;
	padding-top:3px;
	cursor:pointer;
	margin-left:20px;
	float:left;
}
.title{
	color:#006699;
}
.container2{
	border:#DBDBDB 1px solid;
	background-color:#FFFFFF;
	margin:0px auto;
	padding:20px;
	width:860px; 
	margin-top:60px;
}
#report,#file_description,#task_description{
	font-size:12px;
	width:600px;
	height:100px;
	float:left;
	overflow:visible;
	border:#7F9DB9 solid 1px;
	margin-left:3px;
}
#success,#cantAdd_pan{
	font-size:16;
	color:#006600;
	font-weight:bold;
	width:860px; 
	margin-top:80px;
	text-align:center;
	display:none;
}
#report_pan,#task_pan,#exact_pan,#meeting_pan,#file_pan{
	display:none;
}
#exact_pan{
	float:left;
}
#meeting_content{
	width:680px;
	height:300px;
	display:none;
}
#deleteBt{
	cursor:pointer;
	font-size:12px;
	display:none;
	float:left;
}
#upfileName{
	float:left;
}
#upfileName a{
	color:#3388DD;
	text-decoration:none
}
#upfileName a:hover{
	text-decoration:underline
}
.upfileName{
	color:#3388DD;
	text-decoration:none
}
#loading{
	visibility:hidden;
}
#load_rte{
	display:none;
}
</style>
<script language="javascript">
var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var current = "";
//task
var now = new Date();
Dom.get("date").innerHTML=now.getFullYear()+"/"+(now.getMonth()+1)+"/"+now.getDate();
Dom.get("endDate").onchange = function(){
	var self = Dom.get("endDate");
	if(self.options[self.selectedIndex].value == 'exact')
		Dom.get("exact_pan").style.display = "block";
	else
		Dom.get("exact_pan").style.display = "none";
}
//rte
var toolbar = {
	collapse: true,
	draggable: false,
	buttons: [
		{ group: 'fontstyle', label: " ",
			buttons: [
				{ type: 'spin', label: '13', value: 'fontsize', range: [ 9, 75 ]},
				{ type: 'push', label: '加粗', value: 'bold' },
				{ type: 'push', label: '倾斜', value: 'italic' },
				{ type: 'push', label: '下划线', value: 'underline' },
				{ type: 'push', label: '删除线', value: 'strikethrough' },
				{ type: 'separator' },
				{ type: 'push', label: '项目符号', value: 'insertunorderedlist' },
				{ type: 'push', label: '编号', value: 'insertorderedlist' },
				{ type: 'separator' },
				{ type: 'color', label: '字体颜色', value: 'forecolor', disabled: true },
				{ type: 'color', label: '突出显示', value: 'backcolor', disabled: true },
				{ type: 'separator' },
				{ type: 'push', label: '撤销', value: 'undo', disabled: true },
                { type: 'push', label: '恢复', value: 'redo', disabled: true }
			]
		}                     
	]
};

var myConfig = {
	height: '300px',
	width: '680px',
	dompath: false,
	focusAtStart: true,
	autoHeight: true,
	toolbar:toolbar
};
var myEditor = null;	
//upload file
var upload = function(){
	Dom.get("file_title_error_tip").style.display = "none";
	var theForm = Dom.get("createForm");
	theForm.submit();
	
	Dom.get("uploader").style.display = "none";
	Dom.get("upfileName").innerHTML = "<img src='../img/loading_tiny.gif'/>";
}
var stopUpload = function(succuss,oName,linkName){
	if(succuss == 1){
		Dom.get("upfileName").innerHTML = "<a target=_blank href='../upload/"+linkName+"'>"+oName+"</a>";
		Dom.get("fileName").value = oName;
		Dom.get("linkName").value = linkName;
		Dom.get("deleteBt").innerHTML = "<img src='../img/delete2.gif'>删除";
		Dom.get("deleteBt").style.display = "block";
	}else{
		Dom.get("upfileName").innerHTML = "<img src='../img/warning3.gif'>上传失败，请重试。";
		Dom.get("deleteBt").innerHTML = "<img src='../img/refresh2.gif'>重试";
		Dom.get("deleteBt").style.display = "block";
	}
}
var deleteUpload = function(){
	Dom.get("upfileName").innerHTML = "";
	Dom.get("fileName").value = "";
	Dom.get("linkName").value = "";
	Dom.get("deleteBt").style.display = "none";
	Dom.get("uploadRefresh").innerHTML = "<input name=uploader id=uploader type=file class=inputText style='width:383px; height:22px;float:left'/>";
	Event.addListener("uploader","change",upload);
}
Event.addListener("uploader","change",upload);
Event.addListener("deleteBt","click",deleteUpload);
//send
var fadeOut = function(){	
	Dom.setStyle("submit_pan","display","block");
	Dom.setStyle("success","display","none");
	Dom.setStyle("window","display","block");
	hide();
	current = '';
	refreshCaller();
}
var handleSuccess = function(o){
	var sendBt = document.getElementById("send_bt");
	var cancelBt = document.getElementById("cancel_bt");
	sendBt.disabled = false;
	cancelBt.disabled = false;		
	Dom.setStyle("loading","visibility","hidden");	
	Dom.setStyle("window","display","none");	
	Dom.setStyle("submit_pan","display","none");
	Dom.setStyle("success","display","block");
	setTimeout(fadeOut,1000);
};	
var callback ={
  success:handleSuccess
  //failure: handleFailure,
  //argument: ['foo','bar']
};	
Dom.get("send_bt").onclick = function(){
	switch(current){
		case 'task':
			var res = document.getElementById("responser");
			var task = YAHOO.lang.trim(document.getElementById("task").value);
			var rEmail = res.value;
			var responser = res.options[res.selectedIndex].text
			if(task == ""){
				Dom.get("task_title_error_tip").style.display = "block";
				return;
			}
			if(responser == ""){
				responser = "任何人";
			}
			
			var datas = "event="+currentEvent+"&teamId="+teamId+"&task="+task+"&rEmail="+rEmail+"&responser="+responser+"&description="+Dom.get('task_description').value;
			var end = Dom.get("endDate");
			if(end.value == 'exact'){
				datas += "&expire="+Dom.get('date').innerHTML;
			}else{
				datas += "&expire="+end.value;
			}
			YAHOO.util.Connect.asyncRequest('POST', 'addTask.php', callback, datas);
		break;
		case 'report':
			var report_title = YAHOO.lang.trim(document.getElementById("report_title").value);
			if(report_title == ""){
				Dom.get("report_title_error_tip").style.display = "block";
				return;
			}
			var datas = "event="+currentEvent+"&title="+report_title+"&content="+Dom.get('report').value;			
			YAHOO.util.Connect.asyncRequest('POST', 'addReport.php', callback, datas);
		break;
		case 'meeting':
			var meeting_title = YAHOO.lang.trim(document.getElementById("meeting_title").value);
			if(meeting_title == ""){
				Dom.get("meeting_title_error_tip").style.display = "block";
				return;
			}
			myEditor.saveHTML();
			var datas = "event="+currentEvent+"&title="+meeting_title+"&content="+myEditor.get('textarea').value.replace(/&\S+;/g," ").replace(/<!--\S+-->/g,"").replace(/&/g," ");		
			YAHOO.util.Connect.asyncRequest('POST', 'addMeeting.php', callback, datas);
		break;
		case 'file':
			var fileName = YAHOO.lang.trim(document.getElementById("fileName").value);
			if(fileName == ""){
				Dom.get("file_title_error_tip").style.display = "block";
				return;
			}
			var datas = "event="+currentEvent+"&fileName="+fileName+"&linkName="+Dom.get('linkName').value+"&content="+Dom.get('file_description').value;;			
			YAHOO.util.Connect.asyncRequest('POST', 'addFile.php', callback, datas);
		break;
	}
		
	var sendBt = document.getElementById("send_bt");
	var cancelBt = document.getElementById("cancel_bt");
	sendBt.disabled = true;
	cancelBt.disabled = true;
	Dom.setStyle("loading","visibility","visible");	
}
//util
var hide = function(){
	Dom.get("popupBg").style.display = "none";	
	Dom.get(current+"_pan").style.display = "none";	
	Dom.get("task_pan").style.visibility = "hidden";
	current = "";
	if(myEditor != null){
		myEditor.destroy();
		myEditor = null;
	}	
};
var popBg = function(){
	popupLayer = Dom.get("popupBg");
	popupLayer.style.width = document.body.scrollWidth+"px";
	popupLayer.style.height = document.body.scrollHeight+"px";
	popupLayer.style.display = "block";
};
var popUp = function(action){
	popBg();	
	current = action;
	Dom.get(action+"_pan").style.display = "block";
};
var cantAdd = function(){
	Dom.setStyle("window","display","none");	
	Dom.setStyle("submit_pan","display","none");
	popUp('cantAdd');
}
Dom.get("add_report").onclick = function(){
	if(status == 1){
		cantAdd();
		return;
	}
	popUp('report');
	current = 'report';
}
Dom.get("add_task").onclick = function(){
	if(status == 1){
		cantAdd();
		return;
	}
	popUp('task');
	Dom.get("task_pan").style.visibility = "visible";
	current = 'task';
}
Dom.get("add_meeting").onclick = function(){
	if(status == 1){
		cantAdd();
		return;
	}
	Dom.setStyle("load_rte","display","block");
	myEditor = new YAHOO.widget.Editor('meeting_content', myConfig)
	myEditor.render();
	popUp('meeting');
	current = 'meeting';
	setTimeout(function(){
				Dom.setStyle("load_rte","display","none");
				},1000);
}
Dom.get("add_file").onclick = function(){
	if(status == 1){
		cantAdd();
		return;
	}
	popUp('file');
	current = 'file';
}
Dom.get("cancel_bt").onclick = function(){
	hide();
}
//
Dom.get("task").onkeyup = function(){
	Dom.get("task_title_error_tip").style.display = "none";
}
Dom.get("report_title").onkeyup = function(){
	Dom.get("report_title_error_tip").style.display = "none";
}
Dom.get("meeting_title").onkeyup = function(){
	Dom.get("meeting_title_error_tip").style.display = "none";
}
</script>

<script type="text/javascript" src="../jui/calendarBar/calendar.js"></script>
<script type="text/javascript" src="../jui/miniCalendar/miniCalendarBar.js"></script>
<link rel="stylesheet" type="text/css" href="../jui/miniCalendar/style.css"/>	