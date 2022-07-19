<?php
include('../util/PackageXMLGenerator.php');

if (isset($_POST['diffText'])) {
	$diffStr = $_POST['diffText'];
	$options = array();
	$options['rootPath'] = isset($_POST['rootPath']) ? $_POST['rootPath'] : null;
	$options['showSupported'] = isset($_POST['showSupported']) && $_POST['showSupported'] == 'on' ? true : false;
	$options['showIgnored'] = isset($_POST['showIgnored']) && $_POST['showIgnored'] == 'on' ? true : false;
	$options['apiVersion'] = 53;
	
	$generator = new PackageXMLGenerator();
	$generator->generatePackage($diffStr, $options);

	if (!empty($generator->getMetadataArray())) {
		$packageXMLUtil = new PackageXMLUtil();
		$packageXMLUtil->setMetadataArray($generator->getMetadataArray());
		$packageXMLUtil->setApiVersion($options['apiVersion']);

		$xmlEl = $packageXMLUtil->constructPackage();
		$xmlDocument = new DOMDocument('1.0');
		$xmlDocument->preserveWhiteSpace = false;
		$xmlDocument->formatOutput = true;
		$xmlDocument->loadXML($xmlEl->asXML());
		echo $xmlDocument->saveXML();

		if ($options['showIgnored'] && !empty($generator->getIgnoredLines())) {
			echo '<!-- Ignored lines -->' . chr(13);
			echo '<!--' . chr(13);

			foreach ($generator->getIgnoredLines() as $line) {
				echo $line . chr(13);
			}

			echo '-->';
		}
	}
}
?>