var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var members_pan_visible = false;

var showMembers = function(){
	if(!members_pan_visible){
		Dom.setStyle("members_pan","display","block");
		members_pan_visible = true;
		refreshParent();
	}else{
		Dom.setStyle("members_pan","display","none");
		members_pan_visible = false;
		refreshParent();
	}
};

var refreshParent = function(){
	//if(window.navigator.appName.indexOf("Microsoft") != -1 && document.body.scrollHeight>500)
		//parent.document.getElementById('chartProperty_window').height=document.body.scrollHeight;
};

function showConfirmPan(){
	 Dom.setStyle('confirm',"display","block");
	 refreshParent();
};

function hideConfirmPan(){
	 Dom.setStyle('confirm',"display","none");
	 refreshParent();
};

function showLog(logs,event,self){
	var offset=15;
	if(window.navigator.appName.indexOf("Microsoft") != -1)
		offset=181;
	if(logs == '')
		logs = "创建Chart";
	Dom.get('logDetails').innerHTML = logs;
	Dom.setStyle('logDetails','top',self.offsetTop+offset+'px');
	Dom.setStyle('logDetails','display','block');
	refreshParent();
};

function hideLog(){
	Dom.setStyle('logDetails','display','none');
};

Event.onDOMReady(function(){
	refreshParent();
	Event.addListener("show_members","click",showMembers);
	Event.addListener('delete_bt',"click",showConfirmPan);
	Event.addListener('cancel_bt',"click",hideConfirmPan);
});


