// Class Login
"use strict";
function Login(){
}
Login.prototype.getApiInfo = function(){
	this.clearApiInfo();
	var myCon = new Connection(this.displayApiInfo, 'googleOAuth2Example.php?command=getApiInfo');
}
Login.prototype.getAuthUrl = function(){
	this.clearAuthUrl();
	var myCon = new Connection(this.displayAuthUrl, 'googleOAuth2Example.php?command=getAuthUrl');
}
Login.prototype.getGClink = function(){
	this.clearLink();
	var myCon = new Connection(this.displayLink, 'googleOAuth2Example.php?command=getGClink');
}
Login.prototype.getAccessToken = function() {
	var	myCon = new Connection(this.displayAccessToken, 'googleOAuth2Example.php?command=getAccessToken');
}
Login.prototype.getScopes = function() {
	var	myCon = new Connection(this.displayScopes, 'googleOAuth2Example.php?command=getScopes');
}
Login.prototype.getUser = function() {
	this.clearUser();
	var	myCon = new Connection(this.displayUser, 'googleOAuth2Example.php?command=getUser');
}
Login.prototype.displayApiInfo = function (json){
	if (json.payload) {
		var output = document.getElementById('apiInfo');
		output.innerHTML = "";
	  var table = document.createElement('table');
	  output.appendChild(table);
	  table.setAttribute('id','apiInfo');
		table.setAttribute('class','box');
		var tr = document.createElement('tr');
	  table.appendChild(tr);
		var td = document.createElement('td');
		tr.appendChild(td);
		td.innerHTML = 'Client ID';
		var td = document.createElement('td');
		tr.appendChild(td);
		td.innerHTML = json.payload.clientID;
		var tr = document.createElement('tr');
	  table.appendChild(tr);
		var td = document.createElement('td');
		tr.appendChild(td);
		td.innerHTML = 'Client Secret';
		var td = document.createElement('td');
		tr.appendChild(td);
		td.innerHTML = json.payload.clientSecret;
		var tr = document.createElement('tr');
	  table.appendChild(tr);
		var td = document.createElement('td');
		tr.appendChild(td);
		td.innerHTML = 'Redirect URI';
		var td = document.createElement('td');
		tr.appendChild(td);
		td.innerHTML = decodeURIComponent(json.payload.redirectURI);
	  table.setAttribute('id','apiInfo');
	}
}
Login.prototype.displayAuthUrl = function (json){
	if (json.payload) {
		var url = decodeURIComponent(json.payload.authUrl);
		url = decodeURIComponent(url);
		var output = document.getElementById('authorization');
	  output.innerHTML = url;
	}
}
Login.prototype.displayLink = function (json){
	if (json.payload) {
		var link = decodeURIComponent(json.payload);
		var output = document.getElementById('link');
	  output.innerHTML = link;
	}
}
Login.prototype.displayAccessToken = function (json){
	if (json.payload) {
		var output = document.getElementById('accessToken');
		output.innerHTML = "";
	  var table = document.createElement('table');
	  output.appendChild(table);
	  table.setAttribute('id','accessTokenTable');
		table.setAttribute('class','box')
		var th = document.createElement('th');
		table.appendChild(th);
		th.setAttribute('style','text-align:left;');
		th.innerHTML = "Parameter";
		var th = document.createElement('th');
		table.appendChild(th);
		th.setAttribute('style','text-align:left;');
		th.innerHTML = "Value";
		var keyValuePair = "";
		for (keyValuePair in json.payload) {
			var tr = document.createElement('tr');
		  table.appendChild(tr);
			var td = document.createElement('td');
			tr.appendChild(td);
					td.innerHTML = keyValuePair;
			var td = document.createElement('td');
			tr.appendChild(td);
			td.innerHTML = json.payload[keyValuePair];
		}
	}
}
Login.prototype.displayScopes = function (json){
	if (json.payload.scopes) {
		var output = document.getElementById('scopes');
		output.innerHTML = "";
	  var table = document.createElement('table');
	  output.appendChild(table);
	  table.setAttribute('id','scopeTable');
		table.setAttribute('class','box');
		var th = document.createElement('th');
		table.appendChild(th);
		th.setAttribute('style','text-align:left;');
		th.innerHTML = "Scope List";
		var keyValuePair = "";
		for (keyValuePair in json.payload.scopes) {
			var tr = document.createElement('tr');
		  table.appendChild(tr);
			var td = document.createElement('td');
			tr.appendChild(td);
			td.innerHTML = json.payload.scopes[keyValuePair];
		}
	}
}
Login.prototype.displayUser = function (json){
	if (json.payload) {
		var output = document.getElementById('user');
		output.innerHTML = "";
	  var p = document.createElement('p');
	  output.appendChild(p);
		if ((json.payload['name'] == null) || (json.payload['name'] == "")) {
			statusMessage("No User Information","badResults");
		} else {
			var name = json.payload['name'];
			var email = json.payload['email'];
			var url = decodeURIComponent(json.payload['img_url']);
			p.innerHTML = "<h3>" + name + "</h3>" + "<img src='" + url + "alt='avatar' width=100><br />" + email
		}
	}
}
Login.prototype.clearApiInfo = function (){
	var output = document.getElementById('apiInfo');
	output.innerHTML = "";
}
Login.prototype.clearAuthUrl = function (){
	var output = document.getElementById('authorization');
	output.innerHTML = "";
}
Login.prototype.clearLink = function (){
	var output = document.getElementById('link');
	output.innerHTML = "";
}
Login.prototype.clearUser = function (){
	var output = document.getElementById('user');
	output.innerHTML = "";
}
