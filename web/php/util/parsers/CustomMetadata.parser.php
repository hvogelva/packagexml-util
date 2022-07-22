<?php
class CustomMetadata implements ParserInterface {

	public function getFolderName() {
		return 'customMetadata';
	}

	public function getTypeName($metadataStr) {
		return 'CustomMetadata';
	}

	public function getMemberValue($typeName, $metadataStr) {
		$explodeArr = explode('.', $metadataStr);
		
		if (count($explodeArr) > 1) {
			return $explodeArr[0] . '.' . $explodeArr[1];
		}
		
		return $metadataStr;
	}
}
?>