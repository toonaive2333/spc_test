<div id="popupBg2" style="position:absolute; top:0; left:0;filter:alpha(opacity=95);opacity:0.95; background-color:#FFFFFF; border:1 #FF0000 solid; z-index:100;display:none">
<!--item-->
	<div id="item_pan" class="container2">
		<img src='../img/little_chart2.gif'/>
	</div>
<!--complete-->
<div id="cantClose" class="container"><img src="../img/warn.gif"/>&nbsp;&nbsp;由于事件中包含未完成的任务，暂时不能结案。<br /><br /><input type="button" class="bt" value="返回" style="width:100px" onclick="hide2();"/></div>
</div>
<style>
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
#cantClose{
	font-size:16;
	color:#006600;
	font-weight:bold;
	width:860px; 
	margin-top:80px;
	text-align:center;
	display:none;
}
#item_pan{
	display:none;
}
.item2{
	width:100px;
	text-align:right;
	font-weight:bold;
	float:left
}
.line{
	clear:both;
	margin:10px;
}
.tips{
	font-family:Arial;
	color:#3388DD;
}
.createTime{
	color:#666666;
	font-size:12px;
}
.content{
	width:730px;
	border:#7F9DB9 solid 1px;
	padding:5px;
	float:left;
}
.download{
	margin-top:30px;
	padding:5px;
	background-color:#DDEEFF;
	width:50px;
	text-align:center;
	float:left;
}
.download a{
	text-decoration:none;
	font-weight:bold;
	color:#333333;
}
</style>
<script language="javascript">
var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var currentId;
var taskCompleted = 0;

var hide2 = function(){
	Dom.setStyle('popupBg2','display','none');
	Dom.setStyle('cantClose','display','none');
	Dom.setStyle('item_pan','display','none');	
}

var popOut = function(pan){
	window.scrollBy(0,-1000);
	Dom.setStyle('popupBg2','display','block');
	Dom.setStyle(pan,'display','block');
	Dom.get('popupBg2').style.width = document.body.scrollWidth+"px";
	Dom.get('popupBg2').style.height = document.body.scrollHeight+"px";
}

var handleSuccess4 = function(o){
	Dom.get('item_pan').innerHTML = o.responseText;
	if(Dom.get('completed') && status == 1){
		Dom.get('completed').disabled = true;
	}
	if(showEvent == 1)
		Dom.setStyle('parentEvent','display','block');
};	
var callback4 ={
  success:handleSuccess4
  //failure: handleFailure,
  //argument: ['foo','bar']
};
var show = function(id,type){
	currentId = id;
	popOut("item_pan");
	Dom.get('item_pan').innerHTML = "<img src='../img/loading_tiny.gif'/>";
	var datas = "id="+id+"&type="+type;			
	YAHOO.util.Connect.asyncRequest('POST', 'getItem.php', callback4, datas);
}
//change status
var handleSuccess5 = function(o){	
	if(taskCompleted == 1){
		alterComplete();
		Dom.get('lastEdit').innerHTML = o.responseText;
	}else{
		alterGoing();
		Dom.get('lastEdit').innerHTML = "";
	}	
	Dom.setStyle('completeAlter','display','block');
	Dom.setStyle('loading_2','display','none');
	setTimeout(function(){
							hide2();
							refreshCaller();
						 },1000);
}
var callback5 ={
  success:handleSuccess5
  //failure: handleFailure,
  //argument: ['foo','bar']
}
var changeTask = function(){
	if(Dom.get('completed').checked)
		taskCompleted = 1;
	else
		taskCompleted = 0;
	Dom.setStyle('completeAlter','display','none');
	Dom.setStyle('loading_2','display','block');

	var datas = "id="+currentId+"&completed="+taskCompleted;			
	YAHOO.util.Connect.asyncRequest('POST', 'completeTask.php', callback5, datas);
}
var alterComplete = function(){
	Dom.get('completed').checked = true;
	Dom.get('completeFlag').innerHTML = "已完成";
	Dom.get('completeFlag').style.color = "#009933";
}
var alterGoing = function(){
	Dom.get('completed').checked = false;
	Dom.get('completeFlag').innerHTML = "未完成";
	Dom.get('completeFlag').style.color = "#666666";
}
var showSource = function(){
	popOut("item_pan");
	Dom.get('item_pan').innerHTML = "<div class='title'>引用数据</div><div class='hr'></div><br>" + source + "<input type=button class=bt value='返回' style='width:100px;' onclick='hide2();'/><br><br>";
}
</script>