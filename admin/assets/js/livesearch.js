function createRequestObject(){
var request_o;
var browser = navigator.appName;
if(browser == "Microsoft Internet Explorer"){
request_o = new ActiveXObject("Microsoft.XMLHTTP");
}else{
request_o = new XMLHttpRequest();
}
return request_o;
}

var http = createRequestObject(); 

function klijentPretraga()
{
	var url = "livesearch.php?mode=klijenti";
	var s = document.getElementById('qsearch').value;
	var params = "&s="+s;
	http.open("POST", url, true);

	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.setRequestHeader("Content-length", params.length);
	http.setRequestHeader("Connection", "close");

	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status != 200) {
		document.getElementById('searchResults').innerHTML='<li>Loading...</li>';
		}
		if(http.readyState == 4 && http.status == 200) {
		document.getElementById('searchResults').innerHTML = http.responseText; 
		} 
	}
	http.send(params);
}

function serverPretraga()
{
	var url2 = "livesearch.php?mode=serveri";
	var s = document.getElementById('qsearch2').value;
	var params = "&s="+s;
	http.open("POST", url2, true);

	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.setRequestHeader("Content-length", params.length);
	http.setRequestHeader("Connection", "close");

	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status != 200) {
		document.getElementById('searchResults2').innerHTML='<li>Loading...</li>';
		}
		if(http.readyState == 4 && http.status == 200) {
		document.getElementById('searchResults2').innerHTML = http.responseText; 
		} 
	}
	http.send(params);
}

function sendToSearch(str){
	document.getElementById('qsearch').value = str;
	document.getElementById('searchResults').innerHTML = "";
	$("#pretragac").submit()
}

function sendToSearch2(str){
	document.getElementById('qsearch2').value = str;
	document.getElementById('searchResults2').innerHTML = "";
	$("#pretragac2").submit()
}

if ($("#qsearch").is(":focus")) {

}else{
	$("ul#searchResults").fadeOut("fast");
}

$(document).ready(function() {    

	$('#qsearch').blur(function(){
		$("ul#searchResults").fadeOut("fast");
	})
	.focus(function() {		
		$("ul#searchResults").fadeIn("fast");
	});
	
	$('#qsearch2').blur(function(){
		$("ul#searchResults2").fadeOut("fast");
	})
	.focus(function() {		
		$("ul#searchResults2").fadeIn("fast");
	});	
});