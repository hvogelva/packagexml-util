<?php
include('../util/PackageXMLUtil.php');

if (isset($_POST['convertOption']) && isset($_FILES['inputFile'])) {
	$convertOption = $_POST['convertOption'];
	$fileName = $_FILES['inputFile']['name'];
	$extension = pathinfo($fileName, PATHINFO_EXTENSION);

	if ($convertOption == 'to' && $extension == 'xml') {
		$packageXML = @simplexml_load_file($_FILES['inputFile']['tmp_name']);

		if ($packageXML) {
			$packageXMLUtil = new PackageXMLUtil();
			$packageXMLUtil->addPackage($packageXML);
			$writter = $packageXMLUtil->toExcel();

			if ($writter) {
				$excelFilename = 'convertedPackage-' . (new \DateTime())->format('Ymd_His') . '.xlsx';
				$writter->save($excelFilename);
				$content = file_get_contents($excelFilename);
				header('Content-Disposition: attachment; filename=' . $excelFilename);
				unlink($excelFilename);
				exit($content);
			}
		}
	}
	else if ($convertOption == 'from' && $extension == 'xlsx') {
		
	}

	header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>