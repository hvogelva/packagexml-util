<?php
include('../util/PackageXMLUtil.php');

if (isset($_POST['convertOption']) && isset($_FILES['inputFile'])) {
	$convertOption = $_POST['convertOption'];
	$extension = pathinfo($_FILES['inputFile']['name'], PATHINFO_EXTENSION);
	$tmpName = $_FILES['inputFile']['tmp_name'];
	
	$outputFileName = '';
	$conversion = false;

	if ($convertOption == 'to' && $extension == 'xml') {
		$packageXML = @simplexml_load_file($tmpName);

		if ($packageXML) {
			$packageXMLUtil = new PackageXMLUtil();
			$packageXMLUtil->addPackage($packageXML);
			$writter = $packageXMLUtil->toExcel();

			if ($writter) {
				// Save XLSX file
				$outputFileName = 'convertedPackage-' . (new \DateTime())->format('Ymd_His') . '.xlsx';
				$writter->save($outputFileName);
				$conversion = true;
			}
		}
	}
	else if ($convertOption == 'from' && $extension == 'xlsx') {
		$packageXMLUtil = new PackageXMLUtil();
		$xmlEl = @$packageXMLUtil->fromExcel($tmpName);

		if (isset($xmlEl)) {
			$xmlDocument = new DOMDocument('1.0');
			$xmlDocument->preserveWhiteSpace = false;
			$xmlDocument->formatOutput = true;
			$xmlDocument->loadXML($xmlEl->asXML());
			
			// Save XML file
			$outputFileName = 'convertedPackage-' . (new \DateTime())->format('Ymd_His') . '.xml';
			$fp = fopen($outputFileName, 'w');
			fwrite($fp, $xmlDocument->saveXML());
			fclose($fp);
			$conversion = true;
		}
	}

	if ($conversion) {
		$content = file_get_contents($outputFileName);
		header('Content-Disposition: attachment; filename=' . $outputFileName);
		unlink($outputFileName);
		exit($content);
	}

	header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>