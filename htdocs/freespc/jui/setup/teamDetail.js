var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var delete_bt = Dom.get("delete_bt");
var cancel_bt = Dom.get("cancel_bt");
var confirm_Pan = Dom.get("confirm");

function showConfirmPan(){
	 Dom.setStyle(confirm_Pan,"display","block");
};

function hideConfirmPan(){
	 Dom.setStyle(confirm_Pan,"display","none");
};

Event.onDOMReady(function(){
	Event.addListener(delete_bt,"click",showConfirmPan);
	Event.addListener(cancel_bt,"click",hideConfirmPan);
});