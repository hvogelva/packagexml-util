<?php
include('../util/PackageXMLUtil.php');

if (isset($_FILES['file']['name'])) {
	$packageXMLUtil = new PackageXMLUtil();

	foreach ($_FILES['file']['name'] as $key => $val) {
		$packageXML = @simplexml_load_file($_FILES['file']['tmp_name'][$key]);

		if (!$packageXML) {
			http_response_code(500);
			die('Error processing XML file ' . $val);
		}

		$packageXMLUtil->addPackage($packageXML);
	}
	
	$xmlEl = $packageXMLUtil->constructPackage();

	if (isset($xmlEl)) {
		$xmlDocument = new DOMDocument('1.0');
		$xmlDocument->preserveWhiteSpace = false;
		$xmlDocument->formatOutput = true;
		$xmlDocument->loadXML($xmlEl->asXML());
		echo $xmlDocument->saveXML();
	}
}
?>