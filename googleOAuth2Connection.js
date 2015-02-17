//
"use strict";
/*
Connection
*/
// Class
function Connection(funct, url) {
	//funct - function to be called when data is returned
	//  funct must use external reference to object instead of this.data or this.function()
	//url - server side function to generate data to be returned to funct
// SuperClass Attributes

// Attributes
	this.connection = this.Connection(funct, url);

}
// Constructors
Connection.prototype.Connection = function (funct, url) {
	var httpRequest = false;
  	if (window.XMLHttpRequest) { // Mozilla, Safari, ...
		httpRequest = new XMLHttpRequest();
		if (httpRequest.overrideMimeType) {
			httpRequest.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!httpRequest) {
		alert('Giving up :( Cannot create an XMLHTTP instance');
		return false;
	}
	httpRequest.onreadystatechange = function () {
		if (httpRequest.readyState === 4) {
			if (httpRequest.status === 200) {
				var data = httpRequest.responseText;
				var json = '';
				data = replaceText(data, "\\\'", "'");
				clearStatus();
				try {
					json = JSON.parse(data);
				} catch(e) {
					statusMessage('JSON.parse() error: ' + e + '<br />' + data, 'badResults');
				}
				if( json !== undefined) {
					if(json.message !== undefined) {
						statusMessage(json.message, '');
					}
					if (json.error !== undefined) {
						statusMessage(json.error,'badResults');
					}
					if (json.session !== undefined) {
						sessionMessage(json.session);
					}
					if (json.request !== undefined) {
						requestMessage(json.request);
					}
				}
				if (funct) {   //this calls the function to process the data
					funct(json); //funct is passed when data is requested, this processes the results
				}
			}
		}
	};
	httpRequest.open('POST', url, true);
	httpRequest.send('');
};
function clearStatus() {
	var item = document.getElementById('status');
	item.innerHTML = '';
	item.className = 'empty';
};
function statusMessage(msg,cls) {
	var status=document.getElementById('status');
	status.innerHTML = status.innerHTML + msg + '<br />';
	cls = "box " + cls;
	status.className = cls;
};
function replaceText(source, find, replace) {
	var destination = '';
  	var found = source.indexOf(find);
  	while (found >= 0){
	    destination = destination + source.substring(0, found); //found not included
	    destination = destination + replace; //insert replacement string instead
	    source = source.substr(found + find.length); //jump over the find text and shorten remaining text to search
			found = source.indexOf(find);
	}
  	destination = destination + source;
  	return destination;
};
function sessionMessage(msg) {
	var status=document.getElementById('session');
	status.innerHTML = msg;
};
function requestMessage(msg) {
	var status=document.getElementById('request');
	status.innerHTML = msg;
};
