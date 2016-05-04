$(function(){
	$(".commonBack").click(function(){
		history.go(-1);
	})
})
function upPackage(){
	$(".spark_content span").eq(0).addClass("active").click();
	$(".spark_up").hide();
	$(".spark_buy").show();
}
//写cookies
function setCookie(name,value,Time) {
	var a = arguments[2] ? arguments[2] : 1200;
	var exp = new Date();
	exp.setTime(exp.getTime() + a*1000);
	document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString() + ";path=/";
}
//读取cookies 
function getCookie(name) {
	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg))
		return unescape(arr[2]);
	else
		return null;
}
//删除cookies 
function delCookie(name) {
	var exp = new Date();
	exp.setTime(exp.getTime() - 1);
	var cval=getCookie(name);
	if(cval!=null)
		document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}