<?php
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PackageXMLUtil {	

	private $metadataArr;
	private $apiVersion;

	public function __construct() {
		$this->metadataArr = array();
		$this->apiVersion = 0;
	}

	public function addPackage($packageXML) {
		// Iterates <types>
		foreach ($packageXML->types as $type) {
			if (!empty($type->name)) {
				$typeName = trim($type->name); // e.g. ApexClass

				if (!isset($this->metadataArr[$typeName])) {
					$this->metadataArr[$typeName] = array();
				}

				// Iterates <members>
				foreach($type->members as $member) {
					$memberValue = trim($member);

					if (!in_array($memberValue, $this->metadataArr[$typeName])) {
						array_push($this->metadataArr[$typeName], $memberValue);
					}
				}

				natcasesort($this->metadataArr[$typeName]);
			}
		}

		if (isset($packageXML->version)) {
			$version = intval($packageXML->version);
			
			if ($version > $this->apiVersion) {
				$this->apiVersion = $version;
			}
		}

		ksort($this->metadataArr);
	}

	public function constructPackage() {
		if (!empty($this->metadataArr)) {
			$xmlEl = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
				<Package xmlns="http://soap.sforce.com/2006/04/metadata"></Package>');

			foreach ($this->metadataArr as $typeName => $members) {
				$typeNode = $xmlEl->addChild('types');
				$membersNode;
				
				foreach($members as $memberName) {
					$membersNode = $typeNode->addChild('members', $memberName);
				}

				$membersNode = $typeNode->addChild('name', $typeName);
			}

			$xmlEl->addChild('version', $this->apiVersion . '.0');
			return $xmlEl;
		}

		return null;
	}

	public function toExcel() {
		if (!empty($this->metadataArr)) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'Api Name');
			$sheet->setCellValue('B1', 'Metadata Type');
			$sheet->getStyle('A1')->getFont()->setBold(true);
			$sheet->getStyle('B1')->getFont()->setBold(true);

			$rowNumber = 2;

			foreach ($this->metadataArr as $typeName => $members) {
				foreach($members as $memberName) {
					$sheet->setCellValue('A' . $rowNumber, $memberName);
					$sheet->setCellValue('B' . $rowNumber, $typeName);
					$rowNumber++;
				}
			}

			foreach ($sheet->getColumnIterator() as $column) {
				$sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
			}

			return new Xlsx($spreadsheet);
		}

		return null;
	}

	public function fromExcel($inputFileName) {
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
		$sheet = $spreadsheet->getActiveSheet();

		if ($this->apiVersion == 0) {
			$this->apiVersion = 53;
		}
		
		for ($rowNumber = 2; $rowNumber <= $sheet->getHighestRow(); $rowNumber++) {
			$apiName = '' . $sheet->getCell('A' . $rowNumber);
			$metadataType = '' . $sheet->getCell('B' . $rowNumber);

			if (!isset($this->metadataArr[$metadataType])) {
				$this->metadataArr[$metadataType] = array();
			}

			if (!in_array($apiName, $this->metadataArr[$metadataType])) {
				array_push($this->metadataArr[$metadataType], $apiName);
			}
		}

		return $this->constructPackage();
	}

}
?>