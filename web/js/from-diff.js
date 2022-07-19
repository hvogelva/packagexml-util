function resetSettings() {
	document.getElementById('rootFolder').value = 'force-app/main/default/';
	document.getElementById('showSupported').checked = true;
	document.getElementById('showIgnored').checked = false;
}

function clearText() {
	document.getElementById('diffText').value = '';
	document.getElementById('outputXML').value = '';
}

function generatePackage() {
	event.preventDefault();
	
	let xhr = new XMLHttpRequest();
	xhr.open('POST', 'php/service/GeneratePackage.php', true);

	xhr.onload = function(event) {
		if (xhr.status == 200) {
			document.getElementById('outputXML').value = this.responseText;
		} else {
			// TODO error handling
			document.getElementById('outputXML').value = '';
		}
	}
	
	xhr.send(new FormData(document.forms[0]));
}