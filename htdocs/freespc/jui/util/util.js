var ABS_PATH="/freespc/";var TYPE_XR=1;var TYPE_XS=2;var TYPE_IMR=3;var TYPE_P=4;var TYPE_NP=5;var TYPE_U=6;var TYPE_C=7;var chartTypes=['','Xbar-R','Xbar-S','I-MR','P-Chart','NP-Chart','U-Chart','C-Chart'];var popTip=function(a,b){var c=Dom.get('loadingTip');var d=Dom.get('tip');var e=Dom.get('tip_logo');Dom.setStyle(e,"display","block");switch(a){case'loading':e.src=ABS_PATH+"img/loading_tiny.gif";break;case'error':e.src=ABS_PATH+"img/error.gif";break;default:Dom.setStyle(e,"display","none");break}d.innerHTML=b;Dom.setStyle(c,"display","block");Dom.setStyle(c,"left",(document.body.scrollWidth-300)/2+"px");scroll(0,0)};var hideTip=function(){var a=Dom.get('loadingTip');setTimeout(function(){Dom.setStyle(a,"display","none")},5000)};var isZeroInt=function(v){if(parseInt(v)==v&&eval("'"+parseInt(v)+"'.length")==v.length&&v>=0)return true;else return false};var isInt=function(v){if(parseInt(v)==v&&eval("'"+parseInt(v)+"'.length")==v.length&&v>1)return true;else return false};var isNull=function(v){return(YAHOO.lang.trim(v)=="")};var isNumber=function(v){return((!isNull(v))&&(!isNaN(v)))};Chart=function(a){this.root=null;this.init(a)};Chart.prototype.init=function(a){try{this.xmlDoc=new ActiveXObject("Microsoft.XMLDOM")}catch(e){try{this.xmlDoc=document.implementation.createDocument("","",null)}catch(e){return null}}try{this.xmlDoc.async=false;this.xmlDoc.load(a)}catch(e){return null}};Chart.prototype.getParameters=function(a){var b=this.xmlDoc.documentElement;var d=b.getElementsByTagName("chart");var e=false;for(var i=0;i<d.length;i++){var c=d[i];if(c.getAttribute("id")==a){e=true;this.root=c;break}}if(!e)return null;var f=new Array();var g=this.root.childNodes;f['id']=this.root.getAttribute("id");f['type']=this.root.getAttribute("type");f['name']=this.root.getAttribute("name");f['team']=this.root.getAttribute("team");f['team_name']=this.root.getAttribute("team_name");for(var i=0;i<g.length;i++){var p=g[i];if(p.tagName!='logs'&&p.firstChild!=null)f[p.tagName]=p.firstChild.data}return f};
// 修改 Chart 初始化函数，使用跨浏览器兼容的方式创建 XML 对象
function Chartinit() {
    // 跨浏览器创建 XMLHttpRequest 对象
    this.createXMLHttpRequest = function() {
        if (window.XMLHttpRequest) {
            // 现代浏览器
            return new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            // IE 浏览器
            try {
                return new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    return new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {
                    return null;
                }
            }
        }
        return null;
    };
    
    // 跨浏览器创建 XML 文档对象
    this.createXMLDocument = function() {
        var xmlDoc = null;
        if (document.implementation && document.implementation.createDocument) {
            // 现代浏览器
            xmlDoc = document.implementation.createDocument("", "", null);
        } else if (window.ActiveXObject) {
            // IE 浏览器
            try {
                xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
            } catch (e) {
                xmlDoc = null;
            }
        }
        return xmlDoc;
    };
    
    try{this.xmlDoc=new ActiveXObject("Microsoft.XMLDOM")}catch(e){try{this.xmlDoc=document.implementation.createDocument("","",null)}catch(e){return null}}try{this.xmlDoc.async=false;this.xmlDoc.load(a)}catch(e){return null}};Chart.prototype.getParameters=function(a){var b=this.xmlDoc.documentElement;var d=b.getElementsByTagName("chart");var e=false;for(var i=0;i<d.length;i++){var c=d[i];if(c.getAttribute("id")==a){e=true;this.root=c;break}}if(!e)return null;var f=new Array();var g=this.root.childNodes;f['id']=this.root.getAttribute("id");f['type']=this.root.getAttribute("type");f['name']=this.root.getAttribute("name");f['team']=this.root.getAttribute("team");f['team_name']=this.root.getAttribute("team_name");for(var i=0;i<g.length;i++){var p=g[i];if(p.tagName!='logs'&&p.firstChild!=null)f[p.tagName]=p.firstChild.data}return f};