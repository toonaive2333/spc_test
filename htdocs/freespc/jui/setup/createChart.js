var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;

var chartSelector = Dom.get("chart_selector");
var chartTypes = ['','xr','xs','imr','p','np','u','c'];
var integerArea = ['smapleSize_xr','smapleSize_xs','smapleSize_np'];

var hideAll = function(){
	for(i=1;i<chartTypes.length;i++){
		Dom.setStyle("parameters_"+chartTypes[i],"display","none");
	}	
	Dom.setStyle("rules_8","display","none");
	Dom.setStyle("rules_4","display","none");
};

var showParameterPan = function(type){
	hideAll();		
	Dom.setStyle("parameters_"+chartTypes[type],"display","block");
	if( type<4 ){
		Dom.setStyle("rules_8","display","block");
	}else{
		Dom.setStyle("rules_4","display","block");
	}
};

var selectChart = function(){
	hideAll();
	type = chartSelector.value;
	showParameterPan(type);	
};

//只能输入正整数
//var keypress_int = function(){
//	return event.keyCode>=48&&event.keyCode<=57;
//}
//var paste = function(){
//	return false;
//}
//var dragenter = function(){
//	return false;
//}
//var keyup_int = function(){
//	if(/(^0+)/.test(this.value))
//		this.value=this.value.replace(/^0*/, '')
//}

var showParametersErrorTip = function(){
	Dom.setStyle("parameters_error_tip","display","block");
};

var checkAndSend = function(){
	Dom.setStyle("name_error_tip","display","none");
	Dom.setStyle("parameters_error_tip","display","none");
	
	var chartType = parseInt(Dom.get("chart_selector").value);
	var allOK = true;
	if(isNull(Dom.get("name").value)){
		Dom.setStyle("name_error_tip","display","block");
		allOK = false;
	}
	
	switch(chartType){
		case TYPE_XR:
			var sampleSize = Dom.get("sampleSize_xr").value;
			var lcl = Dom.get("lcl_xr_x").value; 
			var ucl = Dom.get("ucl_xr_x").value; 
			var ucl_r = Dom.get("ucl_xr_r").value;
			var lsl = Dom.get("lsl_xr").value;
			var usl = Dom.get("usl_xr").value; 
			
			if( (!isInt(sampleSize)) || (!isNumber(lcl)) || (!isNumber(ucl)) || (!isNumber(ucl_r)) || (!isNumber(lsl)) || (!isNumber(usl)) ){
				showParametersErrorTip();
				allOK = false;
			}
			if(lcl >= ucl){
				showParametersErrorTip();
				allOK = false;
			}
			if(lsl >= usl){
				showParametersErrorTip();
				allOK = false;
			}
			if(ucl_r <= 0){
				showParametersErrorTip();
				allOK = false;
			}
		break;
		case TYPE_XS:
			var sampleSize = Dom.get("sampleSize_xs").value;
			var lcl = Dom.get("lcl_xs_x").value; 
			var ucl = Dom.get("ucl_xs_x").value; 
			var ucl_s = Dom.get("ucl_xs_s").value;
			var lsl = Dom.get("lsl_xs").value;
			var usl = Dom.get("usl_xs").value; 
			
			if( (!isInt(sampleSize)) || (!isNumber(lcl)) || (!isNumber(ucl)) || (!isNumber(ucl_s)) || (!isNumber(lsl)) || (!isNumber(usl)) ){
				showParametersErrorTip();
				allOK = false;
			}
			if(lcl >= ucl){
				showParametersErrorTip();
				allOK = false;
			}
			if(lsl >= usl){
				showParametersErrorTip();
				allOK = false;
			}
			if(ucl_s <= 0){
				showParametersErrorTip();
				allOK = false;
			}		
		break;
		case TYPE_IMR:
			var lcl = Dom.get("lcl_imr_x").value; 
			var ucl = Dom.get("ucl_imr_x").value; 
			var ucl_r = Dom.get("ucl_imr_r").value;
			var lsl = Dom.get("lsl_imr").value;
			var usl = Dom.get("usl_imr").value; 
			
			if( (!isNumber(lcl)) || (!isNumber(ucl)) || (!isNumber(ucl_r)) || (!isNumber(lsl)) || (!isNumber(usl)) ){
				showParametersErrorTip();
				allOK = false;
			}
			if(lcl >= ucl){
				showParametersErrorTip();
				allOK = false;
			}
			if(lsl >= usl){
				showParametersErrorTip();
				allOK = false;
			}
			if(ucl_r <= 0){
				showParametersErrorTip();
				allOK = false;
			}		
		break;
		case TYPE_P:			
			var cl = Dom.get("cl_p").value; 
			
			if( (!isNumber(cl)) ){
				showParametersErrorTip();
				allOK = false;
			}
			if(cl <= 0 || cl >= 1){
				showParametersErrorTip();
				allOK = false;
			}
		break;
		case TYPE_NP:
			var sampleSize = Dom.get("sampleSize_np").value;
			var cl = Dom.get("cl_np").value; 
			
			if( (!isInt(sampleSize)) || (!isNumber(cl)) ){
				showParametersErrorTip();
				allOK = false;
			}
			if(cl <= 0){
				showParametersErrorTip();
				allOK = false;
			}
		break;
		case TYPE_U:
			var cl = Dom.get("cl_u").value; 
			
			if( (!isNumber(cl)) ){
				showParametersErrorTip();
				allOK = false;
			}
			if(cl <= 0){
				showParametersErrorTip();
				allOK = false;
			}
		break;
		case TYPE_C:
			var cl = Dom.get("cl_c").value; 
			
			if( (!isNumber(cl)) ){
				showParametersErrorTip();
				allOK = false;
			}
			if(cl <= 0){
				showParametersErrorTip();
				allOK = false;
			}		
		break;
	}
	if(allOK){
		var theForm = Dom.get("createForm");
		theForm.action = "createChart_handle.php";
		theForm.target = "_self";
		theForm.submit();
	}
};

var upload = function(){
	var theForm = Dom.get("createForm");
	theForm.action = "upload.php";
	theForm.target="upload_target";
	theForm.submit();
	
	Dom.get("uploader").style.display = "none";
	Dom.get("upfileName").innerHTML = "<img src='../img/loading_tiny.gif'/>";
};

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
};

var deleteUpload = function(){
	Dom.get("upfileName").innerHTML = "";
	Dom.get("fileName").value = "";
	Dom.get("linkName").value = "";
	Dom.get("deleteBt").style.display = "none";
	Dom.get("uploadRefresh").innerHTML = "<input name=uploader id=uploader type=file class=inputText style='width:383px; height:22px;float:left'/>";
	Event.addListener("uploader","change",upload);
};

Event.onDOMReady(function(){
	Event.addListener(chartSelector,"change",selectChart);
	Event.addListener("uploader","change",upload);
	Event.addListener("create_bt","click",checkAndSend);
	Event.addListener("deleteBt","click",deleteUpload);
});
