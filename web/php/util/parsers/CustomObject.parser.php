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
					case 'recordTypes':
						return 'RecordType';
						break;
					case 'validationRules':
						return 'ValidationRule';
						break;
				}
			} else if (count($explodeArr) < 3 && str_ends_with($metadataStr, '/')) {
				return 'CustomObject';
			}
		}

		return ParserInterface::UNSUPPORTED;
	}

	public function getMemberValue($typeName, $metadataStr) {
		if ($typeName != ParserInterface::UNSUPPORTED && str_contains($metadataStr, '/')) {
			$explodeArr = explode('/', $metadataStr);

			if (count($explodeArr) == 3) {
				return $explodeArr[0] . '.' . explode('.', $explodeArr[2], 2)[0];
			} else if (count($explodeArr) < 3 && str_ends_with($metadataStr, '/')) {
				return $explodeArr[0];
			}
		}
		
		return $metadataStr;
	}
}
?>