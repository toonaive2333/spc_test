var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
/*var sendBt,
	emails,
	content,
	isWorking = false;
	
var handleSuccess = function(o){	
	isWorking = false;
	if(o.responseText !== undefined){
		var r = o.responseText;
		if(r == 1)
			popTip("","全部发送完成");
		else
			popTip("","完成，但部分邮件未发出(请检查拼写)");
	}
	hideTip();
}

var handleFailure = function(){
	isWorking = false;
	popTip("error","发送失败，请检查网络。");
	hideTip();
}

var callback = {
  success:handleSuccess,
  failure:handleFailure,
  argument: ['foo','bar']
};

var send = function(){
	if(!isWorking){		
		emails = YAHOO.lang.trim(Dom.get('emails').value);	
		content = YAHOO.lang.trim(Dom.get('content').value);
		if(emails == "" || content==""){
			popTip("error","注意：Email地址、邮件内容均不能为空。");
			hideTip();
			return;
		}
		isWorking = true;
		popTip("loading","正在发送邀请邮件...");
		var request = YAHOO.util.Connect.asyncRequest('POST', "invite_handle.php", callback, "emails="+emails+"&content="+content); 
	}
}
*/
var send = function(){
	var allOK = true;
	emails = YAHOO.lang.trim(Dom.get('emails').value);	
	content = YAHOO.lang.trim(Dom.get('content').value);
	
	if(emails == ""){
		Dom.setStyle('emails_error_tip','display','block');
		allOK = false;
	}
	if(content == ""){
		Dom.setStyle('content_error_tip','display','block');
		allOK = false;
	}
	if(allOK)
		Dom.get('invite_form').submit();
};
Event.onDOMReady(function(){
	sendBt = Dom.get('invite_bt');
	Event.addListener(sendBt,"click",send);
});