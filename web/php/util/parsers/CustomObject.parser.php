<?php
class CustomObject implements ParserInterface {

	public function getFolderName() {
		return 'objects';
	}

	public function getTypeName($metadataStr) {
		if (str_contains($metadataStr, '/')) {
			$explodeArr = explode('/', $metadataStr);

			if (count($explodeArr) == 3) {
				switch ($explodeArr[1]) {
					case 'fields':
						return 'CustomField';
						break;
				}
			}
		}

		return ParserInterface::UNSUPPORTED;
	}

	public function getMemberValue($typeName, $metadataStr) {
		if ($typeName != ParserInterface::UNSUPPORTED && str_contains($metadataStr, '/')) {
			$explodeArr = explode('/', $metadataStr);

			if (count($explodeArr) == 3) {
				return $explodeArr[0] . '.' . explode('.', $explodeArr[2], 2)[0];
			}
		}
		
		return $metadataStr;
	}
}
?>