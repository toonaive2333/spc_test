var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var valid = false;

var popMenu = function(){
	Dom.setStyle("charts_menu","visibility","visible");
};

var hideMenu = function(){
	if(!valid)
		Dom.setStyle("charts_menu","visibility","hidden");
};

var fadeMenu = function(){
	setTimeout(hideMenu,500);
};

var showChartList = function(id){
	var status = Dom.getStyle("charts_"+id,"display");
	if(status == "none"){
		Dom.setStyle("charts_"+id,"display","block");
	}
	if(status == "block"){
		Dom.setStyle("charts_"+id,"display","none");
	}
};

var showChart = function(id){
	Dom.setStyle("charts_menu","visibility","hidden");
	Dom.setStyle("refresh","visibility","visible");
	Dom.get("current").innerHTML = 'Chart属性';
	Dom.get("chartProperty_window").src = "chartDetail.php?id="+id;
};

var refreshWindow = function(id){
	Dom.get("chartProperty_window").contentWindow.location.reload();
};

Event.onDOMReady(function(){
	Event.addListener(["current","arrow"],"mouseover",popMenu);
	Event.addListener(["current","arrow"],"mouseout",fadeMenu);
	Event.addListener(["current","arrow"],"mousemove",popMenu);
	Event.addListener("charts_menu","mouseover",function(){ valid=true });
	Event.addListener("charts_menu","mouseout",function(){ valid=false;fadeMenu(); });
	Event.addListener("refresh","click",refreshWindow);
});
