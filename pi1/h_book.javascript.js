function insert(textarea,string) {
	var elem = document.getElementById(textarea);
	elem.value += string;
}

function insertLink(textarea) {
	var linkURL = prompt("Link-URL:");
	var linkText = prompt("Link-Text:");
	var code = "";
	
	     if(linkURL.length == 0)  code = "";
	else if(linkText.length == 0) code = "[url]" + linkURL + "[/url]";
	else                          code = "[url=" + linkURL + "]" + linkText + "[/url]";
	
	insert(textarea, code);
}