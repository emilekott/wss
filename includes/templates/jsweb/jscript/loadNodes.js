/*
Copyright (c) 2006 Andrew Yermakov. All rights reserved.
version 0.9
*/
    function nodeData(id, path) {
	this.id = id;
	this.path = path;
    }

    function makeRequest(url, node, onCompleteCallback) {
	var http_request = false;
	
	if (window.XMLHttpRequest) {
	    http_request = new XMLHttpRequest();
	    if (http_request.overrideMimeType) {
		http_request.overrideMimeType('text/xml');
	    }
	    browser = 'mozilla';
	} else if (window.ActiveXObject) {
	    browser = 'IE';
	    try {
		http_request = new ActiveXObject("Msxml2.XMLHTTP");
	    } catch (e) {
		try {
		    http_request = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {}
	    }
	}

	if (!http_request) {
	    alert('Giving up :( Cannot create an XMLHTTP instance');
	}
	http_request.onreadystatechange = function() { processContents(http_request, node, onCompleteCallback); };
	http_request.open('GET', url, true);
	http_request.send(null);
    }
    
    function processContents(http_request, node, onCompleteCallback) {
	var tmpNode;
	if (http_request.readyState == 4) {
	    if (http_request.status == 200) {
		if (browser == 'IE') {
		    var xmldoc = new ActiveXObject("Msxml2.DOMDocument.3.0");
		    xmldoc.loadXML(http_request.responseText);
		} else {
		    var xmldoc = http_request.responseXML;
		}
		var root_node = xmldoc.getElementsByTagName('category');
		for (i = 0; i < root_node.length; i++) {
		    var id = root_node.item(i).getElementsByTagName('id').item(0).firstChild.data;
		    var childCount = root_node.item(i).getElementsByTagName('childcount').item(0).firstChild.data;
		    var productsCount = root_node.item(i).getElementsByTagName('productscount').item(0).firstChild.data;
		    var multiExpand = root_node.item(i).getElementsByTagName('multiexpand').item(0).firstChild.data;
		    var path = node.data.path + "_" + id;
		    
		    if (productsCount > 0) {
		        tmpNode = new YAHOO.widget.MenuNode({label: root_node.item(i).getElementsByTagName('name').item(0).firstChild.data, href: "index.php?main_page=index&cPath=" + path }, node, false);
		    } else {
			tmpNode = new YAHOO.widget.MenuNode({label: root_node.item(i).getElementsByTagName('name').item(0).firstChild.data }, node, false);
		    }
		    
		    if (multiExpand == 1) {
			tmpNode.multiExpand = true;
		    }
		    
		    tmpNode.data = new nodeData(id, path);
		    if (childCount > 0) {
			tmpNode.setDynamicLoad(loadDataForNode);
		    }
		}
		
		onCompleteCallback();
	    } else {
		alert('There was a problem with the request.');
	    }
	}
    }
