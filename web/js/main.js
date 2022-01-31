function mergePackages() {
	let files = document.getElementById('inputXMLFiles').files;

	if (files.length > 0) {
		let formData = new FormData();
		let validInput = false;

		for (let i = 0; i < files.length; i++) {
			if (files[i].name.endsWith('.xml')) {
				formData.append('file[]', files[i]);
				validInput = true;
			}
		}

		if (validInput) {
			let xhr = new XMLHttpRequest();
			xhr.open('POST', 'php/service/MergeXMLPackages.php', true);
			xhr.onload = function(event) {
				if (xhr.status == 200) {
					document.getElementById('outputXML').value = this.responseText;
				} else {
					// TODO error handling
					document.getElementById('outputXML').value = '';
				}
			}
			
			xhr.send(formData);
		}
	}
}

function copyToClipboard() {
	let copyText = document.getElementById('outputXML');
	copyText.select();
	copyText.setSelectionRange(0, 99999); /* For mobile devices */

	navigator.clipboard.writeText(copyText.value);
}

function download() {
	let data = document.getElementById('outputXML').value; 

	if (data.length > 0) {
		const textToBLOB = new Blob([data], { type: 'text/xml' });
		const sFileName = 'package.xml';

		let newLink = document.createElement("a");
        newLink.download = sFileName;

		if (window.webkitURL != null) {
            newLink.href = window.webkitURL.createObjectURL(textToBLOB);
        }
        else {
            newLink.href = window.URL.createObjectURL(textToBLOB);
            newLink.style.display = "none";
            document.body.appendChild(newLink);
        }

        newLink.click(); 
	}
}

function clearForm() {
	document.getElementById('inputXMLFiles').value = '';
	document.getElementById('outputXML').value = '';
}

function clickConvertOption() {
	let convertForm = document.getElementById('convertForm');
	let selectedOption = convertForm.elements['convertOption'].value;

	if (selectedOption == 'to') {
		// To
		convertForm.elements['inputFile'].accept = '.xml';
	}
	else {
		// From
		convertForm.elements['inputFile'].accept = '.xlsx';
	}
}

function convertPackage() {
	document.getElementById('convertForm').submit();
}
