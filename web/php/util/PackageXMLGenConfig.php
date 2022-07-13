<?php
require 'parsers/ParserInterface.php';

foreach (glob('parsers/*.parser.php') as $classFile) {
    include $classFile;
}

class PackageXMLGenConfig {

	public static function getConfig() {
		// Generics
		$configArr = array(
			'applications' => array('TypeName' => 'CustomApplication'),
			'classes' => array('TypeName' => 'ApexClass'),
			'customMetadata' => array('TypeName' => 'CustomMetadata'),
			'customPermissions' => array('TypeName' => 'CustomPermission'),
			'flows' => array('TypeName' => 'Flow'),
			'globalValueSets' => array('TypeName' => 'GlobalValueSet'),
			'layouts' => array('TypeName' => 'Layout'),
			'permissionsets' => array('TypeName' => 'PermissionSet')
		);
		
		// Dinamically loads custom parsers
		foreach (get_declared_classes() as $className) {
			if (in_array('ParserInterface', class_implements($className))) {
				$parser = new $className();
				$configArr[$parser->getFolderName()] = array('CustomParser' => $parser);
        	}
        }

        return $configArr;
	}
}
?>