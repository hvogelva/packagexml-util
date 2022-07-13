<?php
interface ParserInterface {

	public const UNSUPPORTED = 'Unsupported';

	public function getFolderName();
	public function getTypeName($metadataStr);
	public function getMemberValue($typeName, $metadataStr);
}
?>