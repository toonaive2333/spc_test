var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var team_form = Dom.get("team_form");

function alter(){
	var status = this.checked;
	for(var i=0;i<team_form.length;i++){
		var e=team_form.elements[i];
		if(e.type=="checkbox" && (!e.disabled)){
			e.checked=status;
		}
	} 
};

var send = function(){
	var allOK = true;
	name = YAHOO.lang.trim(Dom.get('name').value);	
	description = YAHOO.lang.trim(Dom.get('description').value);
	
	if(name == ""){
		Dom.setStyle('team_error_tip','display','block');
		allOK = false;
	}
	if(description == ""){
		Dom.setStyle('description_error_tip','display','block');
		allOK = false;
	}
	if(allOK)
		Dom.get('team_form').submit();
};


Event.onDOMReady(function(){
	chechBt = Dom.get('ckecker');
	Event.addListener(chechBt,"click",alter);
	Event.addListener('send_bt',"click",send);
});