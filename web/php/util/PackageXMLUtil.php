<?php
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

		//natcasesort($this->metadataArr);
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

}
?>